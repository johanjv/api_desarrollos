<?php

namespace App\Mail;

use App\Models\Residuos\ValidarMes;
use App\Models\Viaticos\Rechazo;
use App\Models\Viaticos\RegistroSolicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionViaticosRechazo extends Mailable
{
    use Queueable, SerializesModels;

    public $RegistroSolicitud;
    public $RegistroSolicitudObs;
    public $datos;

    public function __construct(RegistroSolicitud $RegistroSolicitud, $RegistroSolicitudObs, $datos)
    {
        $this->RegistroSolicitud    = $RegistroSolicitud;
        $this->RegistroSolicitudObs = $RegistroSolicitudObs;
        $this->datos                = $datos;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('desarrollovs@virreysolisips.com.co', 'Gestión de Viáticos')
            ->subject('Notificación de Viáticos - RECHAZADO')
        ->view('mailsViaticos.notificacionViaticosRechazo');
    }
}
