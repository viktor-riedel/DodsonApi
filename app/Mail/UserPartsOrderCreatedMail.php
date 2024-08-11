<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserPartsOrderCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Order $order;

    public function __construct(User $user, Order $order)
    {
        $this->user = $user;
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'User Parts Order Created',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-parts-order-created',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
