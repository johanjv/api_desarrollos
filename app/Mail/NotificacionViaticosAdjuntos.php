<?php

namespace App\Mail;

use App\Models\Residuos\ValidarMes;
use App\Models\Viaticos\Rechazo;
use App\Models\Viaticos\RegistroSolicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionViaticosAdjuntos extends Mailable
{
    use Queueable, SerializesModels;

    public $RegistroSolicitud;
    public $RegistroSolicitudObs;
    public $datos;

    public function __construct($datos)
    {
        $this->RegistroSolicitud    = null;
        $this->RegistroSolicitudObs = null;
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
            ->subject('Notificación de Viaje-Programación')
        ->view('mailsViaticos.notificacionViaticosAdjuntos')->attach(public_path($this->datos));
    }
}
