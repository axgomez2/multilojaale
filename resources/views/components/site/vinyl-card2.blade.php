@props(['vinyl', 'showActions' => true, 'size' => 'normal', 'orientation' => 'vertical'])

{{-- vinyl-card.blade.php - Componente reutilizável para exibição de discos de vinil --}}
<div
    class="group relative max-w-sm mx-auto 
           {{ $size === 'small' ? 'max-w-xs' : ($size === 'large' ? 'max-w-md' : '') }} 
           {{ $orientation === 'horizontal' ? 'flex max-w-2xl' : '' }}"
    x-data="vinylCard"
    x-init="
        vinylId = '{{ $vinyl->id }}'; 
        vinylTitle = @js($vinyl->title); 
        vinylArtist = @js($vinyl->artists->pluck('name')->implode(', ')); 
        vinylCover = '{{ asset('storage/' . $vinyl->cover_image) }}'
    "
>
    {{-- Card Container com melhor responsividade --}}
    <div class="relative overflow-hidden rounded-xl bg-white shadow-lg border border-gray-100
                {{ $orientation === 'horizontal' ? 'flex' : '' }} 
                hover:shadow-2xl hover:border-purple-200 
                transition-all duration-500 ease-out
                transform hover:-translate-y-2">
        
        {{-- Badges superiores com melhor design --}}
        <div class="absolute top-3 left-3 z-20 flex flex-col gap-2">
            @if($vinyl->isAvailable())
                <div class="inline-flex items-center gap-1 bg-emerald-500/90 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow-lg border border-emerald-400/20">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Disponível
                </div>
            @else
                <div class="inline-flex items-center gap-1 bg-red-500/90 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow-lg border border-red-400/20">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Esgotado
                </div>
            @endif
            
            @if($vinyl->vinylSec->original_price > $vinyl->vinylSec->price)
                <div class="inline-flex items-center gap-1 bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow-lg border border-orange-400/20 animate-pulse">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Oferta
                </div>
            @endif

            {{-- Badge de estoque baixo --}}
            @if($vinyl->vinylSec->stock > 0 && $vinyl->vinylSec->stock <= 3)
                <div class="inline-flex items-center gap-1 bg-amber-500/90 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow-lg border border-amber-400/20">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Últimas {{ $vinyl->vinylSec->stock }}
                </div>
            @endif
        </div>

        {{-- Favorito - Botão flutuante no canto superior direito --}}
        <div class="absolute top-3 right-3 z-20">
            @if($vinyl->isAvailable())
                <button
                    type="button"
                    class="w-10 h-10 rounded-full bg-white/90 backdrop-blur-sm shadow-lg border border-gray-200/50 
                           flex items-center justify-center hover:bg-white hover:scale-110 
                           transition-all duration-300 group/heart"
                    onclick="toggleWishlist('{{ $vinyl->id }}', this)"
                    data-vinyl-id="{{ $vinyl->id }}"
                    title="Adicionar à lista de desejos"
                >
                    <svg class="w-5 h-5 text-gray-600 group-hover/heart:text-red-500 transition-colors duration-300" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            @else
                <button
                    type="button"
                    class="w-10 h-10 rounded-full bg-white/90 backdrop-blur-sm shadow-lg border border-gray-200/50 
                           flex items-center justify-center hover:bg-white hover:scale-110 
                           transition-all duration-300 group/bell"
                    onclick="toggleWantlist('{{ $vinyl->id }}', this)"
                    data-vinyl-id="{{ $vinyl->id }}"
                    title="Adicionar à lista de interesse"
                >
                    <svg class="w-5 h-5 text-gray-600 group-hover/bell:text-blue-500 transition-colors duration-300" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>
            @endif
        </div>

        {{-- Image Container com efeitos visuais melhorados --}}
        <div class="relative {{ $orientation === 'horizontal' ? 'w-2/5 flex-shrink-0' : 'aspect-square' }} overflow-hidden">
            <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug, 'titleSlug' => $vinyl->slug]) }}" 
               class="block w-full h-full relative">
                
                {{-- Gradient overlay sutil --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-10"></div>
                
                <img
                    src="{{ asset('storage/' . $vinyl->cover_image) }}"
                    alt="{{ $vinyl->title }} by {{ $vinyl->artists->pluck('name')->implode(', ') }}"
                    class="w-full h-full object-cover object-center group-hover:scale-110 transition-transform duration-700 ease-out"
                    onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'"
                    loading="lazy"
                />
            </a>

            {{-- Botão de play centralizado com melhor design --}}
            @if($vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }))
            <button
                type="button"
                class="absolute inset-0 flex items-center justify-center bg-black/0 hover:bg-black/40 
                       opacity-0 group-hover:opacity-100 transition-all duration-500 ease-out z-20"
                x-on:click="playAudio"
                title="Ouvir amostra"
            >
                <div class="w-16 h-16 rounded-full bg-white/95 backdrop-blur-sm hover:bg-white 
                            flex items-center justify-center transition-all duration-300 
                            transform hover:scale-110 shadow-2xl border border-white/20
                            animate-pulse hover:animate-none">
                    <svg class="w-8 h-8 text-purple-600 ml-1 transition-colors duration-300" 
                         fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5.14v14l11-7-11-7z"/>
                    </svg>
                </div>
            </button>
            @endif
        </div>

        {{-- Informações do Produto com melhor tipografia --}}
        <div class="p-5 {{ $orientation === 'horizontal' ? 'w-3/5 flex flex-col justify-between' : '' }} space-y-3">
            {{-- Artista e Título com melhor hierarquia --}}
            <div class="space-y-2">
                <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug, 'titleSlug' => $vinyl->slug]) }}" 
                   class="block group/link">
                    <h3 class="{{ $size === 'small' ? 'text-lg' : 'text-xl' }} font-bold tracking-tight text-gray-900 
                               group-hover/link:text-purple-700 transition-colors duration-300 line-clamp-1 mb-1">
                        {{ $vinyl->artists->pluck('name')->implode(', ') }}
                    </h3>
                    <p class="{{ $size === 'small' ? 'text-sm' : 'text-base' }} text-gray-600 line-clamp-2 font-medium">
                        {{ $vinyl->title }}
                    </p>
                </a>
                
                {{-- Informações adicionais com melhor apresentação --}}
                <div class="flex items-center gap-3 text-sm text-gray-500">
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        {{ $vinyl->release_year }}
                    </div>
                    <div class="w-1 h-1 bg-gray-400 rounded-full"></div>
                    <div class="flex items-center gap-1 line-clamp-1">
                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $vinyl->recordLabel->name }}
                    </div>
                </div>
            </div>

            {{-- Preço com melhor apresentação --}}
            <div class="flex justify-between items-end">
                <div class="space-y-1">
                    @if($vinyl->vinylSec->original_price > $vinyl->vinylSec->price)
                        <div class="flex items-center gap-2">
                            <p class="text-sm text-gray-400 line-through">
                                R$ {{ number_format($vinyl->vinylSec->original_price, 2, ',', '.') }}
                            </p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                -{{ round((($vinyl->vinylSec->original_price - $vinyl->vinylSec->price) / $vinyl->vinylSec->original_price) * 100) }}%
                            </span>
                        </div>
                    @endif
                    <p class="{{ $size === 'small' ? 'text-xl' : 'text-2xl' }} font-bold 
                              {{ $vinyl->vinylSec->original_price > $vinyl->vinylSec->price ? 'text-red-600' : 'text-gray-900' }}">
                        R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                    </p>
                </div>
                
                {{-- Status de estoque melhorado --}}
                <div class="text-right">
                    @if($vinyl->vinylSec->stock > 0)
                        <div class="inline-flex items-center gap-1 text-sm text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full">
                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                            Em estoque
                        </div>
                        @if($vinyl->vinylSec->stock <= 10)
                            <p class="text-xs text-amber-600 mt-1">Apenas {{ $vinyl->vinylSec->stock }} restantes</p>
                        @endif
                    @else
                        <div class="inline-flex items-center gap-1 text-sm text-red-700 bg-red-50 px-3 py-1 rounded-full">
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                            Esgotado
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Barra de ações redesenhada --}}
        @if($showActions)
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-white via-white to-transparent 
                    {{ $orientation === 'horizontal' ? 'relative' : '' }}">
            <div class="flex p-4 gap-3">
                {{-- Botão de Play --}}
                @if($vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }))
                <button 
                    type="button"
                    class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-600 hover:bg-blue-700 
                           text-white flex items-center justify-center transition-all duration-300 
                           hover:scale-110 shadow-lg hover:shadow-xl"
                    x-on:click="playAudio"
                    title="Ouvir amostra"
                >
                    <svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5.14v14l11-7-11-7z"/>
                    </svg>
                </button>
                @endif
                
                {{-- Botão principal de ação --}}
                @if($vinyl->isAvailable())
                    <button
                        type="button"
                        class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 
                               text-white py-3 px-6 rounded-xl font-semibold text-sm
                               flex items-center justify-center gap-2 transition-all duration-300 
                               hover:shadow-lg hover:scale-[1.02] transform"
                        x-on:click="addToCart($event)"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5L12 21l7.5-3L17 13"/>
                        </svg>
                        {{ $size === 'small' ? 'Comprar' : 'Adicionar ao Carrinho' }}
                    </button>
                @else
                    <button
                        type="button"
                        class="flex-1 bg-gray-400 text-white py-3 px-6 rounded-xl font-semibold text-sm
                               flex items-center justify-center gap-2 cursor-not-allowed"
                        disabled
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636"/>
                        </svg>
                        Indisponível
                    </button>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>