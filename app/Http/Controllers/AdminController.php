<?php

namespace App\Http\Controllers;

use App\Models\MusicianProfile;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    // Panel principal de administración
    public function index()
    {
        // Estadísticas generales
        $stats = [
            'total_musicians' => MusicianProfile::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'total_revenue' => Subscription::where('status', 'active')->sum('price'),
            'new_musicians_this_month' => MusicianProfile::whereMonth('created_at', now()->month)->count(),
        ];

        // Suscripciones por plan
        $subscriptionsByPlan = Subscription::where('status', 'active')
            ->selectRaw('plan_type, COUNT(*) as count, SUM(price) as revenue')
            ->groupBy('plan_type')
            ->get();

        // Últimas suscripciones
        $recentSubscriptions = Subscription::with('musicianProfile')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Suscripciones que expiran pronto
        $expiringSoon = Subscription::where('status', 'active')
            ->where('ends_at', '>', now())
            ->where('ends_at', '<=', now()->addDays(7))
            ->with('musicianProfile')
            ->get();

        return view('admin.dashboard', compact('stats', 'subscriptionsByPlan', 'recentSubscriptions', 'expiringSoon'));
    }

    // Lista de todos los músicos
    public function musicians(Request $request)
    {
        $query = MusicianProfile::with('user', 'subscriptions');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('stage_name', 'like', "%{$search}%")
                  ->orWhere('genre', 'like', "%{$search}%")
                  ->orWhereHas('user', function($subQ) use ($search) {
                      $subQ->where('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('subscription_status')) {
            $status = $request->subscription_status;
            if ($status === 'active') {
                $query->whereHas('subscriptions', function($q) {
                    $q->where('status', 'active');
                });
            } elseif ($status === 'none') {
                $query->whereDoesntHave('subscriptions');
            }
        }

        if ($request->filled('community')) {
            $query->where('autonomous_community', $request->community);
        }

        $musicians = $query->orderBy('created_at', 'desc')->paginate(20);
        $communities = MusicianProfile::distinct()->pluck('autonomous_community')->filter()->sort()->values();

        return view('admin.musicians', compact('musicians', 'communities'));
    }

    // Detalles de un músico específico
    public function musicianDetail(MusicianProfile $musician)
    {
        $musician->load(['user', 'subscriptions', 'songs', 'concerts', 'merch']);
        
        // Estadísticas del músico
        $stats = [
            'total_songs' => $musician->songs->count(),
            'total_concerts' => $musician->concerts->count(),
            'total_merch' => $musician->merch->count(),
            'total_plays' => $musician->songs->sum('play_count'),
            'concert_revenue' => $musician->concerts->sum(function($c) {
                $sold = $c->capacity ? ($c->capacity - ($c->capacity_available ?? $c->capacity)) : 0;
                return $sold * $c->price * 0.95;
            }),
            'merch_revenue' => $musician->merch->sum(function($m) {
                return $m->sales_count * $m->price * 0.95;
            }),
        ];

        return view('admin.musician-detail', compact('musician', 'stats'));
    }

    // Lista de suscripciones
    public function subscriptions(Request $request)
    {
        $query = Subscription::with('musicianProfile.user');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan_type')) {
            $query->where('plan_type', $request->plan_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('musicianProfile', function($q) use ($search) {
                $q->where('stage_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($subQ) use ($search) {
                      $subQ->where('email', 'like', "%{$search}%");
                  });
            });
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Estadísticas de suscripciones
        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', 'active')->count(),
            'canceled' => Subscription::where('status', 'canceled')->count(),
            'past_due' => Subscription::where('status', 'past_due')->count(),
            'monthly_revenue' => Subscription::where('status', 'active')->sum('price'),
        ];

        return view('admin.subscriptions', compact('subscriptions', 'stats'));
    }

    // Detalles de suscripción
    public function subscriptionDetail(Subscription $subscription)
    {
        $subscription->load('musicianProfile.user');
        
        // Historial de pagos (simulado - en producción vendría de Stripe)
        $paymentHistory = [
            ['date' => $subscription->created_at, 'amount' => $subscription->price, 'status' => 'success'],
            // Aquí irían los pagos reales de Stripe
        ];

        return view('admin.subscription-detail', compact('subscription', 'paymentHistory'));
    }

    // Cancelar suscripción (admin)
    public function cancelSubscription(Subscription $subscription)
    {
        try {
            if ($subscription->stripe_subscription_id && config('services.stripe.secret')) {
                try {
                    $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);
                    $stripeSubscription->cancel();
                    $periodEnd = $stripeSubscription['current_period_end'] ?? now()->timestamp;
                    $subscription->update([
                        'status' => 'canceled',
                        'ends_at' => \Carbon\Carbon::createFromTimestamp($periodEnd),
                    ]);
                } catch (\Exception $stripeError) {
                    \Log::warning('Admin: Stripe cancel failed, canceling locally: ' . $stripeError->getMessage());
                    $subscription->update(['status' => 'canceled', 'ends_at' => now()]);
                }
            } else {
                $subscription->update(['status' => 'canceled', 'ends_at' => now()]);
            }
            return back()->with('success', 'Suscripción cancelada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cancelar la suscripción: ' . $e->getMessage());
        }
    }

    // Reactivar suscripción (admin)
    public function reactivateSubscription(Subscription $subscription)
    {
        try {
            if ($subscription->stripe_subscription_id && config('services.stripe.secret')) {
                try {
                    $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);
                    $stripeSubscription->resume();
                    $periodEnd = $stripeSubscription['current_period_end'] ?? now()->addDays(30)->timestamp;
                    $subscription->update([
                        'status' => 'active',
                        'ends_at' => \Carbon\Carbon::createFromTimestamp($periodEnd),
                    ]);
                } catch (\Exception $stripeError) {
                    \Log::warning('Admin: Stripe reactivate failed, reactivating locally: ' . $stripeError->getMessage());
                    $subscription->update(['status' => 'active', 'ends_at' => now()->addDays(30)]);
                }
            } else {
                $subscription->update(['status' => 'active', 'ends_at' => now()->addDays(30)]);
            }
            return back()->with('success', 'Suscripción reactivada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al reactivar la suscripción: ' . $e->getMessage());
        }
    }

    // API para obtener estadísticas en tiempo real
    public function apiStats()
    {
        return response()->json([
            'musicians' => MusicianProfile::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'monthly_revenue' => Subscription::where('status', 'active')->sum('price'),
            'new_this_month' => MusicianProfile::whereMonth('created_at', now()->month)->count(),
        ]);
    }

    // Eliminar canción (admin)
    public function deleteSong($id)
    {
        $song = \App\Models\Song::findOrFail($id);
        $song->delete();
        return back()->with('success', 'Canción eliminada correctamente.');
    }

    // Eliminar concierto (admin)
    public function deleteConcert($id)
    {
        $concert = \App\Models\Concert::findOrFail($id);
        $concert->delete();
        return back()->with('success', 'Concierto eliminado correctamente.');
    }

    // Eliminar merch (admin)
    public function deleteMerch($id)
    {
        $merch = \App\Models\Merch::findOrFail($id);
        $merch->delete();
        return back()->with('success', 'Producto de merchandising eliminado correctamente.');
    }

    // Eliminar perfil de músico (admin)
    public function deleteMusician($id)
    {
        $musician = MusicianProfile::findOrFail($id);
        $musician->delete();
        return redirect()->route('admin.musicians')->with('success', 'Perfil de músico eliminado correctamente.');
    }
}
