<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use App\Models\Merch;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class CheckoutController extends Controller
{
    // ── Configurar Stripe ────────────────────────────────────────────
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    // ── 1. Página de resumen pre-pago (ítem individual) ──────────────
    public function show($type, $id)
    {
        $item = $this->findItem($type, $id);
        return view('checkout.show', compact('item', 'type'));
    }

    // ── 2. Crear sesión Stripe (ítem individual con cantidad) ─────────
    public function createStripeSession(Request $request)
    {
        $request->validate([
            'item_type' => 'required|in:concert,merch',
            'item_id'   => 'required|integer',
            'quantity'  => 'required|integer|min:1|max:20',
            'size'      => 'nullable|string|max:10',
        ]);

        $type = $request->item_type;
        $id   = (int) $request->item_id;
        $qty  = (int) $request->quantity;
        $size = $type === 'merch' ? trim($request->size ?? '') : '';
        $item = $this->findItem($type, $id);

        // Validate concert capacity
        if ($type === 'concert' && $item->capacity_available !== null && $qty > $item->capacity_available) {
            return back()->withErrors(['quantity' => "Solo quedan {$item->capacity_available} entradas disponibles."]);
        }

        $unitPrice        = (float) $item->price;
        $totalAmount      = $unitPrice * $qty;
        $commission       = $totalAmount * 0.05;
        $musicianEarnings = $totalAmount * 0.95;
        $name             = $this->getItemName($type, $item);
        if ($size !== '') {
            $name .= ' (Talla: ' . $size . ')';
        }

        $order = Order::create([
            'user_id'          => Auth::id(),
            'item_type'        => $type,
            'item_id'          => $id,
            'item_name'        => $name,
            'quantity'         => $qty,
            'amount'           => $totalAmount,
            'commission'       => $commission,
            'musician_earnings'=> $musicianEarnings,
            'status'           => 'pending',
        ]);

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'eur',
                    'unit_amount'  => (int) round($unitPrice * 100),
                    'product_data' => [
                        'name'        => $name,
                        'description' => $this->getItemDescription($type, $item),
                    ],
                ],
                'quantity' => $qty,
            ]],
            'mode'        => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order->id,
            'cancel_url'  => route('checkout.cancel') . '?order_id=' . $order->id,
            'metadata'    => [
                'order_id'  => $order->id,
                'item_type' => $type,
                'item_id'   => $id,
                'user_id'   => Auth::id(),
            ],
        ]);

        $order->update(['stripe_session_id' => $session->id]);

        return redirect($session->url);
    }

    // ── 3. Checkout del carrito (múltiples ítems) ─────────────────────
    public function cartCheckout(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('home')->with('cart_error', 'Tu carrito está vacío.');
        }

        // Create a pending Order per cart item
        $orders     = [];
        $lineItems  = [];

        foreach ($cart as $ci) {
            $type  = $ci['type'];
            $id    = (int) $ci['id'];
            $qty   = (int) $ci['quantity'];
            $price = (float) $ci['price'];
            $name  = $ci['name'];

            $total   = $price * $qty;
            $comm    = $total * 0.05;
            $earn    = $total * 0.95;

            $order = Order::create([
                'user_id'          => Auth::id(),
                'item_type'        => $type,
                'item_id'          => $id,
                'item_name'        => $name,
                'quantity'         => $qty,
                'amount'           => $total,
                'commission'       => $comm,
                'musician_earnings'=> $earn,
                'status'           => 'pending',
            ]);

            $orders[] = $order->id;

            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'eur',
                    'unit_amount'  => (int) round($price * 100),
                    'product_data' => ['name' => $name],
                ],
                'quantity' => $qty,
            ];
        }

        $orderIdsStr = implode(',', $orders);

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items'  => $lineItems,
            'mode'        => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&order_ids=' . $orderIdsStr,
            'cancel_url'  => route('checkout.cancel') . '?order_ids=' . $orderIdsStr,
            'metadata'    => ['order_ids' => $orderIdsStr, 'user_id' => Auth::id()],
        ]);

        // Attach session ID to all orders and clear cart
        Order::whereIn('id', $orders)->update(['stripe_session_id' => $session->id]);
        session()->forget('cart');

        return redirect($session->url);
    }

    // ── 4. Página de éxito ────────────────────────────────────────────
    public function success(Request $request)
    {
        $order  = null;
        $orders = [];

        if ($request->session_id) {
            try {
                $stripeSession = StripeSession::retrieve($request->session_id);

                if ($stripeSession->payment_status === 'paid') {
                    // Single-item flow
                    if ($request->order_id) {
                        $order = Order::find($request->order_id);
                        if ($order) {
                            OrderService::markPaid($order);
                        }
                        $orders = [$order];
                    }

                    // Cart flow (multiple orders)
                    if ($request->order_ids) {
                        $ids = array_map('intval', explode(',', $request->order_ids));
                        $orders = Order::whereIn('id', $ids)->get()->each(function ($o) {
                            OrderService::markPaid($o);
                        })->all();
                        $order = $orders[0] ?? null; // for backwards compat
                    }
                }
            } catch (\Exception $e) {
                // Stripe session inválida
            }
        } elseif ($request->order_id) {
            $order  = Order::find($request->order_id);
            $orders = [$order];
        } elseif ($request->order_ids) {
            $ids    = array_map('intval', explode(',', $request->order_ids));
            $orders = Order::whereIn('id', $ids)->get()->all();
            $order  = $orders[0] ?? null;
        }

        return view('checkout.success', compact('order', 'orders'));
    }

    // ── 5. Cancelación ────────────────────────────────────────────────
    public function cancel(Request $request)
    {
        $ids = [];
        if ($request->order_id)  $ids = [(int) $request->order_id];
        if ($request->order_ids) $ids = array_map('intval', explode(',', $request->order_ids));

        if ($ids) {
            Order::whereIn('id', $ids)->where('status', 'pending')
                ->update(['status' => 'cancelled']);
        }

        return view('checkout.cancel');
    }

    // markOrderPaid logic moved to App\Services\OrderService::markPaid()

    // ── Helpers ───────────────────────────────────────────────────────
    private function findItem(string $type, int $id)
    {
        return match ($type) {
            'concert' => Concert::with('musicianProfile')->findOrFail($id),
            'merch'   => Merch::with('musicianProfile')->findOrFail($id),
            default   => abort(404),
        };
    }

    private function getItemName(string $type, $item): string
    {
        return match ($type) {
            'concert' => 'Entrada: ' . ($item->musicianProfile->stage_name ?? '') . ' en ' . $item->venue,
            'merch'   => $item->name . ' — ' . ($item->musicianProfile->stage_name ?? ''),
            default   => 'Artículo',
        };
    }

    private function getItemDescription(string $type, $item): string
    {
        return match ($type) {
            'concert' => $item->venue . ', ' . $item->city . ' · ' . $item->date->format('d/m/Y H:i'),
            'merch'   => $item->description ?? '',
            default   => '',
        };
    }
}
