<div class="container mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-6">Minha Lista de Interesse</h1>
    
    @if(count($wantlistItems) > 0)
        <div class="mb-6">
            <p class="text-gray-600">{{ count($wantlistItems) }} {{ count($wantlistItems) == 1 ? 'item' : 'itens' }} na sua lista de interesse</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($wantlistItems as $item)
                <div class="bg-white rounded-lg shadow-md overflow-hidden relative">
                    <div class="absolute top-2 right-2 z-10">
                        <button
                            wire:click="removeItem('{{ $item->vinyl_master_id }}')"
                            class="bg-red-500 text-white p-1 rounded-full hover:bg-red-600 transition-colors"
                            title="Remover da lista de interesse"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    
                    <a href="{{ route('site.vinyl.show', [$item->vinylMaster->artists->first()->slug, $item->vinylMaster->slug]) }}">
                        <img 
                            src="{{ $item->vinylMaster->cover_image }}"
                            alt="{{ $item->vinylMaster->title }}"
                            class="w-full h-48 object-cover"
                        >
                        
                        <div class="p-4">
                            <h3 class="text-lg font-semibold truncate">{{ $item->vinylMaster->title }}</h3>
                            <p class="text-gray-600 truncate">{{ $item->vinylMaster->artists->first()->name }}</p>
                            
                            <div class="mt-4 flex items-center">
                                <button
                                    wire:click="toggleNotification('{{ $item->vinyl_master_id }}')"
                                    class="flex items-center {{ $item->notification_sent ? 'text-green-600' : 'text-gray-600' }} hover:text-green-700 transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                    </svg>
                                    {{ $item->notification_sent ? 'Notificação ativada' : 'Ativar notificação' }}
                                </button>
                            </div>
                            
                            <div class="mt-2">
                                <span class="text-amber-600 font-medium">Indisponível no momento</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-gray-50 rounded-lg p-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <h2 class="text-2xl font-bold mb-2">Sua lista de interesse está vazia</h2>
            <p class="text-gray-600 mb-6">Adicione discos indisponíveis que você tem interesse em comprar quando estiverem disponíveis.</p>
            <a href="{{ route('home') }}" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition-colors inline-block">Explorar discos</a>
        </div>
    @endif
</div>
