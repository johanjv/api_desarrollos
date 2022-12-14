<?php

namespace App\Mail;

use App\Models\Residuos\ValidarMes;
use App\Models\Viaticos\RegistroSolicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionViaticosAprobado extends Mailable
{
    use Queueable, SerializesModels;

    public $RegistroSolicitud;
    public $datos;

    public function __construct(RegistroSolicitud $RegistroSolicitud, $datos)
    {
        $this->RegistroSolicitud    = $RegistroSolicitud;
        $this->datos                = $datos;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('desarrollovs@virreysolisips.com.co', 'Gestión de Viaje')
            ->subject('Notificación de viaje ' . '#' . $this->RegistroSolicitud->idSolicitud . ' ' . '- APROBADO')
            ->view('mailsViaticos.notificacionViaticosAprobado');
    }
}
