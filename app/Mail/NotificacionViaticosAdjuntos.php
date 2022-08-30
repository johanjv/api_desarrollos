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
    public $rt2;
    public $datosTabla;
    public $totalRecorridos;
    public $totalViaticos;

    public function __construct($rt2, $datosTabla, $totalRecorridos, $totalViaticos)
    {
        $this->rt2             = $rt2;
        $this->datosTabla      = $datosTabla;
        $this->totalRecorridos = $totalRecorridos;
        $this->totalViaticos   = $totalViaticos;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        foreach ($this->rt2 as $r) {
            $this->from('desarrollovs@virreysolisips.com.co', 'Gestión de Viaje')
            ->subject('Notificación de Viaje-Programación')
            ->view('mailsViaticos.notificacionViaticosAdjuntos')->attach(public_path($r));
        }
    }
}
