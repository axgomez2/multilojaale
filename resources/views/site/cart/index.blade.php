<x-app-layout>
    <!-- Container principal do carrinho -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6">
        <!-- Cabeçalho do carrinho -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0 mb-6">
            <div>
                <h1 class="text-2xl font-bold">Meu Carrinho</h1>
                <p class="text-gray-600 text-sm mt-1">{{ $cartItems->count() }}
                    {{ $cartItems->count() == 1 ? 'item' : 'itens' }} ({{ $cartItems->sum('quantity') }}
                    {{ $cartItems->sum('quantity') == 1 ? 'unidade' : 'unidades' }})</p>
            </div>
            @if (count($cartItems) > 0)
                <form action="{{ route('site.cart.clear') }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 hover:bg-red-50 text-sm font-medium flex items-center px-3 py-1.5 rounded-md border border-red-200 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Limpar Carrinho
                    </button>
                </form>
            @endif
        </div>

        <!-- Conteúdo principal do carrinho -->
        <div class="flex flex-col-reverse lg:grid lg:grid-cols-3 gap-4 lg:gap-8">
            <!-- Coluna da esquerda - Itens do carrinho -->
            <div class="lg:col-span-2 space-y-6">
                @if (count($cartItems) > 0)
                    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
                        <div class="mb-4 border-b pb-4">
                            <h2 class="text-lg font-semibold">Itens no Carrinho ({{ count($cartItems) }})</h2>
                        </div>
                        
                        <div class="space-y-4">
                            @foreach ($cartItems as $item)
                                <div class="border-b border-gray-200 pb-4 mb-4 last:border-b-0 last:mb-0 last:pb-0">
                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <!-- Imagem do produto (menor em mobile) -->
                                        <a href="{{ route('site.vinyl.show', [$item->vinylMaster->artists->first()->slug, $item->vinylMaster->slug]) }}" class="flex-shrink-0">
                                            <img class="h-16 w-16 sm:h-20 sm:w-20 object-cover rounded" 
                                                src="{{ $item->vinylMaster->cover_image }}" 
                                                alt="{{ $item->vinylMaster->title }}" />
                                        </a>
                                        
                                        <!-- Informações do produto -->
                                        <div class="flex-1 flex flex-col sm:flex-row sm:justify-between gap-3">
                                            <!-- Título e artista (ao lado da imagem em mobile) -->
                                            <div class="flex-1">
                                                <a href="{{ route('site.vinyl.show', [$item->vinylMaster->artists->first()->slug, $item->vinylMaster->slug]) }}" 
                                                   class="text-base font-medium text-gray-900 hover:text-purple-600 hover:underline">
                                                    {{ $item->vinylMaster->title }}
                                                </a>
                                                <p class="text-sm text-gray-600 mt-1">{{ $item->vinylMaster->artists->first()->name }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $item->vinylMaster->label }} / {{ $item->vinylMaster->catalog_number }}</p>
                                                
                                                @if (!$item->has_stock)
                                                    <div class="bg-red-100 border border-red-400 text-red-700 px-2 py-1 rounded text-xs mt-2 inline-block">
                                                        <span class="font-bold">Sem estoque suficiente</span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Quantidade e preço -->
                                            <div class="flex items-center justify-between sm:flex-col sm:items-end sm:min-w-[120px]">
                                                <!-- Controle de quantidade -->
                                                <div class="flex items-center">
                                                    <form action="{{ route('site.cart.update', $item->id) }}" method="POST" class="flex items-center" id="update-form-{{ $item->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="button" onclick="decrementQuantity('{{ $item->id }}')" 
                                                            class="inline-flex h-6 w-6 items-center justify-center rounded border border-gray-300 bg-white text-gray-500 hover:bg-gray-100">
                                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                            </svg>
                                                        </button>
                                                        <input type="number" name="quantity" id="quantity-{{ $item->id }}" 
                                                            class="mx-1 h-6 w-10 rounded border-gray-200 text-center text-sm" 
                                                            value="{{ $item->quantity }}" min="1" max="{{ min(10, $item->vinylMaster->vinylSec->stock) }}" readonly />
                                                        <button type="button" onclick="incrementQuantity('{{ $item->id }}')" 
                                                            class="inline-flex h-6 w-6 items-center justify-center rounded border border-gray-300 bg-white text-gray-500 hover:bg-gray-100">
                                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                                
                                                <!-- Preço -->
                                                <div class="text-right">
                                                    <p class="text-base font-bold text-gray-900">{{ number_format($item->vinylMaster->vinylSec->price, 2, ',', '.') }} €</p>
                                                    @if ($item->vinylMaster->vinylSec->original_price > $item->vinylMaster->vinylSec->price)
                                                        <p class="text-xs text-gray-500 line-through">{{ number_format($item->vinylMaster->vinylSec->original_price, 2, ',', '.') }} €</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Botões de ação agrupados (abaixo em mobile, à direita em desktop) -->
                                    <div class="mt-3 flex justify-end">
                                        <div class="inline-flex rounded-md shadow-sm" role="group">
                                            <!-- Botão de favoritos -->
                                            <button type="button" onclick="toggleWishlist('{{ $item->vinylMaster->id }}', this)" 
                                                data-vinyl-id="{{ $item->vinylMaster->id }}" 
                                                class="px-2 py-1 text-xs font-medium {{ in_array($item->vinylMaster->id, $wishlistItems ?? []) ? 'text-red-600' : 'text-gray-500' }} bg-white border border-gray-200 rounded-l-lg hover:bg-gray-100 hover:text-gray-900 focus:z-10 focus:ring-2 focus:ring-purple-500">
                                                <svg class="h-4 w-4 inline wishlist-icon {{ in_array($item->vinylMaster->id, $wishlistItems ?? []) ? 'text-red-600' : '' }}" 
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" 
                                                    fill="{{ in_array($item->vinylMaster->id, $wishlistItems ?? []) ? 'currentColor' : 'none' }}" 
                                                    viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                        d="M12.01 6.001C6.5 1 1 8 5.782 13.001L12.011 20l6.23-7C23 8 17.5 1 12.01 6.002Z" />
                                                </svg>
                                                <span class="sr-only sm:not-sr-only sm:ml-1">{{ in_array($item->vinylMaster->id, $wishlistItems ?? []) ? 'Desfavoritar' : 'Favoritar' }}</span>
                                            </button>
                                            
                                            <!-- Botão de salvar para depois -->
                                            <form action="{{ route('site.cart.save-for-later', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="px-2 py-1 text-xs font-medium text-gray-500 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-gray-900 focus:z-10 focus:ring-2 focus:ring-purple-500">
                                                    <svg class="h-4 w-4 inline" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v13m0-13 4-4m-4 4-4-4M6 10h12" />
                                                    </svg>
                                                    <span class="sr-only sm:not-sr-only sm:ml-1">Salvar</span>
                                                </button>
                                            </form>
                                            
                                            <!-- Botão de remover -->
                                            <form action="{{ route('site.cart.remove', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1 text-xs font-medium text-red-600 bg-white border border-gray-200 rounded-r-lg hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:ring-2 focus:ring-purple-500">
                                                    <svg class="h-4 w-4 inline" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                                    </svg>
                                                    <span class="sr-only sm:not-sr-only sm:ml-1">Remover</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Seu carrinho está vazio</h3>
                        <p class="mt-1 text-sm text-gray-500">Parece que você ainda não adicionou nenhum item ao seu carrinho.</p>
                        <div class="mt-6">
                            <a href="{{ route('home') }}" class="inline-flex items-center rounded-md border border-transparent bg-purple-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                Continuar comprando
                            </a>
                        </div>
                    </div>
                @endif
                
                <!-- Itens sem estoque -->
                @if (count($itemsWithoutStock) > 0)
                    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mt-6">
                        <div class="mb-4 border-b pb-4">
                            <h2 class="text-lg font-semibold text-red-600">Itens sem estoque suficiente ({{ count($itemsWithoutStock) }})</h2>
                        </div>
                        
                        <div class="space-y-4">
                            @foreach ($itemsWithoutStock as $item)
                                <div class="border-b border-gray-200 pb-4 mb-4 last:border-b-0 last:mb-0 last:pb-0 bg-red-50 rounded p-3">
                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <!-- Imagem do produto (menor em mobile) -->
                                        <a href="{{ route('site.vinyl.show', [$item->vinylMaster->artists->first()->slug, $item->vinylMaster->slug]) }}" class="flex-shrink-0">
                                            <img class="h-16 w-16 sm:h-20 sm:w-20 object-cover rounded" 
                                                src="{{ $item->vinylMaster->cover_image }}" 
                                                alt="{{ $item->vinylMaster->title }}" />
                                        </a>
                                        
                                        <!-- Informações do produto -->
                                        <div class="flex-1 flex flex-col sm:flex-row sm:justify-between gap-3">
                                            <!-- Título e artista (ao lado da imagem em mobile) -->
                                            <div class="flex-1">
                                                <a href="{{ route('site.vinyl.show', [$item->vinylMaster->artists->first()->slug, $item->vinylMaster->slug]) }}" 
                                                   class="text-base font-medium text-gray-900 hover:text-purple-600 hover:underline">
                                                    {{ $item->vinylMaster->title }}
                                                </a>
                                                <p class="text-sm text-gray-600 mt-1">{{ $item->vinylMaster->artists->first()->name }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $item->vinylMaster->label }} / {{ $item->vinylMaster->catalog_number }}</p>
                                                
                                                <div class="bg-red-100 border border-red-400 text-red-700 px-2 py-1 rounded text-xs mt-2 inline-block">
                                                    <span class="font-bold">Sem estoque suficiente</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Quantidade e preço -->
                                            <div class="flex items-center justify-between sm:flex-col sm:items-end sm:min-w-[120px]">
                                                <!-- Controle de quantidade -->
                                                <div class="flex items-center">
                                                    <form action="{{ route('site.cart.update', $item->id) }}" method="POST" class="flex items-center" id="update-form-{{ $item->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="button" onclick="decrementQuantity('{{ $item->id }}')" 
                                                            class="inline-flex h-6 w-6 items-center justify-center rounded border border-gray-300 bg-white text-gray-500 hover:bg-gray-100">
                                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                            </svg>
                                                        </button>
                                                        <input type="number" name="quantity" id="quantity-{{ $item->id }}" 
                                                            class="mx-1 h-6 w-10 rounded border-gray-200 text-center text-sm" 
                                                            value="{{ $item->quantity }}" min="1" max="{{ min(10, $item->vinylMaster->vinylSec->stock) }}" readonly />
                                                        <button type="button" onclick="incrementQuantity('{{ $item->id }}')" 
                                                            class="inline-flex h-6 w-6 items-center justify-center rounded border border-gray-300 bg-white text-gray-500 hover:bg-gray-100">
                                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                                
                                                <!-- Preço -->
                                                <div class="text-right">
                                                    <p class="text-base font-bold text-gray-900">{{ number_format($item->vinylMaster->vinylSec->price, 2, ',', '.') }} €</p>
                                                    @if ($item->vinylMaster->vinylSec->original_price > $item->vinylMaster->vinylSec->price)
                                                        <p class="text-xs text-gray-500 line-through">{{ number_format($item->vinylMaster->vinylSec->original_price, 2, ',', '.') }} €</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Botões de ação agrupados (abaixo em mobile, à direita em desktop) -->
                                    <div class="mt-3 flex justify-end">
                                        <div class="inline-flex rounded-md shadow-sm" role="group">
                                            <!-- Botão de favoritos -->
                                            <button type="button" onclick="toggleWishlist('{{ $item->vinylMaster->id }}', this)" 
                                                data-vinyl-id="{{ $item->vinylMaster->id }}" 
                                                class="px-2 py-1 text-xs font-medium {{ in_array($item->vinylMaster->id, $wishlistItems ?? []) ? 'text-red-600' : 'text-gray-500' }} bg-white border border-gray-200 rounded-l-lg hover:bg-gray-100 hover:text-gray-900 focus:z-10 focus:ring-2 focus:ring-purple-500">
                                                <svg class="h-4 w-4 inline wishlist-icon {{ in_array($item->vinylMaster->id, $wishlistItems ?? []) ? 'text-red-600' : '' }}" 
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" 
                                                    fill="{{ in_array($item->vinylMaster->id, $wishlistItems ?? []) ? 'currentColor' : 'none' }}" 
                                                    viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                        d="M12.01 6.001C6.5 1 1 8 5.782 13.001L12.011 20l6.23-7C23 8 17.5 1 12.01 6.002Z" />
                                                </svg>
                                                <span class="sr-only sm:not-sr-only sm:ml-1">{{ in_array($item->vinylMaster->id, $wishlistItems ?? []) ? 'Desfavoritar' : 'Favoritar' }}</span>
                                            </button>
                                            
                                            <!-- Botão de salvar para depois -->
                                            <form action="{{ route('site.cart.save-for-later', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="px-2 py-1 text-xs font-medium text-gray-500 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-gray-900 focus:z-10 focus:ring-2 focus:ring-purple-500">
                                                    <svg class="h-4 w-4 inline" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v13m0-13 4-4m-4 4-4-4M6 10h12" />
                                                    </svg>
                                                    <span class="sr-only sm:not-sr-only sm:ml-1">Salvar</span>
                                                </button>
                                            </form>
                                            
                                            <!-- Botão de remover -->
                                            <form action="{{ route('site.cart.remove', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1 text-xs font-medium text-red-600 bg-white border border-gray-200 rounded-r-lg hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:ring-2 focus:ring-purple-500">
                                                    <svg class="h-4 w-4 inline" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                                    </svg>
                                                    <span class="sr-only sm:not-sr-only sm:ml-1">Remover</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Itens salvos para depois -->
                @if (count($savedItems) > 0)
                    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mt-6">
                        <div class="mb-4 border-b pb-4">
                            <h2 class="text-lg font-semibold">Itens salvos para depois ({{ count($savedItems) }})</h2>
                        </div>
                        
                        <div class="space-y-4">
                            @foreach ($savedItems as $item)
                                <div class="border-b border-gray-200 pb-4 mb-4 last:border-b-0 last:mb-0 last:pb-0">
                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <!-- Imagem do produto (menor em mobile) -->
                                        <a href="{{ route('site.vinyl.show', [$item->vinylMaster->artists->first()->slug, $item->vinylMaster->slug]) }}" class="flex-shrink-0">
                                            <img class="h-16 w-16 sm:h-20 sm:w-20 object-cover rounded" 
                                                src="{{ $item->vinylMaster->cover_image }}" 
                                                alt="{{ $item->vinylMaster->title }}" />
                                        </a>
                                        
                                        <!-- Informações do produto -->
                                        <div class="flex-1 flex flex-col sm:flex-row sm:justify-between gap-3">
                                            <!-- Título e artista (ao lado da imagem em mobile) -->
                                            <div class="flex-1">
                                                <a href="{{ route('site.vinyl.show', [$item->vinylMaster->artists->first()->slug, $item->vinylMaster->slug]) }}" 
                                                   class="text-base font-medium text-gray-900 hover:text-purple-600 hover:underline">
                                                    {{ $item->vinylMaster->title }}
                                                </a>
                                                <p class="text-sm text-gray-600 mt-1">{{ $item->vinylMaster->artists->first()->name }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $item->vinylMaster->label }} / {{ $item->vinylMaster->catalog_number }}</p>
                                                
                                                @if ($item->vinylMaster->vinylSec->stock <= 0)
                                                    <div class="bg-red-100 border border-red-400 text-red-700 px-2 py-1 rounded text-xs mt-2 inline-block">
                                                        <span class="font-bold">Sem estoque</span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Preço -->
                                            <div class="flex items-center justify-between sm:flex-col sm:items-end sm:min-w-[120px]">
                                                <div class="text-right">
                                                    <p class="text-base font-bold text-gray-900">{{ number_format($item->vinylMaster->vinylSec->price, 2, ',', '.') }} €</p>
                                                    @if ($item->vinylMaster->vinylSec->original_price > $item->vinylMaster->vinylSec->price)
                                                        <p class="text-xs text-gray-500 line-through">{{ number_format($item->vinylMaster->vinylSec->original_price, 2, ',', '.') }} €</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Botões de ação agrupados (abaixo em mobile, à direita em desktop) -->
                                    <div class="mt-3 flex justify-end">
                                        <div class="inline-flex rounded-md shadow-sm" role="group">
                                            <!-- Botão de favoritos -->
                                            <button type="button" onclick="toggleWishlist('{{ $item->vinylMaster->id }}', this)" 
                                                data-vinyl-id="{{ $item->vinylMaster->id }}" 
                                                class="px-2 py-1 text-xs font-medium {{ in_array($item->vinylMaster->id, $wishlistItems ?? []) ? 'text-red-600' : 'text-gray-500' }} bg-white border border-gray-200 rounded-l-lg hover:bg-gray-100 hover:text-gray-900 focus:z-10 focus:ring-2 focus:ring-purple-500">
                                                <svg class="h-4 w-4 inline wishlist-icon {{ in_array($item->vinylMaster->id, $wishlistItems ?? []) ? 'text-red-600' : '' }}" 
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" 
                                                    fill="{{ in_array($item->vinylMaster->id, $wishlistItems ?? []) ? 'currentColor' : 'none' }}" 
                                                    viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                        d="M12.01 6.001C6.5 1 1 8 5.782 13.001L12.011 20l6.23-7C23 8 17.5 1 12.01 6.002Z" />
                                                </svg>
                                                <span class="sr-only sm:not-sr-only sm:ml-1">{{ in_array($item->vinylMaster->id, $wishlistItems ?? []) ? 'Desfavoritar' : 'Favoritar' }}</span>
                                            </button>
                                            
                                            <!-- Botão de mover para o carrinho -->
                                            <form action="{{ route('site.cart.move-to-cart', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="px-2 py-1 text-xs font-medium text-purple-600 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-purple-800 focus:z-10 focus:ring-2 focus:ring-purple-500">
                                                    <svg class="h-4 w-4 inline" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
                                                    </svg>
                                                    <span class="sr-only sm:not-sr-only sm:ml-1">Mover para carrinho</span>
                                                </button>
                                            </form>
                                            
                                            <!-- Botão de remover -->
                                            <form action="{{ route('site.cart.remove', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1 text-xs font-medium text-red-600 bg-white border border-gray-200 rounded-r-lg hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:ring-2 focus:ring-purple-500">
                                                    <svg class="h-4 w-4 inline" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                                    </svg>
                                                    <span class="sr-only sm:not-sr-only sm:ml-1">Remover</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Coluna da direita - Resumo do pedido -->
            <div class="lg:col-span-1">
                @if (count($cartItems) > 0)
                    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 lg:sticky lg:top-4">
                        <h2 class="text-lg font-semibold mb-4 border-b pb-4">Resumo do Pedido</h2>
                        
                        <!-- Aplicar cupom -->
                        <div class="mb-6">
                            <form action="{{ route('site.cart.apply-coupon') }}" method="POST" class="flex">
                                @csrf
                                <input type="text" name="coupon_code" placeholder="Código do cupom" 
                                    class="flex-1 rounded-l-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm" />
                                <button type="submit" 
                                    class="inline-flex items-center rounded-r-lg border border-transparent bg-purple-600 px-3 py-2 text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                    Aplicar
                                </button>
                            </form>
                            
                            @if (session('coupon_error'))
                                <div class="mt-2 text-sm text-red-600">
                                    {{ session('coupon_error') }}
                                </div>
                            @endif
                            
                            @if (session('coupon_success'))
                                <div class="mt-2 text-sm text-green-600">
                                    {{ session('coupon_success') }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Detalhes do preço -->
                        <div class="space-y-2 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium">R$ {{ number_format($cartTotal, 2, ',', '.') }}</span>
                            </div>
                            
                            @if (isset($discount) && $discount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Desconto:</span>
                                    <span class="font-medium text-green-600">-R$ {{ number_format($discount, 2, ',', '.') }}</span>
                                </div>
                            @endif
                            
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Frete:</span>
                                <span class="font-medium">Calculado no checkout</span>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-2 mt-2">
                                <div class="flex justify-between">
                                    <span class="text-base font-semibold">Total:</span>
                                    <span class="text-base font-bold">R$ {{ number_format($cartTotal - $discount, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botões de ação -->
                        <div class="space-y-3">
                            <a href="{{ route('site.shipping.index') }}" 
                                class="flex w-full items-center justify-center rounded-md border border-transparent bg-purple-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                Finalizar Compra
                            </a>
                            
                            <a href="{{ route('home') }}" 
                                class="flex w-full items-center justify-center rounded-md border border-purple-600 px-5 py-2.5 text-sm font-medium text-purple-600 hover:bg-gray-50">
                                Continuar Comprando
                            </a>
                        </div>
                        
                        <!-- Informações adicionais -->
                        <div class="mt-6 text-xs text-gray-500">
                            <p>Formas de pagamento aceitas:</p>
                            <div class="flex gap-2 mt-2">
                                <span class="bg-gray-100 p-1 rounded">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="24" height="24" rx="4" fill="#1434CB"/>
                                        <path d="M9.5 15.7L6 12.1L7.1 11L9.5 13.4L16.9 6L18 7.1L9.5 15.7Z" fill="white"/>
                                    </svg>
                                </span>
                                <span class="bg-gray-100 p-1 rounded">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="24" height="24" rx="4" fill="#FF5F00"/>
                                        <circle cx="8" cy="12" r="4" fill="#EB001B"/>
                                        <circle cx="16" cy="12" r="4" fill="#F79E1B"/>
                                    </svg>
                                </span>
                                <span class="bg-gray-100 p-1 rounded">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="24" height="24" rx="4" fill="#006FCF"/>
                                        <path d="M12 6L14 10H10L12 6Z" fill="white"/>
                                        <path d="M12 18L10 14H14L12 18Z" fill="white"/>
                                    </svg>
                                </span>
                                <span class="bg-gray-100 p-1 rounded">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="24" height="24" rx="4" fill="#4D4D4D"/>
                                        <path d="M7 12H17M12 7V17" stroke="white" stroke-width="2"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Carregando os scripts do carrinho -->
    <script src="{{ asset('js/cart.js') }}"></script>
</x-app-layout>