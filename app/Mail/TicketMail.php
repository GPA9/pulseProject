<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $orders;
    public float $grandTotal;

    /**
     * @param Order|Collection $orders  Single Order or collection of Orders
     */
    public function __construct($orders)
    {
        $this->orders     = $orders instanceof Collection ? $orders : collect([$orders]);
        $this->grandTotal = $this->orders->sum('amount');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎵 Tu ticket de compra — Pulse',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.ticket',
        );
    }
}
