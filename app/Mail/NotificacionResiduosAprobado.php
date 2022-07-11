<?php

namespace App\Mail;

use App\Models\Residuos\ValidarMes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionResiduosAprobado extends Mailable
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
        return $this->from('desarrollovs@virreysolisips.com.co', 'Gestión de Residuos Hospitalarios')
            ->subject('Notificación de Residuos - APROBADO')
        ->view('mails.notificacionResiduosAprobado');
    }
}
