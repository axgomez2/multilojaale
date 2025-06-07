<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\VinylMaster;
use Illuminate\Support\Facades\Auth;

class LatestVinyls extends Component
{
    public $limit = 20;
    
    public function render()
    {
        // Buscar os últimos discos adicionados
        $vinyls = VinylMaster::with(['product', 'media', 'artists', 'vinylSec'])
            ->orderBy('created_at', 'desc')
            ->whereHas('vinylSec') // Apenas discos que têm registro em vinylSec
            ->take($this->limit)
            ->get();
            
        // Verificar quais discos estão na wishlist e wantlist do usuário atual
        $wishlistIds = collect([]);
        $wantlistIds = collect([]);
        
        if (Auth::check()) {
            // Obter IDs de discos na wishlist do usuário
            $wishlistIds = Auth::user()
                ->wishlist()
                ->pluck('vinyl_master_id');
                
            // Obter IDs de discos na wantlist do usuário
            $wantlistIds = Auth::user()
                ->wantlist()
                ->pluck('vinyl_master_id');
        }
        
        return view('livewire.latest-vinyls', [
            'vinyls' => $vinyls,
            'wishlistIds' => $wishlistIds,
            'wantlistIds' => $wantlistIds
        ]);
    }
}
