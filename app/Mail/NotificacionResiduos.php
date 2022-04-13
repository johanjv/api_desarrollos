<?php

namespace App\Mail;

use App\Models\Residuos\ValidarMes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionResiduos extends Mailable
{
    use Queueable, SerializesModels;

    public $validarMes;

    public function __construct(ValidarMes $validarMes)
    {
        $this->validarMes = $validarMes;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Notificación de Residuos - RECHAZADO')->view('mails.notificacionResiduos');
    }
}
