<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\MusicianProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription as StripeSubscription;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    // Mostrar planes de suscripción
    public function index()
    {
        $user = Auth::user();
        $profile = $user->musicianProfile;
        
        if (!$profile) {
            return redirect()->route('dashboard')->with('error', 'Necesitas un perfil de artista para ver suscripciones.');
        }

        $currentSubscription = $profile->subscriptions()->active()->first();
        $plans = Subscription::getPlans();

        return view('subscriptions.index', compact('currentSubscription', 'plans'));
    }

    // Crear sesión de checkout de Stripe
    public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'plan_type' => 'required|in:basic,pro,premium'
        ]);

        $user = Auth::user();
        $profile = $user->musicianProfile;
        
        if (!$profile) {
            return response()->json(['error' => 'Perfil de artista no encontrado'], 404);
        }

        $plans = Subscription::getPlans();
        $plan = $plans[$request->plan_type];

        // Crear o obtener cliente de Stripe
        $customer = $this->getOrCreateStripeCustomer($user, $profile);

        // Crear sesión de checkout
        $session = \Stripe\Checkout\Session::create([
            'customer' => $customer->id,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => "Plan {$plan['name']} - Pulse Music",
                        'description' => implode(', ', $plan['features'])
                    ],
                    'unit_amount' => $plan['price'] * 100, // Convertir a centavos
                    'recurring' => [
                        'interval' => 'month',
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('subscriptions.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('subscriptions.cancel'),
            'metadata' => [
                'musician_profile_id' => $profile->id,
                'plan_type' => $request->plan_type,
            ],
        ]);

        return response()->json(['id' => $session->id]);
    }

    // Procesar pago exitoso
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        
        if (!$sessionId) {
            return redirect()->route('subscriptions.index')->with('error', 'Sesión no válida');
        }

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            $subscription = StripeSubscription::retrieve($session->subscription);

            $profileId = $session->metadata->musician_profile_id;
            $planType = $session->metadata->plan_type;
            
            $plans = Subscription::getPlans();
            $plan = $plans[$planType];

            // Stripe puede devolver current_period_end nulo en estados transitorios.
            // Usamos fallback a +30 días para evitar errores fatales y mantener consistencia.
            $periodEndTimestamp = $this->resolvePeriodEndTimestamp($subscription)
                ?? now()->addDays(30)->timestamp;

            $trialEndTimestamp = $subscription['trial_end'] ?? null;

            // Crear suscripción en base de datos
            Subscription::updateOrCreate([
                'stripe_subscription_id' => $subscription->id,
            ], [
                'musician_profile_id' => $profileId,
                'stripe_customer_id' => $subscription->customer,
                'plan_type' => $planType,
                'storage_gb' => $plan['storage_gb'],
                'price' => $plan['price'],
                'status' => $subscription->status,
                'starts_at' => now(),
                'ends_at' => \Carbon\Carbon::createFromTimestamp($periodEndTimestamp),
                'trial_ends_at' => $trialEndTimestamp ? \Carbon\Carbon::createFromTimestamp($trialEndTimestamp) : null,
                'next_billing_at' => \Carbon\Carbon::createFromTimestamp($periodEndTimestamp),
            ]);

            return redirect()->route('subscriptions.index')->with('success', '¡Suscripción activada correctamente!');
        } catch (\Exception $e) {
            return redirect()->route('subscriptions.index')->with('error', 'Error al procesar la suscripción: ' . $e->getMessage());
        }
    }

    // Cancelar suscripción
    public function cancel()
    {
        return redirect()->route('subscriptions.index')->with('info', 'El proceso de suscripción ha sido cancelado.');
    }

    // Cancelar suscripción existente
    public function destroy(Request $request)
    {
        $user = Auth::user();
        $profile = $user->musicianProfile;
        
        if (!$profile) {
            return back()->with('error', 'No tienes un perfil de músico.');
        }

        $subscription = $profile->subscriptions()->active()->first();
        
        if (!$subscription) {
            return back()->with('error', 'No tienes una suscripción activa.');
        }

        try {
            // Try to cancel in Stripe, but don't fail if Stripe is unavailable
            if ($subscription->stripe_subscription_id && config('services.stripe.secret')) {
                try {
                    $stripeSubscription = StripeSubscription::retrieve($subscription->stripe_subscription_id);
                    $stripeSubscription->cancel();

                    $periodEndTimestamp = $this->resolvePeriodEndTimestamp($stripeSubscription)
                        ?? now()->addDays(30)->timestamp;

                    $subscription->update([
                        'status' => 'canceled',
                        'ends_at' => \Carbon\Carbon::createFromTimestamp($periodEndTimestamp),
                    ]);
                } catch (\Exception $stripeError) {
                    // Stripe error (e.g. test mode, no real subscription) — cancel locally anyway
                    \Log::warning('Stripe cancel failed, canceling locally: ' . $stripeError->getMessage());
                    $subscription->update([
                        'status' => 'canceled',
                        'ends_at' => now(),
                    ]);
                }
            } else {
                // No Stripe subscription — cancel locally
                $subscription->update([
                    'status' => 'canceled',
                    'ends_at' => now(),
                ]);
            }

            return back()->with('success', 'Tu suscripción ha sido cancelada. Tu contenido (música, merchandising y conciertos) ya no será visible para otros usuarios.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cancelar la suscripción: ' . $e->getMessage());
        }
    }

    // Webhook de Stripe
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        // Procesar eventos
        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;
            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;
        }

        return response('Webhook processed', 200);
    }

    private function getOrCreateStripeCustomer($user, $profile)
    {
        // Buscar cliente existente
        if ($profile->stripe_customer_id) {
            try {
                return Customer::retrieve($profile->stripe_customer_id);
            } catch (\Exception $e) {
                // Cliente no encontrado, crear uno nuevo
            }
        }

        // Crear nuevo cliente
        $customer = Customer::create([
            'email' => $user->email,
            'name' => $profile->stage_name,
            'metadata' => [
                'musician_profile_id' => $profile->id,
                'user_id' => $user->id,
            ],
        ]);

        // Guardar ID del cliente
        $profile->update(['stripe_customer_id' => $customer->id]);

        return $customer;
    }

    private function handlePaymentSucceeded($invoice)
    {
        $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();
        if ($subscription) {
            $periodEnd = $invoice['period_end'] ?? null;
            $subscription->update([
                'status' => 'active',
                'next_billing_at' => $periodEnd
                    ? \Carbon\Carbon::createFromTimestamp($periodEnd)
                    : now()->addDays(30),
            ]);
        }
    }

    private function handlePaymentFailed($invoice)
    {
        $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();
        if ($subscription) {
            $subscription->update(['status' => 'past_due']);
        }
    }

    private function handleSubscriptionDeleted($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();
        if ($subscription) {
            $subscription->update([
                'status' => 'canceled',
                'ends_at' => now(),
            ]);
        }
    }

    private function resolvePeriodEndTimestamp($stripeSubscription): ?int
    {
        $currentPeriodEnd = $stripeSubscription['current_period_end'] ?? null;
        if ($currentPeriodEnd) {
            return (int) $currentPeriodEnd;
        }

        $itemPeriodEnd = $stripeSubscription['items']['data'][0]['current_period_end'] ?? null;
        if ($itemPeriodEnd) {
            return (int) $itemPeriodEnd;
        }

        return null;
    }
}
