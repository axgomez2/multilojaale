<x-app-layout
    :vinyl="$vinyl"
    :title="$title ?? null"
    :description="$description ?? null"
    :keywords="$keywords ?? null"
    :image="$image ?? null"
    :breadcrumbs="$breadcrumbs ?? null"
>
    <main class="flex-1 bg-slate-900 py-8">
        <div class="container mx-auto px-4">
            
            <!-- Navegação / Breadcrumbs -->
            <nav class="flex mb-8 text-sm">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="inline-flex items-center text-indigo-400 hover:text-indigo-300">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Início
                        </a>
                    </li>
                    @if($vinyl->categories->count() > 0)
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <a href="{{ route('site.category', $vinyl->categories->first()->slug) }}" class="ml-1 text-indigo-400 hover:text-indigo-300 md:ml-2">{{ $vinyl->categories->first()->nome }}</a>
                        </div>
                    </li>
                    @endif
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-gray-400 md:ml-2">{{ $vinyl->title }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
            
            <!-- Conteúdo principal -->
            <div class="flex flex-col lg:flex-row gap-8">
                
                <!-- Coluna da esquerda: Imagem e detalhes da compra -->
                <div class="w-full lg:w-1/2 xl:w-2/5">
                    <!-- Imagem principal do disco -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                        @if(isset($vinyl->cover_image) && !empty($vinyl->cover_image))
                            <img src="{{ asset('storage/' . $vinyl->cover_image) }}" 
                                alt="{{ $vinyl->title ?? 'Disco de Vinil' }}" 
                                class="w-full object-cover aspect-square">
                        @elseif(isset($vinyl->images) && $vinyl->images !== null && $vinyl->images->count() > 0)
                            <img src="{{ asset('storage/' . $vinyl->images->first()->path) }}" 
                                alt="{{ $vinyl->title ?? 'Disco de Vinil' }}" 
                                class="w-full object-cover aspect-square">
                        @else
                            <div class="w-full bg-gray-200 aspect-square flex items-center justify-center">
                                <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Card de compra -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        @if($vinyl->is_available)
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-2xl font-bold text-gray-900">
                                    R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                                </h3>
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Disponível</span>
                            </div>
                            
                            <!-- Informações de envio -->
                            <div class="mb-4">
                                <p class="text-sm text-gray-700 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                    Envio para todo o Brasil
                                </p>
                                <p class="text-sm text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Entrega estimada: 3-7 dias úteis
                                </p>
                            </div>
                            
                            <!-- Botões de ação -->
                            <div class="flex flex-col space-y-2">
                                <button type="button" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition duration-150 ease-in-out">
                                    Adicionar ao Carrinho
                                </button>
                                <button type="button" class="w-full py-3 px-4 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-lg transition duration-150 ease-in-out">
                                    Adicionar à Lista de Desejos
                                </button>
                            </div>
                        @else
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-2xl font-bold text-gray-900">
                                    Produto Indisponível
                                </h3>
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Esgotado</span>
                            </div>
                            
                            <!-- Aviso de indisponibilidade -->
                            <div class="mb-4">
                                <p class="text-sm text-gray-700 mb-2">
                                    Este produto está temporariamente indisponível.
                                </p>
                            </div>
                            
                            <!-- Botão de notificação -->
                            <div class="flex flex-col space-y-2">
                                <button type="button" class="w-full py-3 px-4 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-lg transition duration-150 ease-in-out">
                                    Avisar quando disponível
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Condição do disco -->
                    @if(isset($vinyl->vinylSec))
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Condição do Disco</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            @if(isset($vinyl->vinylSec->midiaStatus))
                            <div>
                                <span class="block text-sm text-gray-500">Mídia</span>
                                <span class="block text-base font-medium text-gray-900">{{ $vinyl->vinylSec->midiaStatus->name ?? 'N/A' }}</span>
                            </div>
                            @endif
                            
                            @if(isset($vinyl->vinylSec->coverStatus))
                            <div>
                                <span class="block text-sm text-gray-500">Capa</span>
                                <span class="block text-base font-medium text-gray-900">{{ $vinyl->vinylSec->coverStatus->name ?? 'N/A' }}</span>
                            </div>
                            @endif
                            
                            @if(isset($vinyl->vinylSec->peso))
                            <div>
                                <span class="block text-sm text-gray-500">Peso</span>
                                <span class="block text-base font-medium text-gray-900">{{ $vinyl->vinylSec->peso ?? 'N/A' }}g</span>
                            </div>
                            @endif
                            
                            @if(isset($vinyl->vinylSec->formato))
                            <div>
                                <span class="block text-sm text-gray-500">Formato</span>
                                <span class="block text-base font-medium text-gray-900">{{ $vinyl->vinylSec->formato ?? 'N/A' }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Coluna da direita: Informações do disco -->
                <div class="w-full lg:w-1/2 xl:w-3/5">
                    <!-- Cabeçalho com título e artista -->
                    <div class="mb-6">
                        @if($vinyl->artists->count() > 0)
                            <h2 class="text-xl font-medium text-gray-300 mb-2">
                                {{ $vinyl->artists->pluck('name')->join(', ') }}
                            </h2>
                        @endif
                        
                        <h1 class="text-4xl font-bold text-white mb-4">
                            {{ $vinyl->title }}
                        </h1>
                        
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($vinyl->categories as $category)
                                <a href="{{ route('site.category', $category->slug) }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-gray-300 px-3 py-1 rounded-full">
                                    {{ $category->nome }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Informações básicas -->
                    <div class="bg-slate-800 rounded-lg shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-bold text-white mb-4">Informações do Disco</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-300">
                            @if($vinyl->recordLabel)
                            <div>
                                <span class="block text-sm text-gray-400">Gravadora</span>
                                <span class="block text-base font-medium">{{ $vinyl->recordLabel->name }}</span>
                            </div>
                            @endif
                            
                            @if($vinyl->year)
                            <div>
                                <span class="block text-sm text-gray-400">Ano de Lançamento</span>
                                <span class="block text-base font-medium">{{ $vinyl->year }}</span>
                            </div>
                            @endif
                            
                            @if($vinyl->country)
                            <div>
                                <span class="block text-sm text-gray-400">País</span>
                                <span class="block text-base font-medium">{{ $vinyl->country }}</span>
                            </div>
                            @endif
                            
                            @if($vinyl->code)
                            <div>
                                <span class="block text-sm text-gray-400">Código</span>
                                <span class="block text-base font-medium">{{ $vinyl->code }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Informações do Artista -->
                    @if($vinyl->artists && $vinyl->artists->count() > 0)
                    <div class="bg-slate-800 rounded-lg shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-bold text-white mb-4">Sobre o Artista</h3>
                        
                        @foreach($vinyl->artists as $artist)
                            <div class="flex flex-col md:flex-row gap-4 mb-4">
                                <!-- Imagem do Artista -->
                                @if($artist->image_url)
                                <div class="w-full md:w-1/4">
                                    <img src="{{ $artist->image_url }}" 
                                         alt="{{ $artist->name }}" 
                                         class="w-full h-auto rounded-lg object-cover aspect-square">
                                </div>
                                @endif
                                
                                <!-- Informações do Artista -->
                                <div class="w-full {{ $artist->image_url ? 'md:w-3/4' : '' }}">
                                    <h4 class="text-xl font-bold text-white mb-2">{{ $artist->name }}</h4>
                                    
                                    @if(isset($artist->bio) && !empty($artist->bio))
                                    <div class="text-gray-300 prose prose-invert max-w-none text-sm">
                                        {!! nl2br(e($artist->bio)) !!}
                                    </div>
                                    @endif
                                    
                                    @if(isset($artist->website) && !empty($artist->website))
                                    <div class="mt-3">
                                        <a href="{{ $artist->website }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 text-sm inline-flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            Site Oficial
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                    
                    <!-- Informações da Gravadora -->
                    @if($vinyl->recordLabel)
                    <div class="bg-slate-800 rounded-lg shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-bold text-white mb-4">Sobre a Gravadora</h3>
                        
                        <div class="flex flex-col md:flex-row gap-4">
                            <!-- Imagem da Gravadora -->
                            @if($vinyl->recordLabel->logo_url)
                            <div class="w-full md:w-1/4">
                                <img src="{{ $vinyl->recordLabel->logo_url }}" 
                                     alt="{{ $vinyl->recordLabel->name }}" 
                                     class="w-full h-auto rounded-lg object-contain bg-white p-2">
                            </div>
                            @endif
                            
                            <!-- Informações da Gravadora -->
                            <div class="w-full {{ $vinyl->recordLabel->logo_url ? 'md:w-3/4' : '' }}">
                                <h4 class="text-xl font-bold text-white mb-2">{{ $vinyl->recordLabel->name }}</h4>
                                
                                @if(isset($vinyl->recordLabel->description) && !empty($vinyl->recordLabel->description))
                                <div class="text-gray-300 prose prose-invert max-w-none text-sm">
                                    {!! nl2br(e($vinyl->recordLabel->description)) !!}
                                </div>
                                @endif
                                
                                @if(isset($vinyl->recordLabel->founded_year) && !empty($vinyl->recordLabel->founded_year))
                                <div class="mt-2 text-gray-300">
                                    <span class="text-gray-400 text-sm">Fundada em:</span> {{ $vinyl->recordLabel->founded_year }}
                                </div>
                                @endif
                                
                                @if(isset($vinyl->recordLabel->website) && !empty($vinyl->recordLabel->website))
                                <div class="mt-3">
                                    <a href="{{ $vinyl->recordLabel->website }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 text-sm inline-flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        Site Oficial
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Lista de faixas -->
                    @if($vinyl->tracks && $vinyl->tracks->count() > 0)
                    <div class="bg-slate-800 rounded-lg shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-bold text-white mb-4">Faixas</h3>
                        
                        <div class="space-y-4">
                            @foreach($vinyl->tracks as $track)
                                <div class="flex justify-between items-center py-2 border-b border-slate-700">
                                    <div class="flex items-center">
                                        <span class="text-gray-400 w-8">{{ $track->position }}</span>
                                        <span class="text-white">{{ $track->name }}</span>
                                    </div>
                                    @if($track->duration)
                                        <span class="text-gray-400 text-sm">{{ $track->duration }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Descrição -->
                    @if($vinyl->description)
                    <div class="bg-slate-800 rounded-lg shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-bold text-white mb-4">Descrição</h3>
                        
                        <div class="prose prose-invert max-w-none">
                            {!! nl2br(e($vinyl->description)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Discos similares -->
            @if($similarVinyls->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-white mb-6">Você também pode gostar</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($similarVinyls as $similar)
                        <x-site.vinyl-card :vinyl="$similar" size="normal"
                            :inWishlist="in_array($similar->id, is_array($wishlistItems) ? $wishlistItems : ($wishlistItems ? $wishlistItems->toArray() : []))"
                            :inWantlist="in_array($similar->id, is_array($wantlistItems) ? $wantlistItems : ($wantlistItems ? $wantlistItems->toArray() : []))" />
                    @endforeach
                </div>
            </div>
            @endif
            
        </div>
    </main>
</x-app-layout>
