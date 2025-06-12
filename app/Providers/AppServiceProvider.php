<?php

namespace App\Providers;

use App\Providers\StoreServiceProvider;
use App\Providers\ValidatorServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar o StoreServiceProvider
        $this->app->register(StoreServiceProvider::class);
        
        // Registrar o ValidatorServiceProvider para validações customizadas
        $this->app->register(ValidatorServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
