<?php

namespace App\Providers;

use App\Models\VinylMaster;
use App\ViewModels\VinylCardViewModel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewModelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Registra um compositor que transforma VinylMaster em VinylCardViewModel
        View::composer('components.site.vinyl-card', function ($view) {
            $data = $view->getData();
            
            if (isset($data['vinyl']) && $data['vinyl'] instanceof VinylMaster) {
                $view->with('viewModel', new VinylCardViewModel($data['vinyl']));
            }
        });
    }
}
