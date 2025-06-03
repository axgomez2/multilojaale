<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VinylMaster;
use App\Models\Artist;
use App\Models\Wishlist;
use App\Models\Wantlist;
use Illuminate\Support\Facades\Auth;

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
        
        // Verificar wishlist e wantlist para o usuário logado
        $wishlistItems = [];
        $wantlistItems = [];
        
        if (Auth::check()) {
            // Obter os IDs dos itens na wishlist e wantlist do usuário
            $userId = Auth::id();
            $wishlistItems = Wishlist::where('user_id', $userId)
                ->pluck('vinyl_master_id')
                ->toArray();
                
            $wantlistItems = Wantlist::where('user_id', $userId)
                ->pluck('vinyl_master_id')
                ->toArray();
        }
        
        // Verificar se o vinil principal está na wishlist ou wantlist
        $inWishlist = in_array($vinyl->id, $wishlistItems);
        $inWantlist = in_array($vinyl->id, $wantlistItems);
        
        // Define SEO specific data for this page
        $artist = $vinyl->artists->first();
        $artistName = $artist ? $artist->name : '';
        
        $title = $vinyl->title . ' - ' . $artistName;
        $description = 'Disco ' . $vinyl->title . ' de ' . $artistName;
        if (!empty($vinyl->description)) {
            $description .= '. ' . substr($vinyl->description, 0, 150);
            if (strlen($vinyl->description) > 150) {
                $description .= '...';
            }
        }
        
        $keywords = 'vinil, disco, ' . $artistName . ', ' . $vinyl->title . ', música';
        if ($vinyl->categories && $vinyl->categories->count() > 0) {
            $keywords .= ', ' . $vinyl->categories->pluck('nome')->implode(', ');
        }
        
        $image = null;
        if (!empty($vinyl->cover_image)) {
            $image = asset('storage/' . $vinyl->cover_image);
        }
        
        // Prepare breadcrumbs for schema
        $breadcrumbs = [
            ['name' => 'Início', 'url' => route('home')]
        ];
        
        if ($vinyl->categories && $vinyl->categories->count() > 0) {
            $category = $vinyl->categories->first();
            $breadcrumbs[] = [
                'name' => $category->nome,
                'url' => route('site.category', $category->slug)
            ];
        }
        
        $breadcrumbs[] = [
            'name' => $title,
            'url' => url()->current()
        ];
        
        // Return the view with all data
        return view('site.vinyl.show', [
            'vinyl' => $vinyl,
            'similarVinyls' => $similarVinyls,
            'wishlistItems' => $wishlistItems,
            'wantlistItems' => $wantlistItems,
            'inWishlist' => $inWishlist,
            'inWantlist' => $inWantlist,
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'image' => $image,
            'breadcrumbs' => $breadcrumbs
        ]);
    }
}
