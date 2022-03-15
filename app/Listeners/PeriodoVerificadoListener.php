<?php

namespace App\Listeners;

use App\Events\PeriodoVerificadoEvent;
use App\Models\Residuos\ValidarMes;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PeriodoVerificadoListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\PeriodoVerificadoEvent  $event
     * @return void
     */
    public function handle(PeriodoVerificadoEvent $event)
    {
        return ValidarMes::all();
    }
}
