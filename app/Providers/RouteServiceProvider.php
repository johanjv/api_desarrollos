<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(function(){
                require base_path('routes/api.php');
                require base_path('routes/hvsedes/hvsedes.php');
                require base_path('routes/factucontrol/factucontrol.php');
                require base_path('routes/citologias/citologias.php');
                require base_path('routes/mamitas/mamitas.php');
                require base_path('routes/GestionResiduos/GestionResiduos.php');
                require base_path('routes/viaticos/viaticos.php');
                require base_path('routes/FirmaDigital/FirmaDigital.php');

                /* RUTAS VERSION 2 */
                require base_path('routes/v2/auth/auth.php');

                /* AJUSTAR A RUTAS V2 */

                require base_path('routes/vacunacion/vacunacion.php');
                require base_path('routes/escalas/escalas.php');
                require base_path('routes/consentimientos/consentimientos.php');
                require base_path('routes/consentimientos/consentimientos.php');
                require base_path('routes/gestionpacientes/gestionpacientes.php');
                require base_path('routes/lineaEtica/lineaEtica.php');

                /* RUTAS VERSION 3 */
                require base_path('routes/v3/auth/auth.php');
                require base_path('routes/VideoConsulta/VideoConsulta.php');

            });
    }
}
