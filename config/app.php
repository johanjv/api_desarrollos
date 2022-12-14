<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    //'timezone' => 'UTC',
    'timezone' => 'America/Bogota',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Variables de entorno personalizadas para los Desarrollos
    |--------------------------------------------------------------------------
    |
    | Estas son las variables definidas para cada desarrollo y asi poder
    | obtener los modulos correspondientes a cada auno.
    |
    */

    'admGlobal'       => env('APP_ADMINGLOBAL', 1),
    'hvSedes'         => env('APP_HVSEDES', 25),
    'factuControl'    => env('APP_FACTUCONTROL', 10024),
    'citologias'      => env('APP_CITOLOGIAS', 10025),
    'mamitas'         => env('APP_MAMITAS', 10031),
    'residuos'        => env('APP_RESIDUOS', 10034),
    'firma'           => env('APP_FIRMA', 10037),
    'viaticos'        => env('APP_VIATICOS', 10035),
    'escalas'         => env('APP_ESCALAS', 10039),
    'vacunacion'      => env('APP_VACUNACION', 10044),
    'consentimientos' => env('APP_CONSENTIMIENTOS', 10047),
    'agenda'          => env('APP_AGENDA', 10050),
    'lineaetica'      => env('APP_LINEAETICA', 10052),


    /*
    |--------------------------------------------------------------------------
    | Variables de entorno personalizadas para los Desarrollos VERSION 2
    |--------------------------------------------------------------------------
    |
    | Estas son las variables definidas para cada desarrollo y asi poder
    | obtener los modulos correspondientes a cada auno.
    |
    */

    'aplicativos' => [
        'lineaEtica' => [
            'id'            => 10052,
            'estrategia'    => 'EstrategiaLineaEtica',
        ],
        'PruebaApp' => [
            'id'            => 10054,
            'estrategia'    => 'EstrategiaPruebaApp',
        ],
        'videoConsulta' => [
            'id'            => 10055,
            'estrategia'    => 'EstrategiavideoConsulta',
        ],
    ],

    'ultimoPermiso' => 6,


    /*
    |--------------------------------------------------------------------------
    | Grupos disponibles HVSEDES
    |--------------------------------------------------------------------------
    |
    | Estos son los grupos disponoibles desde LDAP
    | obtener los modulos correspondientes a cada auno.
    |
    */

    'superAdmin'    => env('APP_SUPERADMIN', 1),
    'administrador' => env('APP_ADMINISTRADOR', 2),
    'hvConsultor'   => env('APP_CONSULTOR', 3),
    'hvSupervisor'  => env('APP_SUPERVISOR', 3),
    'hvAdmServHab'  => env('APP_ADM_SERV_HAB', 5),
    'hvAdmInfra'    => env('APP_ADM_INFRA', 6),
    'HvTalentoHumo' => env('APP_CONS_TH', 7),
    'hvAdmTH'       => env('APP_ADM_TH', 8),

    /*
    |--------------------------------------------------------------------------
    | Grupos disponibles Factucontrol
    |--------------------------------------------------------------------------
    |
    | Estos son los grupos disponoibles desde LDAP
    | obtener los modulos correspondientes a cada auno.
    |
    */

    'superAdmin'       => env('APP_SUPERADMIN', 1),
    'administrador'    => env('APP_ADMINISTRADOR', 2),
    'RadicadorFactu'   => env('APP_RADICADORFACTU', 9),
    'CoordinadorFactu' => env('APP_COORDINADORFACTU', 10),
    'TesoreriaFactu'   => env('APP_TESORERIAFACTU', 11),
    'Atencion'         => env('APP_ATENCION', 12),
    'AdminFac'         => env('APP_ADMINFAC', 14),

    /*
    |--------------------------------------------------------------------------
    | Grupos disponibles Vi??ticos
    |--------------------------------------------------------------------------
    |
    | Estos son los grupos disponoibles desde LDAP
    | obtener los modulos correspondientes a cada auno.
    |
    */

    'superAdmin'        => env('APP_SUPERADMIN', 1),
    'administrador'     => env('APP_ADMINISTRADOR', 2),
    'APD_ViaticosAdmin' => env('APP_ADMINUSER', 20),


    /*
    |--------------------------------------------------------------------------
    | Grupos disponibles Citolog??as
    |--------------------------------------------------------------------------
    |
    | Estos son los grupos disponoibles desde LDAP
    | obtener los modulos correspondientes a cada auno.
    |
    */


    'ProfCitologias' => env('APP_PROF_CITOLOGIAS', 13),


    /*
    |--------------------------------------------------------------------------
    | Grupos disponibles Mamitas Seguras
    |--------------------------------------------------------------------------
    |
    | Estos son los grupos disponoibles desde LDAP
    | obtener los modulos correspondientes a cada auno.
    |
    */
    'Mamitas2_0' => env('APP_MAMITAS_USERS', 15),


    /*
    |--------------------------------------------------------------------------
    | Grupos disponibles GESTI??N DE RESIDUOS HOSPITALARIOS
    |--------------------------------------------------------------------------
    |
    | Estos son los grupos disponoibles desde LDAP
    | obtener los modulos correspondientes a cada auno.
    |
    */

    'SupAdmResiduos' => env('APP_SUPADMRESIDUOS', 16),
    'AdmResiduos' => env('APP_ADMRESIDUOS', 17),
    'UsersResiduos' => env('APP_USERSRESIDUOS', 18),


    /*
    |--------------------------------------------------------------------------
    | Grupos disponibles FIRMA DIGITAL
    |--------------------------------------------------------------------------
    |
    | Estos son los grupos disponoibles desde LDAP
    | obtener los modulos correspondientes a cada auno.
    |
    */

    'userAdmFirma' => env('APP_USERADMFIRMA', 19),


    /*
    |--------------------------------------------------------------------------
    | Grupos disponibles ESCALAS DE REHABILITACION
    |--------------------------------------------------------------------------
    |
    | Estos son los grupos disponoibles desde LDAP
    | obtener los modulos correspondientes a cada auno.
    |
    */

    'UsersEscalas' => env('APP_USERSESCALAS', 21),
    'AdminEscalas' => env('APP_ADMINESCALAS', 22),


    /*
    |--------------------------------------------------------------------------
    | Grupos disponibles GESTI??N DE PACIENTES
    |--------------------------------------------------------------------------
    |
    | Estos son los grupos disponoibles desde LDAP
    | obtener los modulos correspondientes a cada auno.
    |
    */

    'adminGP' => env('APP_ADMINGP', 25),

    /*
    |--------------------------------------------------------------------------
    | Grupos disponibles CONSENTIMIENTOS INFORMADOS
    |--------------------------------------------------------------------------
    |
    | Estos son los grupos disponoibles desde LDAP
    | obtener los modulos correspondientes a cada auno.
    |
    */

    'usersConsentimientos' => env('APP_USERSCONSENTIMIENTOS', 30),

    /*
    |--------------------------------------------------------------------------
    | Grupos disponibles LINEA ETICA
    |--------------------------------------------------------------------------
    |
    | Estos son los grupos disponoibles desde LDAP
    | obtener los modulos correspondientes a cada auno.
    |
    */

    'usersLineaetica' => env('APP_USERSLINEAETICA', 40),




    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,
        Barryvdh\DomPDF\ServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'PDF' => Barryvdh\DomPDF\Facade::class,

    ],

];
