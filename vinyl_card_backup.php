<?php

namespace App\Livewire;

use Livewire\Component;

class VinylCard extends Component
{
    // Propriedades do componente que serÃ£o passadas do parent
    public $vinyl;
    public $showActions = true;
    public $size = 'normal';
    public $orientation = 'vertical';
    public $inWishlist = false;
    public $inWantlist = false;
    public $highlightText = null;

    // MÃ©todo para adicionar o vinil ao carrinho
    public function addToCart()
    {
        // Verificar se o produto estÃ¡ disponÃ­vel para compra
        if (!$this->vinyl->vinylSec || !$this->vinyl->vinylSec->in_stock || $this->vinyl->vinylSec->stock <= 0) {
            $this->dispatch('notify', ['message' => 'Este produto nÃ£o estÃ¡ disponÃ­vel para compra.',
                'type' => 'error'
            ]);
            return;
        }
        
        // Obter IDs necessÃ¡rios
        $vinylId = $this->vinyl->id ?? null;
        $productId = optional($this->vinyl->product)->id ?? null;
        
        if (!$vinylId || !$productId) {
            $this->dispatch('notify', ['message' => 'NÃ£o foi possÃ­vel adicionar o produto ao carrinho.',
                'type' => 'error'
            ]);
            return;
        }

        try {
            // Verificar se possui sessÃ£o de carrinho
            $cartId = session('cart_id');
            
            // ParÃ¢metros para a requisiÃ§Ã£o
            $params = [
                'vinyl_master_id' => $vinylId,
                'product_id' => $productId,
                'quantity' => 1
            ];
            
            // Se tivermos um cart_id, incluir na requisiÃ§Ã£o
            if ($cartId) {
                $params['cart_id'] = $cartId;
            }
            
            // Chamada HTTP para adicionar ao carrinho - usado diretamente o controlador como alternativa Ã  chamada HTTP
            $response = \Illuminate\Support\Facades\Http::post(route('site.cart.add'), $params);
            
            $data = $response->json();
            
            if ($response->successful() && ($data['success'] ?? false)) {
                // Salvar o cart_id na sessÃ£o se for retornado
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
            $this->dispatch('notify', 
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    // MÃ©todo para alternar o estado de wishlist (apenas para produtos disponÃ­veis)
    public function toggleWishlist()
    {
        // Verificar se o produto estÃ¡ disponÃ­vel (sÃ³ produtos disponÃ­veis podem ser adicionados Ã  wishlist)
        if (!$this->vinyl->vinylSec || !$this->vinyl->vinylSec->in_stock || $this->vinyl->vinylSec->stock <= 0) {
            $this->dispatch('notify', ['message' => 'Este produto nÃ£o estÃ¡ disponÃ­vel para adicionar Ã  wishlist. Use a wantlist para produtos indisponÃ­veis.',
                'type' => 'error'
            ]);
            return;
        }
        
        // Verificar se o usuÃ¡rio estÃ¡ autenticado
        if (!auth()->check()) {
            $this->dispatch('notify', ['message' => 'FaÃ§a login para adicionar aos favoritos',
                'type' => 'warning'
            ]);
            return;
        }
        
        try {
            $vinylId = $this->vinyl->id ?? null;
            $userId = auth()->id();
            
            if (!$vinylId) {
                throw new \Exception('ID do disco nÃ£o encontrado.');
            }
            
            // Verificar se jÃ¡ estÃ¡ na wishlist
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
                // Adicionar Ã  wishlist
                \App\Models\Wishlist::create([
                    'user_id' => $userId,
                    'vinyl_master_id' => $vinylId
                ]);
                
                $message = 'Disco adicionado aos favoritos!';
                $this->inWishlist = true;
            }
                
            $this->dispatch('notify', 
                'message' => $message,
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', 
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    // MÃ©todo para alternar o estado de wantlist (apenas para produtos indisponÃ­veis)
    public function toggleWantlist()
    {
        // Verificar se o produto estÃ¡ indisponÃ­vel (sÃ³ produtos indisponÃ­veis podem ser adicionados Ã  wantlist)
        if ($this->vinyl->vinylSec && $this->vinyl->vinylSec->in_stock && $this->vinyl->vinylSec->stock > 0) {
            $this->dispatch('notify', ['message' => 'Este produto estÃ¡ disponÃ­vel para compra. Adicione Ã  wishlist ou ao carrinho.',
                'type' => 'info'
            ]);
            return;
        }
        
        // Verificar se o usuÃ¡rio estÃ¡ autenticado
        if (!auth()->check()) {
            $this->dispatch('notify', ['message' => 'FaÃ§a login para adicionar Ã  wantlist',
                'type' => 'warning'
            ]);
            return;
        }
        
        try {
            $vinylId = $this->vinyl->id ?? null;
            $userId = auth()->id();
            
            if (!$vinylId) {
                throw new \Exception('ID do disco nÃ£o encontrado.');
            }
            
            // Verificar se jÃ¡ estÃ¡ na wantlist
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
                // Adicionar Ã  wantlist
                \App\Models\Wantlist::create([
                    'user_id' => $userId,
                    'vinyl_master_id' => $vinylId,
                    'notification_sent' => false
                ]);
                
                $message = 'Disco adicionado Ã  wantlist!';
                $this->inWantlist = true;
            }
                
            $this->dispatch('notify', 
                'message' => $message,
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', 
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.vinyl-card');
    }
}
