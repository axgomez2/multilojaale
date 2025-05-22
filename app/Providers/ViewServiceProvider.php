<?php

namespace App\Providers;

use App\Models\CatStyleShop;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ViewServiceProvider extends ServiceProvider
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
        // Compartilha as categorias com todas as views do site
        View::composer('components.site.navbar2', function ($view) {
            // Desativa o cache temporariamente para debug
            Cache::forget('site_categories');
            
            // Busca as categorias diretamente da tabela pivot
            $categories = Cache::remember('site_categories', 1800, function () {
                // Query simples e direta: pegar APENAS categorias que têm vinis associados
                // usando uma consulta SQL pura para garantir o resultado correto
                $categoryIds = DB::table('cat_style_shop_vinyl_master')
                    ->select('cat_style_shop_id')
                    ->distinct()
                    ->pluck('cat_style_shop_id')
                    ->toArray();
                
                // Busca as categorias com esses IDs
                return CatStyleShop::whereIn('id', $categoryIds)
                    ->orderBy('nome')
                    ->get();
            });
            
            // Log para debugging
            \Illuminate\Support\Facades\Log::info('Categorias no menu: ' . $categories->count());
            
            $view->with('categories', $categories);
        });
    }
}
