<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Wantlist;
use App\Models\VinylMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WantlistController extends Controller
{
    /**
     * Construtor do controlador.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    
    /**
     * Exibe a lista de interesse do usuário.
     */
    public function index()
    {
        $user = Auth::user();
        $wantlistItems = $user->wantlist()->with('vinylMaster.artists')->get();
        
        return view('site.wantlist.index', [
            'wantlistItems' => $wantlistItems,
        ]);
    }
    
    /**
     * Adiciona um item à lista de interesse.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vinyl_master_id' => 'required|uuid|exists:vinyl_masters,id',
        ]);
        
        $user = Auth::user();
        $vinylMasterId = $request->vinyl_master_id;
        
        // Verifica se o vinil está indisponível (caso contrário, deveria ir para a wishlist)
        $vinylMaster = VinylMaster::findOrFail($vinylMasterId);
        
        if ($vinylMaster->isAvailable()) {
            return response()->json([
                'message' => 'Este vinil está disponível para compra. Considere adicioná-lo à sua Wishlist.'
            ], 422);
        }
        
        // Verifica se o item já está na lista de interesse
        if (Wantlist::hasItem($user->id, $vinylMasterId)) {
            return response()->json([
                'message' => 'Este item já está na sua lista de interesse.'
            ], 422);
        }
        
        // Adiciona o item à lista de interesse
        $wantlistItem = Wantlist::create([
            'user_id' => $user->id,
            'vinyl_master_id' => $vinylMasterId,
            'notification_sent' => false,
        ]);
        
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Item adicionado à lista de interesse com sucesso! Você será notificado quando estiver disponível.',
                'wantlist_item' => $wantlistItem,
            ]);
        }
        
        return redirect()->back()->with('success', 'Item adicionado à lista de interesse com sucesso! Você será notificado quando estiver disponível.');
    }
    
    /**
     * Remove um item da lista de interesse.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $wantlistItem = Wantlist::where('user_id', $user->id)
                              ->where('vinyl_master_id', $id)
                              ->first();
        
        if (!$wantlistItem) {
            return response()->json([
                'message' => 'Item não encontrado na sua lista de interesse.'
            ], 404);
        }
        
        $wantlistItem->delete();
        
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Item removido da lista de interesse com sucesso!'
            ]);
        }
        
        return redirect()->back()->with('success', 'Item removido da lista de interesse com sucesso!');
    }
    

    

    
    /**
     * Alterna a presença de um item na lista de interesse (adiciona ou remove).
     * 
     * @param string $id ID do VinylMaster
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle($id)
    {
        $user = Auth::user();
        
        // Verifica se o vinil existe e está indisponível
        $vinylMaster = VinylMaster::findOrFail($id);
        
        if ($vinylMaster->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'Este vinil está disponível para compra. Considere adicioná-lo à sua Wishlist.'
            ], 422);
        }
        
        // Verifica se o item já está na lista de interesse
        $wantlistItem = Wantlist::where('user_id', $user->id)
                                ->where('vinyl_master_id', $id)
                                ->first();
        
        if ($wantlistItem) {
            // Se existir, remove da lista
            $wantlistItem->delete();
            return response()->json([
                'success' => true,
                'message' => 'Item removido da lista de interesse',
                'added' => false,
                'id' => $id
            ]);
        } else {
            // Se não existir, adiciona à lista
            Wantlist::create([
                'user_id' => $user->id,
                'vinyl_master_id' => $id,
                'notification_sent' => false,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'disco adicionado a lista de espera, voce sera notificado quando o item estiver disponivel',
                'added' => true,
                'id' => $id
            ]);
        }
    }
    
    /**
     * Verifica o status de um item na lista de interesse (para uso via API).
     */
    public function check(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:vinyl_masters,id',
        ]);
        
        $user = Auth::user();
        $ids = $request->ids;
        
        // Buscar quais dos IDs enviados estão na wantlist do usuário
        $items = Wantlist::where('user_id', $user->id)
                        ->whereIn('vinyl_master_id', $ids)
                        ->pluck('vinyl_master_id')
                        ->toArray();
        
        return response()->json([
            'items' => $items
        ]);
    }
}
