<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmarCuentaMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;
    public $password; // <-- Agregamos esta variable

    // Modificamos el constructor para recibir la contraseña
    public function __construct(User $user, $token, $password)
    {
        $this->user = $user;
        $this->token = $token;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: '¡Tus accesos para BunnyKlin!');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.confirmar-cuenta');
    }
}