<?php

namespace App\Services;

use App\Models\Concert;
use App\Models\Merch;
use App\Models\Order;

class OrderService
{
    /**
     * Mark an order as paid and apply all side-effects:
     *  - Decrement concert capacity_available
     *  - Increment merch sales_count
     */
    public static function markPaid(Order $order): void
    {
        if ($order->status === 'paid') {
            return; // idempotent: skip if already processed
        }

        $order->update(['status' => 'paid']);

        $qty = max(1, (int) $order->quantity);

        // Decrement concert capacity
        if ($order->item_type === 'concert') {
            $concert = Concert::find($order->item_id);
            if ($concert && $concert->capacity_available !== null && $concert->capacity_available > 0) {
                $newAvail = max(0, $concert->capacity_available - $qty);
                $concert->update(['capacity_available' => $newAvail]);
            }
        }

        // Increment merch sales_count
        if ($order->item_type === 'merch') {
            $merch = Merch::find($order->item_id);
            if ($merch) {
                $merch->increment('sales_count', $qty);
            }
        }
    }
}
