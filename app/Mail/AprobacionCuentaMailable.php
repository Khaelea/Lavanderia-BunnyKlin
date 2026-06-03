<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AprobacionCuentaMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    // Recibimos al usuario y el token de seguridad
    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    // El asunto del correo que te llegará
    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Solicitud de Aprobación de Cuenta - CRM');
    }

    // La vista HTML que armamos en el paso anterior
    public function content(): Content
    {
        return new Content(view: 'emails.aprobacion-cuenta');
    }
}