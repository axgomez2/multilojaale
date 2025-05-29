<button
    wire:click="toggle"
    wire:loading.attr="disabled"
    class="w-full h-full bg-gray-100 py-3 flex items-center justify-center hover:bg-gray-200 transition-colors duration-300 relative"
    title="{{ $isAvailable ? ($inWishlist ? 'Remover da lista de desejos' : 'Adicionar à lista de desejos') : ($inWantlist ? 'Remover da lista de interesse' : 'Adicionar à lista de interesse') }}"
>
    <div wire:loading class="absolute inset-0 bg-gray-200 bg-opacity-50 flex items-center justify-center">
        <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
    
    @if($isAvailable)
        <!-- Ícone de coração para wishlist (produtos disponíveis) -->
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="w-5 h-5 {{ $inWishlist ? 'text-red-500' : 'text-gray-700' }} transition-colors duration-300" 
             viewBox="0 0 24 24" 
             fill="{{ $inWishlist ? 'currentColor' : 'none' }}" 
             stroke="currentColor" 
             stroke-width="2" 
             stroke-linecap="round" 
             stroke-linejoin="round">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
        </svg>
    @else
        <!-- Ícone de sino para wantlist (produtos indisponíveis) -->
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="w-5 h-5 {{ $inWantlist ? 'text-purple-500' : 'text-gray-700' }} transition-colors duration-300" 
             viewBox="0 0 24 24" 
             fill="{{ $inWantlist ? 'currentColor' : 'none' }}" 
             stroke="currentColor" 
             stroke-width="2" 
             stroke-linecap="round" 
             stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
        </svg>
    @endif
</button>
