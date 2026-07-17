<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public Transaction $transaction;

    /**
     * Create a new message instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('E-Ticket Resmi Anda: ' . ($this->transaction->event->title ?? 'Event'))
            ->view('emails.ticket')
            ->with(['transaction' => $this->transaction]);
    }
}
