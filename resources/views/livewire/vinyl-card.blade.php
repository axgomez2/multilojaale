<div 
    class="max-w-sm mx-auto {{ $size === 'small' ? 'max-w-xs' : ($size === 'large' ? 'max-w-md' : '') }} {{ $orientation === 'horizontal' ? 'flex' : '' }}"
    x-data="vinylCard"
    x-init="
        vinylId = '{{ $vinyl->id }}'; 
        vinylTitle = {{ Js::from($vinyl->title) }}; 
        vinylArtist = {{ Js::from($vinyl->artists->pluck('name')->implode(', ')) }}; 
        vinylCover = '{{ asset('storage/' . $vinyl->cover_image) }}';
    "
>
    <!-- Imagem do produto -->
    <div class="{{ $orientation === 'horizontal' ? 'w-1/3' : 'w-full' }} relative overflow-hidden group">
        <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug ?? 'unknown', 'titleSlug' => $vinyl->slug]) }}" class="block">
            @if($vinyl->cover_image)
                <img 
                    src="{{ asset('storage/' . $vinyl->cover_image) }}" 
                    alt="{{ optional($vinyl->artists->first())->name ?? 'Artista' }} - {{ $vinyl->title }}" 
                    class="w-full h-auto object-cover transition-transform duration-300 group-hover:scale-105"
                >
            @else
                <div class="bg-gray-200 aspect-square flex items-center justify-center">
                    <span class="text-gray-400">Sem imagem</span>
                </div>
            @endif
            
            <!-- Status labels do estoque -->
            <div class="absolute top-2 left-2 z-10 flex flex-col gap-1">
                @if($vinyl->vinylSec && $vinyl->vinylSec->in_stock && $vinyl->vinylSec->stock > 0)
                    <div class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">
                        Disponível
                    </div>
                @else
                    <div class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">
                        Indisponível
                    </div>
                @endif
                
                @if($vinyl->vinylSec && $vinyl->vinylSec->original_price > $vinyl->vinylSec->price)
                    <div class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">
                        Oferta
                    </div>
                @endif
            </div>
        </a>
        
        <!-- Botão de play para ouvir amostras -->
        @if($vinyl->tracks && $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }))
        <button
            type="button"
            class="absolute inset-0 flex items-center justify-center bg-black/10 opacity-0 hover:opacity-100 hover:bg-black/40 transition-all duration-300 ease-in-out"
            x-on:click="playAudio('{{ $vinyl->id }}', {{ Js::from($vinyl->title) }}, {{ Js::from($vinyl->artists->pluck("name")->implode(", ")) }}, '{{ asset("storage/" . $vinyl->cover_image) }}')" 
            title="Ouvir amostra"
        >
            <div class="w-16 h-16 rounded-full bg-white/80 hover:bg-white flex items-center justify-center transition-all duration-300 transform hover:scale-110 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-purple-600 hover:text-purple-700 ml-1 transition-colors duration-300" viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="5 3 19 12 5 21 5 3" />
                </svg>
            </div>
        </button>
        @endif
    </div>

    <!-- Conteúdo do card -->
    <div class="{{ $orientation === 'horizontal' ? 'w-2/3 pl-4' : 'w-full pt-3' }}">
        <div class="flex justify-between">
            <h3 class="text-sm font-semibold line-clamp-1">
                <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug ?? 'unknown', 'titleSlug' => $vinyl->slug]) }}" class="hover:text-purple-600 transition-colors">
                    {{ $vinyl->artists->first()->name ?? 'Artista desconhecido' }}
                </a>
            </h3>
            
            <!-- Apenas mostrar valor para produtos com preço -->
            @if($vinyl->product && $vinyl->product->price > 0)
                <span class="text-sm font-bold text-purple-600">
                    R$ {{ number_format($vinyl->product->price, 2, ',', '.') }}
                </span>
            @endif
        </div>

        <h4 class="text-xs text-gray-600 line-clamp-1 mb-2">{{ $vinyl->title }}</h4>
        
        <!-- Detalhes e características rápidas -->
        <div class="flex flex-wrap gap-1 mb-2">
            @if($vinyl->release_year)
                <span class="inline-block bg-gray-100 text-xs px-2 py-0.5 rounded">{{ $vinyl->release_year }}</span>
            @endif
            
            @if($vinyl->country)
                <span class="inline-block bg-gray-100 text-xs px-2 py-0.5 rounded">{{ $vinyl->country }}</span>
            @endif
            
            @if($vinyl->recordLabel)
                <span class="inline-block bg-gray-100 text-xs px-2 py-0.5 rounded">{{ $vinyl->recordLabel->name }}</span>
            @endif
        </div>

        <!-- Ações (opcionais) -->
        @if($showActions)
            <div class="flex justify-between items-center mt-4">
                <div class="flex items-center space-x-2">
                    <!-- Botões de Wishlist/Wantlist conforme disponibilidade -->
                    @if($vinyl->vinylSec && $vinyl->vinylSec->in_stock && $vinyl->vinylSec->stock > 0)
                        <!-- Botão de Wishlist para produtos disponíveis -->
                        <button 
                            wire:click="toggleWishlist"
                            class="{{ $inWishlist ? 'text-red-500' : 'text-gray-400' }} hover:text-red-600 transition-colors"
                            title="{{ $inWishlist ? 'Remover da wishlist' : 'Adicionar à wishlist' }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @else
                        <!-- Botão de Wantlist para produtos indisponíveis -->
                        <button 
                            wire:click="toggleWantlist"
                            class="{{ $inWantlist ? 'text-blue-500' : 'text-gray-400' }} hover:text-blue-600 transition-colors"
                            title="{{ $inWantlist ? 'Remover da wantlist' : 'Adicionar à wantlist' }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </button>
                    @endif
                </div>

                <!-- Botão de Carrinho apenas para produtos disponíveis -->
                @if($vinyl->vinylSec && $vinyl->vinylSec->in_stock && $vinyl->vinylSec->stock > 0)
                    @if($vinyl->product && $vinyl->product->price > 0)
                        <button 
                            wire:click="addToCart"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-xs flex items-center transition-colors duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                            </svg>
                            Adicionar
                        </button>
                    @else
                        <span class="text-xs text-gray-500 italic">
                            Consulte preço
                        </span>
                    @endif
                @else
                    <span class="text-xs text-gray-500 italic">
                        Indisponível
                    </span>
                @endif
            </div>
        @endif
    </div>
</div>
