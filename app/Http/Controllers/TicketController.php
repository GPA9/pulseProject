<?php

namespace App\Http\Controllers;

use App\Mail\TicketMail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    /**
     * Render a printable HTML ticket for a single order.
     */
    public function download(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'paid') {
            abort(404, 'El ticket solo está disponible para pedidos completados.');
        }

        $orders = collect([$order]);
        return view('ticket', compact('orders', 'order'));
    }

    /**
     * Render a printable HTML ticket for multiple orders (cart checkout).
     */
    public function downloadMultiple(Request $request)
    {
        $ids = array_filter(array_map('intval', explode(',', $request->query('order_ids', ''))));

        if (empty($ids)) {
            abort(404);
        }

        $orders = Order::whereIn('id', $ids)
            ->where('user_id', Auth::id())
            ->where('status', 'paid')
            ->get();

        if ($orders->isEmpty()) {
            abort(404, 'No se encontraron pedidos completados.');
        }

        $order = $orders->first();
        return view('ticket', compact('orders', 'order'));
    }

    /**
     * Send the ticket by email for a single order.
     * DISABLED: Users can only download/print tickets
     */
    // public function sendEmail(Order $order)
    // {
    //     if ($order->user_id !== Auth::id()) {
    //         abort(403);
    //     }

    //     if ($order->status !== 'paid') {
    //         return back()->with('ticket_error', 'Solo puedes enviar tickets de pedidos completados.');
    //     }

    //     try {
    //         Mail::to(Auth::user()->email)->send(new TicketMail($order));
    //         return back()->with('ticket_sent', '¡Ticket enviado a ' . Auth::user()->email . '!');
    //     } catch (\Exception $e) {
    //         return back()->with('ticket_error', 'No se pudo enviar el email: ' . $e->getMessage());
    //     }
    // }

    /**
     * Send the ticket by email for multiple orders (cart).
     * DISABLED: Users can only download/print tickets
     */
    // public function sendEmailMultiple(Request $request)
    // {
    //     $ids = array_filter(array_map('intval', explode(',', $request->query('order_ids', ''))));

    //     if (empty($ids)) {
    //         abort(404);
    //     }

    //     $orders = Order::whereIn('id', $ids)
    //         ->where('user_id', Auth::id())
    //         ->where('status', 'paid')
    //         ->get();

    //     if ($orders->isEmpty()) {
    //         return back()->with('ticket_error', 'No se encontraron pedidos completados.');
    //     }

    //     try {
    //         Mail::to(Auth::user()->email)->send(new TicketMail($orders));
    //         return back()->with('ticket_sent', '¡Ticket enviado a ' . Auth::user()->email . '!');
    //     } catch (\Exception $e) {
    //         return back()->with('ticket_error', 'No se pudo enviar el email: ' . $e->getMessage());
    //     }
    // }
}
