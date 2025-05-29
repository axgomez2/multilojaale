<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\VinylMaster;
use App\Models\Wishlist;
use App\Models\Wantlist;

class ToggleWishlistWantlist extends Component
{
    public $vinylId;
    public $isAvailable;
    public $inWishlist = false;
    public $inWantlist = false;
    
    public function mount($vinylId, $isAvailable)
    {
        $this->vinylId = $vinylId;
        $this->isAvailable = $isAvailable;
        
        if (Auth::check()) {
            $this->checkStatus();
        }
    }
    
    /**
     * Verifica o status atual do item (se está na wishlist ou wantlist)
     */
    public function checkStatus()
    {
        if (!Auth::check()) {
            return;
        }
        
        $userId = Auth::id();
        $vinylIdInt = (int) $this->vinylId; // Conversão para inteiro para garantir compatibilidade
        
        if ($this->isAvailable) {
            $this->inWishlist = Wishlist::hasItem($userId, $vinylIdInt);
        } else {
            $this->inWantlist = Wantlist::hasItem($userId, $vinylIdInt);
        }
    }
    
    /**
     * Alternar item na wishlist ou wantlist
     */
    public function toggle()
    {
        \Log::info('ToggleWishlistWantlist: Toggle iniciado', [
            'vinyl_id' => $this->vinylId,
            'tipo_id' => gettype($this->vinylId),
            'is_available' => $this->isAvailable,
            'user_id' => Auth::id()
        ]);
        
        if (!Auth::check()) {
            \Log::warning('ToggleWishlistWantlist: Usuário não autenticado');
            // Redirecionar para login
            $this->dispatchBrowserEvent('notify', [
                'message' => 'Faça login para adicionar itens às suas listas',
                'type' => 'warning'
            ]);
            return redirect()->route('login', ['redirect' => url()->current()]);
        }
        
        // Debug para identificar problemas no browser
        $this->dispatchBrowserEvent('console-log', [
            'message' => 'Dados do toggle: ',
            'data' => [
                'vinyl_id' => $this->vinylId,
                'tipo_id' => gettype($this->vinylId),
                'is_available' => $this->isAvailable,
                'user_id' => Auth::id()
            ]
        ]);
        
        try {
            \Log::info('ToggleWishlistWantlist: Buscando vinil', ['id' => $this->vinylId]);
            $vinyl = VinylMaster::findOrFail($this->vinylId);
            \Log::info('ToggleWishlistWantlist: Vinil encontrado', ['title' => $vinyl->title]);
            $userId = Auth::id();
            $message = '';
            $success = true;
        
            if ($this->isAvailable) {
                // Lógica da wishlist
                if (!$vinyl->isAvailable()) {
                    $this->dispatchBrowserEvent('notify', [
                        'message' => 'Este vinil não está disponível para compra. Considere adicioná-lo à sua lista de interesse.',
                        'type' => 'warning'
                    ]);
                    return;
                }
                
                // Converte o ID para inteiro para garantir compatibilidade com o modelo
                $vinylIdInt = (int) $this->vinylId;
                
                // Verifica se o item já está na wishlist
                if (Wishlist::hasItem($userId, $vinylIdInt)) {
                    // Remove da wishlist
                    Wishlist::where('user_id', $userId)
                          ->where('vinyl_master_id', $vinylIdInt)
                          ->delete();
                           
                    $this->inWishlist = false;
                    $message = 'Item removido da lista de desejos';
                } else {
                    // Adiciona à wishlist
                    Wishlist::create([
                        'user_id' => $userId,
                        'vinyl_master_id' => $vinylIdInt,
                    ]);
                    
                    $this->inWishlist = true;
                    $message = 'Item adicionado à lista de desejos';
                }
            } else {
                // Lógica da wantlist
                if ($vinyl->isAvailable()) {
                    $this->dispatchBrowserEvent('notify', [
                        'message' => 'Este vinil está disponível para compra. Considere adicioná-lo à sua lista de desejos.',
                        'type' => 'warning'
                    ]);
                    return;
                }
                
                // Converte o ID para inteiro para garantir compatibilidade com o modelo
                $vinylIdInt = (int) $this->vinylId;
                
                // Verifica se o item já está na wantlist
                if (Wantlist::hasItem($userId, $vinylIdInt)) {
                    // Remove da wantlist
                    Wantlist::where('user_id', $userId)
                          ->where('vinyl_master_id', $vinylIdInt)
                          ->delete();
                          
                    $this->inWantlist = false;
                    $message = 'Item removido da lista de interesse';
                } else {
                    // Adiciona à wantlist
                    Wantlist::create([
                        'user_id' => $userId,
                        'vinyl_master_id' => $vinylIdInt,
                    ]);
                    
                    $this->inWantlist = true;
                    $message = 'Item adicionado à lista de interesse';
                }
            }
            
            // Emitir evento para atualizar outros componentes Livewire na página
            if ($this->isAvailable) {
                \Log::info('ToggleWishlistWantlist: Emitindo evento wishlistUpdated');
                $this->emit('wishlistUpdated');
            } else {
                \Log::info('ToggleWishlistWantlist: Emitindo evento wantlistUpdated');
                $this->emit('wantlistUpdated');
            }
            
            \Log::info('ToggleWishlistWantlist: Operação concluída com sucesso', ['message' => $message]);
            $this->dispatchBrowserEvent('notify', [
                'message' => $message,
                'type' => 'success'
            ]);
            
            // Adicionar mais um evento de log no console do navegador
            $this->dispatchBrowserEvent('console-log', [
                'message' => 'Operação concluída: ' . $message,
                'data' => [
                    'in_wishlist' => $this->inWishlist,
                    'in_wantlist' => $this->inWantlist
                ]
            ]);
            
        } catch (\Exception $e) {
            // Registrar erro e notificar o usuário
            \Log::error('Erro no toggle wishlist/wantlist: ' . $e->getMessage(), [
                'exception' => $e,
                'vinyl_id' => $this->vinylId,
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatchBrowserEvent('console-log', [
                'message' => 'Erro ao processar: ' . $e->getMessage(),
                'data' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ]);
            
            $this->dispatchBrowserEvent('notify', [
                'message' => 'Ocorreu um erro ao processar sua solicitação. Tente novamente.',
                'type' => 'error'
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.toggle-wishlist-wantlist');
    }
}
