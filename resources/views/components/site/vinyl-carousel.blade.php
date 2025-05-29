@props([
    'title',            // Título do carrossel
    'vinyls',           // Coleção de vinis a serem exibidos
    'slug' => null,     // Slug da categoria (opcional)
    'limit' => 10,      // Limite de discos
    'wishlistItems' => [],  // IDs dos itens na wishlist
    'wantlistItems' => []   // IDs dos itens na wantlist
])

<div x-data="{ 
    scroll: 0, 
    totalItems: {{ min($vinyls->count(), $limit) }}, 
    visibleItems: {{ min(4, $vinyls->count()) }}
}" class="relative mb-12">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-white" style="font-family: 'Montserrat', 'Inter', 'Arial', sans-serif;">{{ $title }}</h2>
        
        @if($slug)
            <a href="{{ route('site.category', $slug) }}" class="text-sm text-indigo-400 hover:text-indigo-300 flex items-center">
                Ver todos
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @endif
    </div>

    <div class="relative">
        <!-- Botão de navegação esquerda -->
        <button @click="scroll = Math.max(0, scroll - 1)" 
                class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-black bg-opacity-50 hover:bg-opacity-70 text-white rounded-full p-2 focus:outline-none"
                :class="{'opacity-50 cursor-not-allowed': scroll <= 0, 'opacity-100 cursor-pointer': scroll > 0}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        
        <!-- Carrossel -->
        <div class="overflow-hidden">
            <div class="flex space-x-4 pb-4 transition-transform duration-300 ease-in-out" 
                 :style="`transform: translateX(-${scroll * 240}px)`">
                @foreach($vinyls->take($limit) as $vinyl)
                    <div class="flex-shrink-0 w-56">
                        <x-site.vinyl-card :vinyl="$vinyl" size="small"
                            :inWishlist="in_array($vinyl->id, is_array($wishlistItems) ? $wishlistItems : ($wishlistItems ? $wishlistItems->toArray() : []))"
                            :inWantlist="in_array($vinyl->id, is_array($wantlistItems) ? $wantlistItems : ($wantlistItems ? $wantlistItems->toArray() : []))" />
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Botão de navegação direita -->
        <button @click="scroll = Math.min(totalItems - visibleItems, scroll + 1)" 
                class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-black bg-opacity-50 hover:bg-opacity-70 text-white rounded-full p-2 focus:outline-none"
                :class="{'opacity-50 cursor-not-allowed': scroll >= totalItems - visibleItems, 'opacity-100 cursor-pointer': scroll < totalItems - visibleItems}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>
</div>
