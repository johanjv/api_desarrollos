<?php

namespace App\Mail;

/* use App\Models\Residuos\ValidarMes; */
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class mailConsentimiento extends Mailable
{
    use Queueable, SerializesModels;

    public $datosAfiliado;
    public $consentimiento;

    public function __construct($datosAfiliado, $consentimiento)
    {
        $this->datosAfiliado  = $datosAfiliado;
        $this->consentimiento = $consentimiento;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('desarrollovs@virreysolisips.com.co', 'Consentimiento Informado')
            ->subject('Notificación de Consentimiento Informado - RECIBIDO')
        ->view('mailsConsentimientos.mailLink');
    }
}
