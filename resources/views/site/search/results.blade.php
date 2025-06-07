<x-app-layout>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-stone-900 mb-8">Resultados da busca: <span class="bg-yellow-200 px-1">{{ $query }}</span></h1>
    
    <div class="bg-white shadow-md rounded-md p-6 mb-6">
        <p class="text-stone-700">
            Encontramos <strong>{{ $vinyls->total() }}</strong> resultados para a sua pesquisa.
        </p>
    </div>
    

    <!-- <div class="w-full bg-slate-800">
    @foreach($vinyls as $vinyl)
    <div class="flex flex-col md:flex-row bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
    
    <div class="flex-shrink-0 flex justify-center items-center  md:w-40 md:h-40 w-full aspect-square">
        <img class="object-cover w-full h-full p-1" src="{{ asset('storage/' . $vinyl->cover_image) }}" alt="Nome do disco">
    </div>
    
    <div class="flex flex-col justify-center border-l border-gray-400 flex-1 px-4 py-2">
        <span class="text-2xl md:text-3xl font-bold leading-tight text-stone-900">{{ $vinyl->title }}</span>
        <span class="text-xl md:text-2xl font-semibold text-stone-900">{{ $vinyl->artists->pluck('name')->implode(', ') }}</span>
        <span class="text-base text-stone-900">{{ $vinyl->release_year }} - {{ $vinyl->recordLabel->name }}</span>
        <span class="text-base mt-2 text-stone-900">badge para categorias aqui</span>
    </div>
   
    <div class="flex items-center justify-center md:w-80 px-2">
        <span class="text-xl md:text-2xl   text-stone-900">R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}</span>
    </div>
    
    <div class="flex md:flex-col flex-row md:w-28 w-full">
        <a href="#" class="flex-1 flex items-center justify-center text-black font-bold text-base md:text-lg bg-yellow-300 hover:bg-yellow-400 transition-colors">OUVIR</a>
        <a href="#" class="flex-1 flex items-center justify-center text-black font-bold text-base md:text-lg bg-red-500 hover:bg-red-600 transition-colors">FAVORITOS</a>
        <a href="#" class="flex-1 flex items-center justify-center text-black font-bold text-base md:text-lg bg-yellow-300 hover:bg-yellow-400 transition-colors">COMPRAR</a>
    </div>
</div>
    @endforeach
    </div> -->




    @if($vinyls->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 text-stone-900 rounded-md p-6 ">
            <p>Nenhum resultado encontrado para <span class="bg-yellow-200 px-1">{{ $query }}</span>.</p>
            <p class="mt-2">Sugestões:</p>
            <ul class="list-disc list-inside mt-2">
                <li>Verifique a ortografia das palavras</li>
                <li>Tente utilizar palavras mais gerais</li>
                <li>Tente buscar por artistas, gêneros ou títulos populares</li>
            </ul>
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-5 mb-8 mt-20">
            @foreach($vinyls as $vinyl)
                <div class="flex justify-center">
                    <x-site.vinyl-card 
                        :vinyl="$vinyl" 
                        :highlightText="$query"
                        :showActions="true" 
                        :size="'normal'" 
                        :orientation="'vertical'" 
                        :inWishlist="auth()->check() && in_array($vinyl->id, $wishlistItems ?? [])" 
                        :inWantlist="false" 
                    />
                </div>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $vinyls->appends(['q' => $query])->links() }}
        </div>
    @endif
</div>
</x-app-layout>
