<?php

namespace App\Http\Controllers\Site;

use App\Models\StoreInformation;
use App\Models\VinylMaster;
use App\Models\VinylSec;
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
        
        // Buscar todos os vinis cadastrados, incluindo os incompletos
        $vinyls = VinylMaster::with([
            'vinylSec', 
            'vinylSec.midiaStatus', 
            'vinylSec.coverStatus', 
            'vinylSec.supplier',
            'artists',
            'tracks',
            'recordLabel'
        ])
        ->latest() // Mais recentes primeiro
        ->take(15) // Limite de 15 registros para não sobrecarregar
        ->get();
        
        // Para cada vinil, adicionamos um atributo indicando se está disponível
        $vinyls->each(function($vinyl) {
            // Um vinil está disponível se tiver informações completas e estiver em estoque
            $vinyl->is_available = $vinyl->vinylSec && 
                                  $vinyl->vinylSec->in_stock && 
                                  $vinyl->vinylSec->price > 0;
        });
        
        return view('site.home', [
            'store' => $store,
            'vinyls' => $vinyls
        ]);
    }
}
