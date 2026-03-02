<?php

namespace App\Mail;

use App\Models\Transactions;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DriverAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Transactions $transaction) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Trip Assigned: ' . $this->transaction->transaction_code,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.driver_assigned',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
