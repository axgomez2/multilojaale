<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MelhorEnvio\Resources\Shipment;
use MelhorEnvio\Enums\Environment;

class MelhorEnvioServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('melhorenvio', function ($app) {
            $environment = config('app.env') === 'production' 
                ? Environment::PRODUCTION 
                : Environment::SANDBOX;
                
            $shipment = new Shipment();
            $shipment->setEnvironment($environment);
            $shipment->setToken(config('services.melhorenvio.token'));
            
            return $shipment;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
