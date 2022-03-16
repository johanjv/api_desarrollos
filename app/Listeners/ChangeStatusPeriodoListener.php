<?php

namespace App\Listeners;

use App\Events\ChangeStatusPeriodoEvent;
use App\Models\Residuos\ValidarMes;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ChangeStatusPeriodoListener
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
     * @param  \App\Events\ChangeStatusPeriodoEvent  $event
     * @return void
     */
    public function handle(ChangeStatusPeriodoEvent $event)
    {
        return ValidarMes::all();
    }
}
