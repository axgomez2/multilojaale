<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\VinylMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Construtor do controlador.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    
    /**
     * Exibe a lista de desejos do usuário.
     */
    public function index()
    {
        $user = Auth::user();
        $wishlistItems = $user->wishlist()->with('vinylMaster.artists')->get();
        
        return view('site.wishlist.index', [
            'wishlistItems' => $wishlistItems,
        ]);
    }
    
    /**
     * Adiciona um item à lista de desejos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vinyl_master_id' => 'required|uuid|exists:vinyl_masters,id',
        ]);
        
        $user = Auth::user();
        $vinylMasterId = $request->vinyl_master_id;
        
        // Verifica se o vinil está disponível para compra
        $vinylMaster = VinylMaster::findOrFail($vinylMasterId);
        
        if (!$vinylMaster->isAvailable()) {
            return response()->json([
                'message' => 'Este vinil não está disponível para compra. Considere adicioná-lo à sua Wantlist.'
            ], 422);
        }
        
        // Verifica se o item já está na lista de desejos
        if (Wishlist::hasItem($user->id, $vinylMasterId)) {
            return response()->json([
                'message' => 'Este item já está na sua lista de desejos.'
            ], 422);
        }
        
        // Adiciona o item à lista de desejos
        $wishlistItem = Wishlist::create([
            'user_id' => $user->id,
            'vinyl_master_id' => $vinylMasterId,
        ]);
        
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Item adicionado à lista de desejos com sucesso!',
                'wishlist_item' => $wishlistItem,
            ]);
        }
        
        return redirect()->back()->with('success', 'Item adicionado à lista de desejos com sucesso!');
    }
    
    /**
     * Remove um item da lista de desejos.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $wishlistItem = Wishlist::where('user_id', $user->id)
                              ->where('vinyl_master_id', $id)
                              ->first();
        
        if (!$wishlistItem) {
            return response()->json([
                'message' => 'Item não encontrado na sua lista de desejos.'
            ], 404);
        }
        
        $wishlistItem->delete();
        
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Item removido da lista de desejos com sucesso!'
            ]);
        }
        
        return redirect()->back()->with('success', 'Item removido da lista de desejos com sucesso!');
    }
    

    

    
    /**
     * Alterna a presença de um item na lista de desejos (adiciona ou remove).
     * 
     * @param string $id ID do VinylMaster
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle($id)
    {
        $user = Auth::user();
        
        // Verifica se o vinil existe e está disponível
        $vinylMaster = VinylMaster::findOrFail($id);
        
        if (!$vinylMaster->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'Este vinil não está disponível para compra. Considere adicioná-lo à sua Wantlist.'
            ], 422);
        }
        
        // Verifica se o item já está na lista de desejos
        $wishlistItem = Wishlist::where('user_id', $user->id)
                                ->where('vinyl_master_id', $id)
                                ->first();
        
        if ($wishlistItem) {
            // Se existir, remove da lista
            $wishlistItem->delete();
            return response()->json([
                'success' => true,
                'message' => 'Item removido da lista de desejos',
                'added' => false,
                'id' => $id
            ]);
        } else {
            // Se não existir, adiciona à lista
            Wishlist::create([
                'user_id' => $user->id,
                'vinyl_master_id' => $id,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Item adicionado à lista de desejos',
                'added' => true,
                'id' => $id
            ]);
        }
    }
    
    /**
     * Verifica o status de um item na lista de desejos (para uso via API).
     */
    public function check(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:vinyl_masters,id',
        ]);
        
        $user = Auth::user();
        $ids = $request->ids;
        
        // Buscar quais dos IDs enviados estão na wishlist do usuário
        $items = Wishlist::where('user_id', $user->id)
                        ->whereIn('vinyl_master_id', $ids)
                        ->pluck('vinyl_master_id')
                        ->toArray();
        
        return response()->json([
            'items' => $items
        ]);
    }
}
