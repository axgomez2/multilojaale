<?php

namespace App\Http\Controllers\Site;

use App\Models\StoreInformation;
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
        
        return view('site.home', [
            'store' => $store
        ]);
    }
}
