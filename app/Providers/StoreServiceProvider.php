<?php

namespace App\Providers;

use App\Services\StoreService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class StoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(StoreService::class, function ($app) {
            return new StoreService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Compartilhar a instância de StoreService com todas as views
        View::composer('*', function ($view) {
            $view->with('storeService', app(StoreService::class));
        });
        
        // Disponibilizar helper para o componente admin-layout
        $this->loadViewComponentsAs('', [
            \App\View\Components\AdminLayout::class,
        ]);
    }
}
