<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CitaReservadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cita;

    public function __construct(Appointment $cita)
    {
        $this->cita = $cita;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cita reservada correctamente - AppSalon',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cita-reservada',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}