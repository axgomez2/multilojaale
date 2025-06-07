<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

class VinylCard extends Component
{
    // Propriedades do componente que serão passadas do parent
    public $vinyl;
    public $showActions = true;
    public $size = 'normal';
    public $orientation = 'vertical';
    public $inWishlist = false;
    public $inWantlist = false;
    public $highlightText = null;

    // Método para adicionar o vinil ao carrinho
    public function addToCart()
    {
        // Debug log para verificar se o método está sendo chamado
        Log::info('addToCart chamado', ['vinyl_id' => $this->vinyl->id ?? 'null', 'product_id' => optional($this->vinyl->product)->id ?? 'null']);
        // Verificar se o produto está disponível para compra
        if (!$this->vinyl->vinylSec || !$this->vinyl->vinylSec->in_stock || $this->vinyl->vinylSec->stock <= 0) {
            $this->dispatch('notify', [
                'message' => 'Este produto não está disponível para compra.',
                'type' => 'error'
            ]);
            return;
        }
        
        // Obter IDs necessários
        $vinylId = $this->vinyl->id ?? null;
        $productId = optional($this->vinyl->product)->id ?? null;
        
        if (!$vinylId || !$productId) {
            $this->dispatch('notify', [
                'message' => 'Não foi possível adicionar o produto ao carrinho.',
                'type' => 'error'
            ]);
            return;
        }

        try {
            // Verificar se possui sessão de carrinho
            $cartId = session('cart_id');
            
            // Parâmetros para a requisição
            $params = [
                'vinyl_master_id' => $vinylId,
                'product_id' => $productId,
                'quantity' => 1
            ];
            
            // Se tivermos um cart_id, incluir na requisição
            if ($cartId) {
                $params['cart_id'] = $cartId;
            }
            
            // Chamada HTTP para adicionar ao carrinho
            $response = \Illuminate\Support\Facades\Http::post(route('site.cart.add'), $params);
            
            $data = $response->json();
            
            if ($response->successful() && ($data['success'] ?? false)) {
                // Salvar o cart_id na sessão se for retornado
                if (isset($data['cart_id'])) {
                    session(['cart_id' => $data['cart_id']]);
                }
                
                $this->dispatch('notify', [
                    'message' => $data['message'] ?? 'Produto adicionado ao carrinho!',
                    'type' => 'success'
                ]);
                
                // Emitir evento para atualizar o contador do carrinho
                if (isset($data['cartCount'])) {
                    $this->dispatch('update-cart-count', [
                        'count' => $data['cartCount']
                    ]);
                }
            } else {
                throw new \Exception($data['message'] ?? 'Erro ao adicionar produto ao carrinho.');
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    // Método para alternar o estado de wishlist (apenas para produtos disponíveis)
    public function toggleWishlist()
    {
        // Debug log para verificar se o método está sendo chamado
        Log::info('toggleWishlist chamado', ['vinyl_id' => $this->vinyl->id ?? 'null']);
        // Verificar se o produto está disponível (só produtos disponíveis podem ser adicionados à wishlist)
        if (!$this->vinyl->vinylSec || !$this->vinyl->vinylSec->in_stock || $this->vinyl->vinylSec->stock <= 0) {
            $this->dispatch('notify', [
                'message' => 'Este produto não está disponível para adicionar à wishlist. Use a wantlist para produtos indisponíveis.',
                'type' => 'error'
            ]);
            return;
        }
        
        // Verificar se o usuário está autenticado
        if (!auth()->check()) {
            $this->dispatch('notify', [
                'message' => 'Faça login para adicionar aos favoritos',
                'type' => 'warning'
            ]);
            return;
        }
        
        try {
            $vinylId = $this->vinyl->id ?? null;
            $userId = auth()->id();
            
            if (!$vinylId) {
                throw new \Exception('ID do disco não encontrado.');
            }
            
            // Verificar se já está na wishlist
            $existsInWishlist = \App\Models\Wishlist::hasItem($userId, $vinylId);
            
            // Salvar no banco de dados
            if ($existsInWishlist) {
                // Remover da wishlist
                \App\Models\Wishlist::where('user_id', $userId)
                    ->where('vinyl_master_id', $vinylId)
                    ->delete();
                
                $message = 'Disco removido dos favoritos!';
                $this->inWishlist = false;
            } else {
                // Adicionar à wishlist
                \App\Models\Wishlist::create([
                    'user_id' => $userId,
                    'vinyl_master_id' => $vinylId
                ]);
                
                $message = 'Disco adicionado aos favoritos!';
                $this->inWishlist = true;
            }
                
            $this->dispatch('notify', [
                'message' => $message,
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    // Método para alternar o estado de wantlist (apenas para produtos indisponíveis)
    public function toggleWantlist()
    {
        // Debug log para verificar se o método está sendo chamado
        Log::info('toggleWantlist chamado', ['vinyl_id' => $this->vinyl->id ?? 'null']);
        // Verificar se o produto está indisponível (só produtos indisponíveis podem ser adicionados à wantlist)
        if ($this->vinyl->vinylSec && $this->vinyl->vinylSec->in_stock && $this->vinyl->vinylSec->stock > 0) {
            $this->dispatch('notify', [
                'message' => 'Este produto está disponível para compra. Adicione à wishlist ou ao carrinho.',
                'type' => 'info'
            ]);
            return;
        }
        
        // Verificar se o usuário está autenticado
        if (!auth()->check()) {
            $this->dispatch('notify', [
                'message' => 'Faça login para adicionar à wantlist',
                'type' => 'warning'
            ]);
            return;
        }
        
        try {
            $vinylId = $this->vinyl->id ?? null;
            $userId = auth()->id();
            
            if (!$vinylId) {
                throw new \Exception('ID do disco não encontrado.');
            }
            
            // Verificar se já está na wantlist
            $existsInWantlist = \App\Models\Wantlist::hasItem($userId, $vinylId);
            
            // Salvar no banco de dados
            if ($existsInWantlist) {
                // Remover da wantlist
                \App\Models\Wantlist::where('user_id', $userId)
                    ->where('vinyl_master_id', $vinylId)
                    ->delete();
                
                $message = 'Disco removido da wantlist!';
                $this->inWantlist = false;
            } else {
                // Adicionar à wantlist
                \App\Models\Wantlist::create([
                    'user_id' => $userId,
                    'vinyl_master_id' => $vinylId,
                    'notification_sent' => false
                ]);
                
                $message = 'Disco adicionado à wantlist!';
                $this->inWantlist = true;
            }
                
            $this->dispatch('notify', [
                'message' => $message,
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    // Hook para inicializar os estados baseado no banco de dados
    public function mount()
    {
        // Se o usuário estiver logado, verificar se o vinil está na wishlist e wantlist
        if (auth()->check() && $this->vinyl) {
            $userId = auth()->id();
            $vinylId = $this->vinyl->id;
            
            $this->inWishlist = \App\Models\Wishlist::hasItem($userId, $vinylId);
            $this->inWantlist = \App\Models\Wantlist::hasItem($userId, $vinylId);
        }
    }

    // Renderizar o componente
    public function render()
    {
        return view('livewire.vinyl-card');
    }
}
