<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Event;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            if ($secret) {
                $event = Webhook::constructEvent($payload, $sigHeader, $secret);
            } else {
                // En desarrollo sin webhook secret, parsear directamente
                $event = Event::constructFrom(json_decode($payload, true));
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // Single-item flow
            $orderId = $session->metadata->order_id ?? null;
            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    OrderService::markPaid($order);
                }
            }

            // Cart flow (multiple orders)
            $orderIds = $session->metadata->order_ids ?? null;
            if ($orderIds) {
                $ids = array_map('intval', explode(',', $orderIds));
                Order::whereIn('id', $ids)->get()->each(function ($order) {
                    OrderService::markPaid($order);
                });
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
