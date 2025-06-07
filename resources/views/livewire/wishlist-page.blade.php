<div class="container mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-6">Minha Lista de Desejos</h1>
    
    @if(count($wishlistItems) > 0)
        <div class="mb-6 flex justify-between items-center">
            <p class="text-gray-600">{{ count($wishlistItems) }} {{ count($wishlistItems) == 1 ? 'item' : 'itens' }} na sua lista de desejos</p>
            <button 
                wire:click="addAllToCart"
                class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition-colors"
            >
                Adicionar todos ao carrinho
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            @foreach($wishlistItems as $item)
                <div class="bg-white rounded-lg shadow-md overflow-hidden relative">
                    <div class="absolute top-2 right-2 z-10">
                        <button
                            wire:click="removeItem('{{ $item->vinyl_master_id }}')"
                            class="bg-red-500 text-white p-1 rounded-full hover:bg-red-600 transition-colors"
                            title="Remover da lista de desejos"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="relative">
                        <a href="{{ route('site.vinyl.show', [$item->vinylMaster->artists->first()->slug, $item->vinylMaster->slug]) }}">
                            <div class="aspect-square overflow-hidden">
                                <img 
                                    src="{{ asset('storage/' . $item->vinylMaster->cover_image) }}"
                                    alt="{{ $item->vinylMaster->title }}"
                                    class="w-full h-full object-cover object-center"
                                    onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'"
                                >
                            </div>
                            
                            <div class="p-4">
                                <h3 class="text-lg font-semibold truncate">{{ $item->vinylMaster->title }}</h3>
                                <p class="text-gray-600 truncate">{{ $item->vinylMaster->artists->first()->name }}</p>
                                
                                @if($item->vinylMaster->vinylSec && $item->vinylMaster->vinylSec->stock > 0)
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-green-600 font-bold">R$ {{ number_format($item->vinylMaster->vinylSec->price, 2, ',', '.') }}</span>
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <span class="text-red-600">Indisponível no momento</span>
                                    </div>
                                @endif
                            </div>
                        </a>
                        
                        @if($item->vinylMaster->vinylSec && $item->vinylMaster->vinylSec->stock > 0)
                            <div class="absolute bottom-4 right-4">
                                <button
                                    wire:click.stop="addItemToCart('{{ $item->vinyl_master_id }}')" 
                                    class="bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700 transition-colors"
                                >
                                    Adicionar ao carrinho
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-gray-50 rounded-lg p-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <h2 class="text-2xl font-bold mb-2">Sua lista de desejos está vazia</h2>
            <p class="text-gray-600 mb-6">Adicione discos que você deseja comprar à sua lista de desejos.</p>
            <a href="{{ route('home') }}" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition-colors inline-block">Explorar discos</a>
        </div>
    @endif
</div>
