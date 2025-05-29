<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Wantlist;
use App\Models\VinylMaster;

class WantlistPage extends Component
{
    public $wantlistItems = [];
    
    protected $listeners = [
        'wantlistUpdated' => 'refreshWantlist',
        'removeFromWantlist' => 'removeItem'
    ];
    
    public function mount()
    {
        $this->refreshWantlist();
    }
    
    /**
     * Atualiza a lista de itens da wantlist
     */
    public function refreshWantlist()
    {
        if (Auth::check()) {
            $this->wantlistItems = Auth::user()->wantlist()
                                    ->with('vinylMaster.artists')
                                    ->get();
        }
    }
    
    /**
     * Remove um item da wantlist
     */
    public function removeItem($vinylMasterId)
    {
        if (!Auth::check()) {
            return;
        }
        
        $userId = Auth::id();
        
        Wantlist::where('user_id', $userId)
                ->where('vinyl_master_id', $vinylMasterId)
                ->delete();
                
        $this->refreshWantlist();
        
        $this->dispatchBrowserEvent('notify', [
            'message' => 'Item removido da lista de interesse',
            'type' => 'success'
        ]);
    }
    
    /**
     * Ativar notificações para um item
     */
    public function toggleNotification($vinylMasterId)
    {
        if (!Auth::check()) {
            return;
        }
        
        $userId = Auth::id();
        
        $wantlistItem = Wantlist::where('user_id', $userId)
                             ->where('vinyl_master_id', $vinylMasterId)
                             ->first();
                             
        if ($wantlistItem) {
            $wantlistItem->notification_sent = !$wantlistItem->notification_sent;
            $wantlistItem->save();
            
            $message = $wantlistItem->notification_sent 
                ? 'Você receberá notificações quando este item estiver disponível' 
                : 'Notificações desativadas para este item';
                
            $this->dispatchBrowserEvent('notify', [
                'message' => $message,
                'type' => 'success'
            ]);
            
            $this->refreshWantlist();
        }
    }
    
    public function render()
    {
        return view('livewire.wantlist-page');
    }
}
