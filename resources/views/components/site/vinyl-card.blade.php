@props([
    'vinyl',             // Objeto VinylMaster completo
    'showActions' => true,    // Mostrar botões de ação (adicionar ao carrinho, favoritos)
    'size' => 'normal',       // Tamanho do card: 'small', 'normal', 'large'
    'orientation' => 'vertical' // Orientação: 'vertical', 'horizontal'
])

@php
    // Determinar classes com base no tamanho
    $containerClasses = match($size) {
        'small' => 'max-w-xs',
        'large' => 'max-w-md',
        default => 'max-w-sm'
    };
    
    // Determinar altura da imagem com base no tamanho
    $imgHeight = match($size) {
        'small' => 'h-48',
        'large' => 'h-80',
        default => 'h-64'
    };
    
    // Verificar se o vinil está disponível
    $isAvailable = $vinyl->is_available ?? 
                  ($vinyl->vinylSec && 
                   $vinyl->vinylSec->in_stock && 
                   $vinyl->vinylSec->price > 0);
                   
    // Determinar a estrutura do layout baseado na orientação
    $isHorizontal = $orientation === 'horizontal';
@endphp

@php
    // Obter o artista principal e o título para construir o slug
    $artistName = isset($vinyl->artists) && $vinyl->artists->count() > 0 ? $vinyl->artists->first()->name : 'artista';
    $artistSlug = \Illuminate\Support\Str::slug($artistName);
    $titleSlug = \Illuminate\Support\Str::slug($vinyl->title ?? 'disco');
@endphp

<a href="{{ route('site.vinyl.show', ['artistSlug' => $artistSlug, 'titleSlug' => $titleSlug]) }}" 
   class="block">
    <div @class([
        'bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col',
        $containerClasses,
        'flex-row' => $isHorizontal
    ])>
    <!-- Imagem do produto -->
    <div @class([
        'relative', 
        'w-1/3' => $isHorizontal,
        'w-full' => !$isHorizontal
    ])>
        @if(isset($vinyl->cover_image) && !empty($vinyl->cover_image))
            <img src="{{ asset('storage/' . $vinyl->cover_image) }}" 
                alt="{{ $vinyl->title ?? 'Disco de Vinil' }}" 
                @class([
                    'object-cover', 
                    $imgHeight,
                    'w-full' => !$isHorizontal,
                    'h-full w-full' => $isHorizontal
                ])>
        @elseif(isset($vinyl->images) && $vinyl->images !== null && $vinyl->images->count() > 0)
            <img src="{{ asset('storage/' . $vinyl->images->first()->path) }}" 
                alt="{{ $vinyl->title ?? 'Disco de Vinil' }}" 
                @class([
                    'object-cover', 
                    $imgHeight,
                    'w-full' => !$isHorizontal,
                    'h-full w-full' => $isHorizontal
                ])>
        @else
            <div @class([
                'bg-gray-200 flex items-center justify-center', 
                $imgHeight,
                'w-full' => !$isHorizontal,
                'h-full w-full' => $isHorizontal
            ])>
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif
        
        <!-- Status do vinil (se aplicável) -->
        @if($isAvailable)
            <div class="absolute top-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">
                Disponível
            </div>
        @elseif(isset($vinyl->vinylSec) && $vinyl->vinylSec && !$vinyl->vinylSec->in_stock)
            <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                Esgotado
            </div>
        @endif
    </div>
    
    <!-- Conteúdo do card -->
    <div @class([
        'p-4 pb-0',
        'w-2/3' => $isHorizontal,
        'w-full' => !$isHorizontal
    ])>
        <!-- Artista primeiro (conforme solicitado) -->
        @if(isset($vinyl->artists) && $vinyl->artists !== null && $vinyl->artists->count() > 0)
            <p class="font-medium text-slate-700 mb-1">
                {{ $vinyl->artists->pluck('name')->join(', ') }}
            </p>
        @endif
        
        <!-- Título do disco com fonte menor -->
        <h3 @class([
            'font-bold text-gray-900 truncate mb-2',
            'text-xs' => $size === 'small',
            'text-sm' => $size === 'normal',
            'text-base' => $size === 'large'
        ])>
            {{ $vinyl->title ?? 'Disco de Vinil' }}
        </h3>
        
        <!-- Preço com tamanho reduzido -->
        @if(isset($vinyl->vinylSec) && $vinyl->vinylSec && isset($vinyl->vinylSec->price) && $vinyl->vinylSec->price > 0)
            <p @class([
                'font-bold text-slate-900 mt-1',
                'text-sm' => $size === 'small',
                'text-base' => $size === 'normal',
                'text-lg' => $size === 'large'
            ])>
                R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
            </p>
        @endif
        
        <!-- Barra de navegação inferior com botões de ação -->
        @if($showActions)
            <div class="mt-auto -mx-4 flex w-auto overflow-hidden border-t border-gray-200" style="height: 40px; margin-bottom: -1px; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem;">
                @if($isAvailable)
                    <!-- Botão de favoritos (20%) -->
                    <button 
                        type="button"
                        @click="$dispatch('toggle-wishlist', {id: {{ $vinyl->id ?? 0 }}});"
                        class="flex items-center justify-center w-1/5 h-full bg-gray-100 hover:bg-gray-200 transition-colors"
                        title="Adicionar à lista de desejos"
                    >
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                    
                    <!-- Botão de play (20%) -->
                    <button 
                        type="button"
                        @click="$dispatch('play-preview', {id: {{ $vinyl->id ?? 0 }}});"
                        class="flex items-center justify-center w-1/5 h-full bg-gray-100 hover:bg-gray-200 transition-colors"
                        title="Ouvir prévia"
                    >
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                    
                    <!-- Botão de adicionar ao carrinho (60%) -->
                    <button 
                        type="button"
                        @click="$dispatch('add-to-cart', {id: {{ $vinyl->id ?? 0 }}});"
                        class="flex items-center justify-center w-3/5 h-full bg-yellow-400 hover:bg-yellow-500 text-slate-900 font-medium transition-colors"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="text-xs">Adicionar</span>
                    </button>
                @else
                    <!-- Botão de Wantlist (20%) -->
                    <button 
                        type="button"
                        @click="$dispatch('add-to-wantlist', {id: {{ $vinyl->id ?? 0 }}});"
                        class="flex items-center justify-center w-1/5 h-full bg-gray-100 hover:bg-gray-200 transition-colors"
                        title="Adicionar à lista de procura"
                    >
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                    
                    <!-- Botão de play (20%) -->
                    <button 
                        type="button"
                        @click="$dispatch('play-preview', {id: {{ $vinyl->id ?? 0 }}});"
                        class="flex items-center justify-center w-1/5 h-full bg-gray-100 hover:bg-gray-200 transition-colors"
                        title="Ouvir prévia"
                    >
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                    
                    <!-- Botão de avise-me (60%) -->
                    <button 
                        type="button"
                        @click="$dispatch('notify-available', {id: {{ $vinyl->id ?? 0 }}});"
                        class="flex items-center justify-center w-3/5 h-full bg-slate-700 hover:bg-slate-800 text-white font-medium transition-colors"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="text-xs">Avise-me</span>
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
</a>
