<?php

namespace App\Mail;

use App\Models\Residuos\ValidarMes;
use App\Models\Viaticos\Rechazo;
use App\Models\Viaticos\RegistroSolicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionViaticosCopiaCorreo extends Mailable
{
    use Queueable, SerializesModels;
    public $rt2;
    public $datosTabla;
    public $datosCopiaCorreo;

    public function __construct($rt2, $datosTabla, $datosCopiaCorreo)
    {
        $this->rt2              = $rt2;
        $this->datosTabla       = $datosTabla;
        $this->datosCopiaCorreo = $datosCopiaCorreo;
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
                ->view('mailsViaticos.notificacionViaticosCopiaCorreo')->attach(public_path($r));
        }
    }
}
