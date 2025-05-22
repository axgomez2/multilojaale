<x-app-layout>
<div class="bg-gray-50 min-h-screen">
    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho da página -->
            <div class="bg-slate-900 rounded-lg shadow-md p-6 mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">{{ $title }}</h1>
                <p class="text-gray-300">{{ $description }}</p>
                @if(isset($category))
                    <div class="mt-4 bg-yellow-500 text-slate-900 inline-block px-3 py-1 rounded-full font-medium">
                        Categoria: {{ $category->nome }}
                    </div>
                @endif
            </div>

            @if($vinyls->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-4 text-xl font-medium text-gray-900">Nenhum disco encontrado</h3>
                    <p class="mt-2 text-gray-500">Não encontramos nenhum disco de vinil nesta categoria no momento.</p>
                    <a href="{{ route('site.products') }}" class="mt-4 inline-block bg-yellow-400 hover:bg-yellow-500 text-slate-900 font-bold py-2 px-4 rounded">
                        Ver todos os discos
                    </a>
                </div>
            @else
                <!-- Grade de produtos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($vinyls as $vinyl)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <!-- Imagem do produto -->
                            <div class="relative">
                                @if($vinyl->images->count() > 0)
                                    <img src="{{ asset('storage/' . $vinyl->images->first()->path) }}" 
                                        alt="{{ $vinyl->title }}" 
                                        class="w-full h-64 object-cover">
                                @else
                                    <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Status do vinil (se aplicável) -->
                                @if($vinyl->vinylSec && $vinyl->vinylSec->in_stock && $vinyl->is_available)
                                    <div class="absolute top-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">
                                        Disponível
                                    </div>
                                @elseif($vinyl->vinylSec && !$vinyl->vinylSec->in_stock)
                                    <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                        Esgotado
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Informações do produto -->
                            <div class="p-4">
                                <h3 class="text-lg font-bold text-gray-900 truncate">{{ $vinyl->title }}</h3>
                                
                                @if($vinyl->artists->count() > 0)
                                    <p class="text-sm text-gray-600 mb-2">
                                        {{ $vinyl->artists->pluck('name')->join(', ') }}
                                    </p>
                                @endif
                                
                                @if($vinyl->vinylSec && $vinyl->vinylSec->price > 0)
                                    <p class="text-xl font-bold text-slate-900 mt-2">
                                        R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                                    </p>
                                @endif
                                
                                <!-- Botões de ação -->
                                <div class="mt-4 flex justify-between items-center">
                                    <a href="#" class="text-slate-700 hover:text-yellow-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </a>
                                    
                                    @if($vinyl->is_available)
                                        <button class="bg-yellow-400 hover:bg-yellow-500 text-slate-900 font-medium py-1 px-3 rounded text-sm">
                                            Adicionar
                                        </button>
                                    @else
                                        <button disabled class="bg-gray-300 text-gray-500 font-medium py-1 px-3 rounded text-sm cursor-not-allowed">
                                            Indisponível
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Paginação -->
                <div class="mt-8">
                    {{ $vinyls->links() }}
                </div>
            @endif
        </div>
    </main>
</div>
</x-app-layout>
