<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VinylMaster;
use App\Models\Artist;

class VinylDetailsController extends Controller
{
    /**
     * Exibe os detalhes de um disco de vinil
     *
     * @param string $artistSlug Slug do artista
     * @param string $titleSlug Slug do título do disco
     * @return \Illuminate\View\View
     */
    public function show(string $artistSlug, string $titleSlug)
    {
        // Buscar o artista pelo slug
        $artist = Artist::where(function($query) use ($artistSlug) {
            $query->whereRaw("LOWER(REPLACE(name, ' ', '-')) = ?", [strtolower($artistSlug)])
                  ->orWhere('slug', $artistSlug);
        })->first();
        
        if (!$artist) {
            abort(404, 'Artista não encontrado');
        }
        
        // Buscar o disco pelo slug do título e pelo artista
        $vinyl = VinylMaster::with([
                'vinylSec', 
                'vinylSec.midiaStatus', 
                'vinylSec.coverStatus', 
                'vinylSec.supplier',
                'artists',
                'tracks' => function($query) {
                    $query->orderBy('position', 'asc');
                },
                'recordLabel',
                'categories',
            ])
            ->whereHas('artists', function($query) use ($artist) {
                $query->where('artist_id', $artist->id);
            })
            ->where(function($query) use ($titleSlug) {
                $query->whereRaw("LOWER(REPLACE(title, ' ', '-')) = ?", [strtolower($titleSlug)])
                      ->orWhere('slug', $titleSlug);
            })
            ->first();
            
        if (!$vinyl) {
            abort(404, 'Disco não encontrado');
        }
        
        // Verificar disponibilidade
        $isAvailable = false;
        if ($vinyl->vinylSec && $vinyl->vinylSec->in_stock && $vinyl->vinylSec->price > 0) {
            $isAvailable = true;
        }
        $vinyl->is_available = $isAvailable;
        
        // Buscar discos similares (mesma categoria ou mesmo artista)
        $similarVinyls = VinylMaster::with(['vinylSec', 'artists'])
            ->whereHas('vinylSec', function($query) {
                $query->where('in_stock', true)
                      ->where('price', '>', 0);
            })
            ->where('id', '!=', $vinyl->id)
            ->where(function($query) use ($vinyl) {
                // Mesmo artista
                $query->whereHas('artists', function($q) use ($vinyl) {
                    $q->whereIn('artist_id', $vinyl->artists->pluck('id')->toArray());
                });
                // Ou mesma categoria
                if ($vinyl->categories->count() > 0) {
                    $query->orWhereHas('categories', function($q) use ($vinyl) {
                        $q->whereIn('cat_style_shop_id', $vinyl->categories->pluck('id')->toArray());
                    });
                }
            })
            ->inRandomOrder()
            ->take(4)
            ->get();
            
        // Marcar discos similares como disponíveis
        $similarVinyls->each(function($vinyl) {
            $vinyl->is_available = true;
        });
        
        return view('site.vinyl.show', [
            'vinyl' => $vinyl,
            'similarVinyls' => $similarVinyls,
        ]);
    }
}
