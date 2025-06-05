@props(['vinyl', 'showActions' => true, 'size' => 'normal', 'orientation' => 'vertical', 'inWishlist' => false, 'inWantlist' => false])

<!-- vinyl-card.blade.php - Componente reutilizável para exibição de discos de vinil -->
<div
    class="max-w-sm mx-auto {{ $size === 'small' ? 'max-w-xs' : ($size === 'large' ? 'max-w-md' : '') }} {{ $orientation === 'horizontal' ? 'flex' : '' }}"
    x-data="vinylCard"
>
    <!-- Card Container -->
    <div class="overflow-hidden shadow-lg bg-white relative {{ $orientation === 'horizontal' ? 'flex' : '' }} hover:shadow-lg transition-shadow duration-300">
        <!-- Badges superiores com status do produto -->
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
            
            @if($vinyl->vinylSec->original_price > $vinyl->vinylSec->price)
                <div class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">
                    Oferta
                </div>
            @endif
        </div>

        <!-- Image Container com capa do vinil e botão de play -->
        <div class="relative {{ $orientation === 'horizontal' ? 'w-1/3' : 'aspect-square' }}">
            <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug, 'titleSlug' => $vinyl->slug]) }}" class="block w-full h-full">
                <img
                    src="{{ asset('storage/' . $vinyl->cover_image) }}"
                    alt="{{ $vinyl->title }} by {{ $vinyl->artists->pluck('name')->implode(', ') }}"
                    class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300"
                    onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'"
                />
            </a>

            <!-- Overlay com botão de play apenas se tiver faixas com áudio -->
            @if($vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }))
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

        <!-- Informações do Produto -->
        <div class="p-4 {{ $orientation === 'horizontal' ? 'w-2/3 flex flex-col justify-between' : '' }}">
            <!-- Artista e Título -->
            <div class="{{ $size === 'small' ? 'mb-1' : 'mb-2' }}">
                <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug, 'titleSlug' => $vinyl->slug]) }}" class="block">
                    <h5 class="{{ $size === 'small' ? 'text-sm' : 'text-base' }} font-bold tracking-tight text-gray-900 hover:text-purple-700 transition-colors duration-300 line-clamp-1">
                        {{ $vinyl->artists->pluck('name')->implode(', ') }}
                    </h5>
                    <p class="{{ $size === 'small' ? 'text-xs' : 'text-sm' }} text-gray-700 line-clamp-1 mt-0.5">
                        {{ $vinyl->title }}
                    </p>
                </a>
                <!-- Informações adicionais -->
                <div class="flex items-center gap-1 mt-1">
                    <p class="text-xs text-gray-500">{{ $vinyl->release_year }}</p>
                    <span class="text-xs text-gray-400">&bull;</span>
                    <p class="text-xs text-gray-500 line-clamp-1">{{ $vinyl->recordLabel->name }}</p>
                </div>
            </div>

            <!-- Preço e Disponibilidade -->
            <div class="flex justify-between items-end">
                <div>
                    @if($vinyl->vinylSec->original_price > $vinyl->vinylSec->price)
                        <p class="text-xs text-gray-500 line-through mb-0.5">R$ {{ number_format($vinyl->vinylSec->original_price, 2, ',', '.') }}</p>
                    @endif
                    <p class="{{ $size === 'small' ? 'text-base' : 'text-xl' }} font-bold {{ $vinyl->vinylSec->original_price > $vinyl->vinylSec->price ? 'text-red-600' : 'text-gray-900' }}">
                        R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                    </p>
                </div>
                <!-- Disponibilidade como tag -->
                <div class="flex items-center">
                    @if($vinyl->vinylSec->stock > 0)
                        <span class="inline-flex items-center text-xs text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Em estoque
                        </span>
                    @else
                        <span class="inline-flex items-center text-xs text-red-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            Esgotado
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if($showActions)
        <!-- Barra de ações - Wishlist/Wantlist, Play e Carrinho com proporção 20-20-60 -->
        <div class="flex w-full divide-x divide-gray-100 {{ $orientation === 'horizontal' ? 'absolute bottom-0 left-0 right-0' : '' }}">
            <!-- Botão de Wishlist/Wantlist (20%) - Implementação híbrida -->
            <div class="w-[20%]">
                @if($vinyl->vinylSec && $vinyl->vinylSec->in_stock && $vinyl->vinylSec->stock > 0)
                    <!-- Botão de Wishlist para produtos disponíveis -->
                    <button
                        type="button"
                        class="w-full h-full bg-gray-100 py-3 flex items-center justify-center hover:bg-gray-200 transition-colors duration-300 relative"
                        onclick="toggleWishlist('{{ $vinyl->id }}', this)"
                        data-vinyl-id="{{ $vinyl->id }}"
                        data-wishlist-id="{{ $vinyl->id }}"
                        title="Adicionar à lista de desejos"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" 
                             class="wishlist-icon w-5 h-5 {{ $inWishlist ? 'text-red-600' : 'text-gray-700' }} transition-colors duration-300" 
                             viewBox="0 0 24 24" 
                             fill="{{ $inWishlist ? 'currentColor' : 'none' }}" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             stroke-linecap="round" 
                             stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                        </svg>
                    </button>
                @else
                    <!-- Botão de Wantlist para produtos indisponíveis -->
                    <button
                        type="button"
                        class="w-full h-full bg-gray-100 py-3 flex items-center justify-center hover:bg-gray-200 transition-colors duration-300 relative"
                        onclick="toggleWantlist('{{ $vinyl->id }}', this)"
                        data-vinyl-id="{{ $vinyl->id }}"
                        data-wantlist-id="{{ $vinyl->id }}"
                        title="Adicionar à lista de interesse"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" 
                             class="wantlist-icon w-5 h-5 {{ $inWantlist ? 'text-purple-600' : 'text-gray-700' }} transition-colors duration-300" 
                             viewBox="0 0 24 24" 
                             fill="{{ $inWantlist ? 'currentColor' : 'none' }}" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             stroke-linecap="round" 
                             stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                        </svg>
                    </button>
                @endif
            </div>
            
            <!-- Botão de Play (20%) - Somente se tiver áudio disponível -->
            <button 
                type="button"
                class="w-[20%] {{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? 'bg-gray-100 hover:bg-gray-200' : 'bg-gray-100 opacity-50 cursor-not-allowed' }} p-3 flex items-center justify-center transition-colors duration-300"
                x-on:click="playAudio"
                {{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? '' : 'disabled' }}
                title="{{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? 'Ouvir amostra' : 'Áudio não disponível' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 {{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? 'text-blue-600' : 'text-gray-400' }}" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8 5.14v14l11-7-11-7z" />
                </svg>
            </button>
            
            <!-- Botão de Carrinho (60%) - Adaptado para produtos disponíveis/indisponíveis -->
            @if($vinyl->vinylSec && $vinyl->vinylSec->in_stock && $vinyl->vinylSec->stock > 0)
                <button
                    type="button"
                    class="w-[60%] bg-purple-600 text-white py-3 flex items-center justify-center hover:bg-purple-700 transition-colors duration-300"
                    x-on:click="addToCart($event)"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1" />
                        <circle cx="20" cy="21" r="1" />
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                    </svg>
                    <span class="font-medium {{ $size === 'small' ? 'text-sm' : '' }}">{{ $size === 'small' ? 'Comprar' : 'Comprar' }}</span>
                </button>
            @else
                <button
                    type="button"
                    class="w-[60%] bg-gray-600 text-white py-3 flex items-center justify-center transition-colors duration-300 cursor-not-allowed"
                    disabled
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 16.7a4 4 0 0 1-4 4H4v-4h16.7a4 4 0 0 1-.7 0z"/>
                        <path d="M16 8a2 2 0 0 1 2 2v2h-2V8z"/>
                        <path d="M12 8a2 2 0 0 1 2 2v2h-2V8z"/>
                        <path d="M8 8a2 2 0 0 1 2 2v2H8V8z"/>
                        <path d="M4 8a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v2H4V8z"/>
                        <path d="M20 12V8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v4"/>
                    </svg>
                    <span class="font-medium {{ $size === 'small' ? 'text-sm' : '' }}">Indisponível</span>
                </button>
            @endif
        </div>
        @endif
    </div>
</div>




