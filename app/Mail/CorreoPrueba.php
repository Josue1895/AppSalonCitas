<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CorreoPrueba extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Prueba de Correo - AppSalon',
        );
    }

    public function content(): Content
{
    return new Content(
        view: 'emails.prueba',
    );
}

    public function attachments(): array
    {
        return [];
    }
}