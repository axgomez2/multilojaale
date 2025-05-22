<?php

namespace App\Http\Controllers\Site;

use App\Models\StoreInformation;
use App\Models\VinylMaster;
use App\Models\VinylSec;
use App\Models\CatStyleShop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Display the home page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obter as informações da loja
        $store = StoreInformation::getInstance();
        
        // Buscar os discos em destaque
        $featuredVinyls = $this->getFeaturedVinyls();
        
        // Buscar categorias principais com seus discos
        $categories = $this->getCategoriesWithVinyls();
        
        // Buscar os lançamentos mais recentes
        $latestVinyls = VinylMaster::with([
            'vinylSec', 
            'vinylSec.midiaStatus', 
            'vinylSec.coverStatus', 
            'vinylSec.supplier',
            'artists',
            'tracks',
            'recordLabel'
        ])
        ->whereHas('vinylSec', function($query) {
            $query->where('in_stock', true)
                  ->where('price', '>', 0);
        })
        ->latest() // Mais recentes primeiro
        ->take(10) // Limite de 10 registros para o carrossel
        ->get();
        
        // Para cada vinil, adicionamos um atributo indicando que está disponível
        $latestVinyls->each(function($vinyl) {
            $vinyl->is_available = true;
        });
        
        return view('site.home', [
            'store' => $store,
            'latestVinyls' => $latestVinyls,
            'featuredVinyls' => $featuredVinyls,
            'categories' => $categories
        ]);
    }
    
    /**
     * Obter vinis da categoria "destaque"
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFeaturedVinyls()
    {
        // Buscar a categoria com o nome "destaque"
        $featuredCategory = CatStyleShop::where('nome', 'destaque')
            ->orWhere('nome', 'Destaque')
            ->first();
            
        // Se a categoria existir, buscar os vinis associados
        if ($featuredCategory) {
            $vinyls = $featuredCategory->vinylMasters()
                ->with([
                    'vinylSec', 
                    'vinylSec.midiaStatus', 
                    'vinylSec.coverStatus', 
                    'vinylSec.supplier',
                    'artists',
                    'tracks',
                    'recordLabel'
                ])
                ->whereHas('vinylSec', function($query) {
                    $query->where('in_stock', true)
                          ->where('price', '>', 0);
                })
                ->latest()
                ->take(20) // Limitado a 20 discos
                ->get();
                
            // Marcar todos como disponíveis já que filtramos por isso
            $vinyls->each(function($vinyl) {
                $vinyl->is_available = true;
            });
            
            return $vinyls;
        }
        
        // Se a categoria não existir, retornar uma coleção vazia
        return collect([]);
    }
    
    /**
     * Buscar categorias principais com seus vinis
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getCategoriesWithVinyls()
    {
        // Buscar categorias que tenham discos disponíveis (excluindo a categoria 'destaque')
        $categories = CatStyleShop::whereHas('vinylMasters', function($query) {
                // Apenas discos disponíveis
                $query->whereHas('vinylSec', function($q) {
                    $q->where('in_stock', true)
                      ->where('price', '>', 0);
                });
            })
            ->where('nome', '!=', 'destaque')
            ->where('nome', '!=', 'Destaque')
            ->orderBy('nome')
            ->take(5) // Limitado a 5 categorias principais
            ->get();
        
        // Para cada categoria, buscar os 10 discos mais recentes
        return $categories->map(function($category) {
            // Garantir que o slug é uma string
            if (is_object($category->slug) && $category->slug instanceof \Closure) {
                // Se por algum motivo slug é uma closure, vamos gerar um slug a partir do nome
                $category->slug = \Illuminate\Support\Str::slug($category->nome);
            }
            
            // Buscar os discos desta categoria
            $vinyls = $category->vinylMasters()
                ->with([
                    'vinylSec', 
                    'vinylSec.midiaStatus', 
                    'vinylSec.coverStatus', 
                    'vinylSec.supplier',
                    'artists',
                    'tracks',
                    'recordLabel'
                ])
                ->whereHas('vinylSec', function($query) {
                    $query->where('in_stock', true)
                          ->where('price', '>', 0);
                })
                ->latest()
                ->take(10) // Limitado a 10 discos por carrossel
                ->get();
            
            // Marcar todos como disponíveis
            $vinyls->each(function($vinyl) {
                $vinyl->is_available = true;
            });
            
            // Retornar a categoria com seus discos
            return [
                'category' => $category,
                'vinyls' => $vinyls
            ];
        })->filter(function($item) {
            // Filtrar apenas categorias que tenham discos
            return $item['vinyls']->isNotEmpty();
        });
    }
    
    /**
     * Display all products page
     *
     * @return \Illuminate\View\View
     */
    public function allProducts()
    {
        // Obter as informações da loja
        $store = StoreInformation::getInstance();
        
        // Buscar todos os vinis disponíveis
        $vinyls = VinylMaster::with([
            'vinylSec', 
            'vinylSec.midiaStatus', 
            'vinylSec.coverStatus', 
            'vinylSec.supplier',
            'artists',
            'tracks',
            'recordLabel'
        ])
        ->whereHas('vinylSec', function($query) {
            $query->where('in_stock', true)
                  ->where('price', '>', 0);
        })
        ->latest()
        ->paginate(20);
        
        // Para cada vinil, adicionamos um atributo indicando se está disponível
        $vinyls->each(function($vinyl) {
            $vinyl->is_available = true; // Já filtramos apenas os disponíveis
        });
        
        return view('site.products', [
            'store' => $store,
            'vinyls' => $vinyls,
            'title' => 'Todos os Discos de Vinil',
            'description' => 'Confira nossa coleção completa de discos de vinil.'
        ]);
    }
    
    /**
     * Display products by category
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function category($slug)
    {
        // Obter a categoria pelo slug
        $category = CatStyleShop::where('slug', $slug)->firstOrFail();
        
        // Obter as informações da loja
        $store = StoreInformation::getInstance();
        
        // Buscar todos os vinis relacionados com esta categoria
        $vinyls = $category->vinylMasters()
            ->with([
                'vinylSec', 
                'vinylSec.midiaStatus', 
                'vinylSec.coverStatus', 
                'vinylSec.supplier',
                'artists',
                'tracks',
                'recordLabel'
            ])
            ->whereHas('vinylSec', function($query) {
                $query->where('in_stock', true)
                      ->where('price', '>', 0);
            })
            ->latest()
            ->paginate(20);
            
        // Para cada vinil, adicionamos um atributo indicando se está disponível
        $vinyls->each(function($vinyl) {
            $vinyl->is_available = true; // Já filtramos apenas os disponíveis
        });
        
        return view('site.products', [
            'store' => $store,
            'vinyls' => $vinyls,
            'title' => "Categoria: {$category->nome}",
            'description' => "Discos de vinil da categoria {$category->nome}.",
            'category' => $category
        ]);
    }
}
