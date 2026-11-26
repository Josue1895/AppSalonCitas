<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EstadoCitaMail extends Mailable
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
            subject: 'Actualización de estado de cita - AppSalon',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.estado-cita',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}