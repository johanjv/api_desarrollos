<?php

namespace App\Mail;

use App\Models\Residuos\ValidarMes;
use App\Models\Viaticos\RegistroSolicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionViaticosRegistro extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $nombreDirectivo;
    public $departamentos;
    public $datosColaborador;

    public function __construct(RegistroSolicitud $data, $nombreDirectivo, $departamentos, $datosColaborador)
    {
        $this->data             = $data;
        $this->nombreDirectivo  = $nombreDirectivo;
        $this->departamentos    = $departamentos;
        $this->datosColaborador = $datosColaborador;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('desarrollovs@virreysolisips.com.co', 'Gestión de Viaje')
            ->subject('Notificación de viaje ' . '#' . $this->data->idSolicitud)
            ->view('mailsViaticos.notificacionViaticosRegistro');
    }
}
