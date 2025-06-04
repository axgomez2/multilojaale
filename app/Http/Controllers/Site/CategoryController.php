<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\CatStyleShop;
use App\Models\StoreInformation;
use App\Models\VinylMaster;
use App\Models\Artist;
use App\Models\RecordLabel;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display products by category
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
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
                'recordLabel',
                'media'
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

    /**
     * Display all products
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
            'recordLabel',
            'media'
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
     * Display products by artist
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function byArtist($slug)
    {
        // Obter o artista pelo slug
        $artist = Artist::where('slug', $slug)->firstOrFail();
        
        // Obter as informações da loja
        $store = StoreInformation::getInstance();
        
        // Buscar todos os vinis relacionados com este artista
        $vinyls = $artist->vinylMasters()
            ->with([
                'vinylSec', 
                'vinylSec.midiaStatus', 
                'vinylSec.coverStatus', 
                'vinylSec.supplier',
                'artists',
                'tracks',
                'recordLabel',
                'media'
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
            'title' => "Discos de {$artist->name}",
            'description' => "Todos os discos de vinil de {$artist->name} disponíveis em nossa loja.",
            'artist' => $artist
        ]);
    }
    
    /**
     * Display products by record label
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function byLabel($slug)
    {
        // Obter a gravadora pelo slug
        $label = RecordLabel::where('slug', $slug)->firstOrFail();
        
        // Obter as informações da loja
        $store = StoreInformation::getInstance();
        
        // Buscar todos os vinis relacionados com esta gravadora
        $vinyls = VinylMaster::where('record_label_id', $label->id)
            ->with([
                'vinylSec', 
                'vinylSec.midiaStatus', 
                'vinylSec.coverStatus', 
                'vinylSec.supplier',
                'artists',
                'tracks',
                'recordLabel',
                'media'
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
            'title' => "Discos da gravadora {$label->name}",
            'description' => "Todos os discos de vinil da gravadora {$label->name} disponíveis em nossa loja.",
            'label' => $label
        ]);
    }
}
