<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VinylMaster;
use App\Models\Artist;
use App\Models\Genre;
use App\Models\Track;
use Illuminate\Database\Eloquent\Builder;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return redirect()->back()->with('error', 'Digite algo para pesquisar');
        }
        
        // Carregar IDs de itens na wishlist do usuário, se estiver logado
        $wishlistItems = [];
        if (auth()->check()) {
            $wishlistItems = \App\Models\Wishlist::where('user_id', auth()->id())
                ->pluck('vinyl_master_id')
                ->toArray();
        }
        
        // Buscar vinis pelo título ou artista
        $vinyls = VinylMaster::with(['artists', 'tracks', 'vinylSec', 'recordLabel', 'styles', 'categories'])
            ->where(function (Builder $builder) use ($query) {
                // Busca pelo título do vinil
                $builder->where('title', 'like', "%{$query}%");
                
                // Busca pelo nome do artista (relacionamento)
                $builder->orWhereHas('artists', function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                });
                
                // Busca pelo gênero/estilo (relacionamento)
                $builder->orWhereHas('styles', function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                });
                
                // Busca pelas categorias (CatStyleShop)
                $builder->orWhereHas('categories', function ($q) use ($query) {
                    $q->where('nome', 'like', "%{$query}%");
                });
                
                // Busca pelas faixas (relacionamento)
                $builder->orWhereHas('tracks', function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                });
                
                // Busca pela gravadora/selo (relacionamento)
                $builder->orWhereHas('recordLabel', function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(16);
            
        // Passar a query e os itens da wishlist para a view
        return view('site.search.results', compact('vinyls', 'query', 'wishlistItems'));
    }
}
