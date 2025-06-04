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
                <!-- Grade de produtos com responsividade: 5 colunas em xl, 4 em lg, 2 em sm, 1 em xs -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach($vinyls as $vinyl)
                        <div class="flex justify-center">
                            <x-site.vinyl-card 
                                :vinyl="$vinyl" 
                                :showActions="true" 
                                :size="'normal'" 
                                :orientation="'vertical'" 
                                :inWishlist="false" 
                                :inWantlist="false" 
                            />
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
