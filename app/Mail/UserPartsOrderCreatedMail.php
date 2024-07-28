<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class UserPartsOrderCreatedMail extends Mailable{
    use Queueable, SerializesModels;

    public function __construct(//|)
    {
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
