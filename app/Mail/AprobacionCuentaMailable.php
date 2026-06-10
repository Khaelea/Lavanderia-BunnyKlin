<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AprobacionCuentaMailable extends Mailable
{
    use Queueable, SerializesModels;

    // Cambiamos el nombre de la variable a $user para que la vista deje de marcar error
    public $user; 
    public $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        return $this->view('emails.aprobacion-cuenta')
                    ->subject('Aprobación de cuenta pendiente');
    }
}