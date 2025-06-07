@props(['release'])

<div class="max-w-6xl mx-auto" x-data="{
    selectedCoverIndex: 0,
    showMainImage(index) {
        this.selectedCoverIndex = index;
    }
}">
    <h3 class="text-xl font-semibold mb-4 text-gray-900">Você selecionou o disco: {{ $release['title'] }}</h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Image Column -->
        <div class="md:col-span-1">
            <!-- Capa do disco selecionada -->
            <div class="mb-3">
                @if(isset($release['images']) && count($release['images']) > 0)
                    <div class="relative group">
                        <template x-for="(image, index) in {{ json_encode($release['images']) }}" :key="index">
                            <img :src="image.uri"
                                 :alt="'{{ $release['title'] }} - ' + (index + 1)"
                                 class="rounded-lg shadow-lg w-full object-cover"
                                 style="height: 300px;"
                                 x-show="selectedCoverIndex === index"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100">
                        </template>

                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3 rounded-b-lg opacity-0 group-hover:opacity-100 transition-opacity">
                            <p class="text-white text-sm">Imagem <span x-text="selectedCoverIndex + 1"></span> de <span x-text="{{ count($release['images']) }}"></span></p>
                        </div>
                    </div>

                    <!-- Seletor de imagens -->
                    <div class="mt-3">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Selecione a imagem para capa:</h5>
                        <div class="grid grid-cols-5 gap-2">
                            <template x-for="(image, index) in {{ json_encode($release['images']) }}" :key="index">
                                <div
                                    @click="showMainImage(index)"
                                    class="relative cursor-pointer rounded-md overflow-hidden transition-all duration-200"
                                    :class="selectedCoverIndex === index ? 'ring-2 ring-blue-500' : 'hover:opacity-80'">
                                    <img :src="image.uri150 || image.uri"
                                         class="w-full h-16 object-cover"
                                         :alt="'{{ $release['title'] }} - ' + (index + 1)">
                                    <div
                                        x-show="selectedCoverIndex === index"
                                        class="absolute inset-0 bg-blue-500/20 flex items-center justify-center">
                                        <div class="bg-blue-500 text-white rounded-full p-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <input type="hidden" name="selected_cover_index" x-model="selectedCoverIndex">
                    </div>
                @else
                    <div class="rounded-lg shadow-lg w-full max-w-full h-80 flex items-center justify-center bg-gray-200 dark:bg-gray-700">
                        <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Informações do artista com imagem -->
            @if(isset($release['artists']))
                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                    <h4 class="text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Sobre o artista</h4>
                    <div class="flex items-center mb-2">
                        <!-- Se tiver mais detalhes do artista, poderia incluir uma imagem aqui -->
                        <div class="bg-gray-200 dark:bg-gray-600 rounded-full w-10 h-10 flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">
                                @if(isset($release['artists']))
                                    @foreach(is_array($release['artists']) ? $release['artists'] : [] as $artist)
                                        {{ $artist['name'] }}
                                        @if(!$loop->last), @endif
                                    @endforeach
                                @else
                                    Artista Desconhecido
                                @endif
                            </p>
                            @if(isset($release['genres']))
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ implode(', ', $release['genres']) }}
                                </p>
                            @endif
                            @if(isset($release['artists'][0]['id']) && isset($release['artists'][0]['name']))
                                <!-- Link para o perfil do artista no Discogs com formato correto incluindo o slug -->
                                @php
                                    $artistId = $release['artists'][0]['id'];
                                    $artistName = $release['artists'][0]['name'];
                                    $artistSlug = Str::slug($artistName);
                                    $artistUrl = "https://www.discogs.com/artist/{$artistId}-{$artistSlug}";
                                @endphp
                                <a href="{{ $artistUrl }}"
                                    target="_blank"
                                    class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                                    Ver perfil no Discogs
                                </a>
                            @endif
                        </div>
                    </div>

                    @if(isset($release['styles']))
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach(is_array($release['styles']) ? $release['styles'] : [] as $style)
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full dark:bg-blue-900 dark:text-blue-200">
                                    {{ $style }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Details Column -->
        <div class="md:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Artista</h4>
                    <p class="text-base text-gray-900">{{ !empty($release['artists']) ? implode(', ', array_column($release['artists'], 'name')) : 'Desconhecido' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Título</h4>
                    <p class="text-base text-gray-900">{{ $release['title'] }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Ano</h4>
                    <p class="text-base text-gray-900">{{ $release['year'] ?? 'Desconhecido' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Gênero</h4>
                    <p class="text-base text-gray-900">{{ !empty($release['genres']) && is_array($release['genres']) ? implode(', ', $release['genres']) : 'Não especificado' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Estilos</h4>
                    <p class="text-base text-gray-900">{{ !empty($release['styles']) && is_array($release['styles']) ? implode(', ', $release['styles']) : 'Não especificado' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">País</h4>
                    <p class="text-base text-gray-900">{{ $release['country'] ?? 'Desconhecido' }}</p>
                </div>
                @if(isset($release['labels']))
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Gravadora</h4>
                        <p class="text-base text-gray-900">{{ !empty($release['labels']) ? implode(', ', array_column($release['labels'], 'name')) : 'Desconhecido' }}</p>
                    </div>

                    <!-- Número de Catálogo -->
                    @if(!empty($release['labels'][0]['catno']))
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Cat. nº</h4>
                        <p class="text-base text-gray-900 font-mono">{{ $release['labels'][0]['catno'] }}</p>
                    </div>
                    @endif
                @endif
            </div>

            <!-- Market Info -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-lg font-semibold mb-3 text-gray-900">Informações de Mercado (Discogs)</h4>
                <!-- Estatísticas do Disco -->
                <div class="grid grid-cols-1 gap-6">
                    <!-- Identificadores -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-sm font-medium mb-3 text-gray-700 dark:text-gray-300">Identificadores</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Código de Barras -->
                            <div>
                                <h5 class="text-xs font-medium text-gray-500">Código de Barras</h5>
                                <p class="text-sm font-mono text-gray-900">
                                    @if(!empty($release['identifiers']))
                                        @php
                                            $barcode = null;
                                            foreach($release['identifiers'] as $identifier) {
                                                if(isset($identifier['type']) && strtolower($identifier['type']) == 'barcode') {
                                                    $barcode = $identifier['value'];
                                                    break;
                                                }
                                            }
                                        @endphp
                                        {{ $barcode ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>

                            <!-- Número de Catálogo (já adicionado anteriormente) -->
                            <div>
                                <h5 class="text-xs font-medium text-gray-500">Cat. nº</h5>
                                <p class="text-sm font-mono text-gray-900">
                                    {{ !empty($release['labels'][0]['catno']) ? $release['labels'][0]['catno'] : 'N/A' }}
                                </p>
                            </div>

                            <!-- Outros Identificadores -->
                            @if(!empty($release['identifiers']))
                                @foreach($release['identifiers'] as $identifier)
                                    @if(isset($identifier['type']) && strtolower($identifier['type']) != 'barcode')
                                        <div>
                                            <h5 class="text-xs font-medium text-gray-500">{{ ucfirst($identifier['type'] ?? 'Identificador') }}</h5>
                                            <p class="text-sm font-mono text-gray-900">{{ $identifier['value'] ?? 'N/A' }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Estatísticas de Coleção -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-sm font-medium mb-3 text-gray-700 dark:text-gray-300">Estatísticas de Coleção</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <!-- Possuem -->
                            <div>
                                <h5 class="text-xs font-medium text-gray-500">Possuem</h5>
                                <p class="text-base font-semibold text-gray-900">
                                    {{ isset($release['community']['have']) ? number_format($release['community']['have'], 0, ',', '.') : 'N/A' }}
                                </p>
                            </div>

                            <!-- Querem -->
                            <div>
                                <h5 class="text-xs font-medium text-gray-500">Querem</h5>
                                <p class="text-base font-semibold text-gray-900">
                                    {{ isset($release['community']['want']) ? number_format($release['community']['want'], 0, ',', '.') : 'N/A' }}
                                </p>
                            </div>

                            <!-- Avaliação Média -->
                            <div>
                                <h5 class="text-xs font-medium text-gray-500">Avaliação Média</h5>
                                <p class="text-base font-semibold text-gray-900 flex items-center">
                                    @if(isset($release['community']['rating']['average']))
                                        {{ number_format($release['community']['rating']['average'], 2, ',', '.') }}
                                        <span class="text-yellow-500 ml-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>

                            <!-- Avaliações -->
                            <div>
                                <h5 class="text-xs font-medium text-gray-500">Avaliações</h5>
                                <p class="text-base font-semibold text-gray-900">
                                    {{ isset($release['community']['rating']['count']) ? number_format($release['community']['rating']['count'], 0, ',', '.') : 'N/A' }}
                                </p>
                            </div>

                            <!-- Quantidade à venda -->
                            <div>
                                <h5 class="text-xs font-medium text-gray-500">À venda</h5>
                                <p class="text-base font-semibold text-gray-900">
                                    {{ isset($release['num_for_sale']) ? number_format($release['num_for_sale'], 0, ',', '.') : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Informações de Preço -->
                    @php
                        $rawMarketData = $release['raw_market_data'] ?? [];
                    @endphp
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-sm font-medium mb-3 text-gray-700 dark:text-gray-300">Informações de Preço</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <!-- Preço Baixo -->
                            <div>
                                <h5 class="text-xs font-medium text-gray-500">Baixo</h5>
                                <p class="text-base font-semibold text-gray-900">
                                    @if(array_key_exists('lowest_price', $rawMarketData) && is_scalar($rawMarketData['lowest_price']) && is_numeric($rawMarketData['lowest_price']))
                                        R$ {{ number_format((float)$rawMarketData['lowest_price'], 2, ',', '.') }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>

                            <!-- Preço Mediano -->
                            <div>
                                <h5 class="text-xs font-medium text-gray-500">Mediano</h5>
                                <p class="text-base font-semibold text-gray-900">
                                    @if(array_key_exists('median_price', $rawMarketData) && is_scalar($rawMarketData['median_price']) && is_numeric($rawMarketData['median_price']))
                                        R$ {{ number_format((float)$rawMarketData['median_price'], 2, ',', '.') }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>

                            <!-- Preço Alto -->
                            <div>
                                <h5 class="text-xs font-medium text-gray-500">Alto</h5>
                                <p class="text-base font-semibold text-gray-900">
                                    @if(array_key_exists('highest_price', $rawMarketData) && is_scalar($rawMarketData['highest_price']) && is_numeric($rawMarketData['highest_price']))
                                        R$ {{ number_format((float)$rawMarketData['highest_price'], 2, ',', '.') }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Preço Sugerido -->
                    <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg border border-green-200 dark:border-green-700">
                        <h4 class="text-base font-medium mb-2 text-green-700 dark:text-green-300">Preço Sugerido para Venda</h4>
                        <p class="text-2xl font-bold text-green-800 dark:text-green-200">
                            @php
                                $suggestedPrice = isset($release['suggested_price']) && is_numeric($release['suggested_price']) ? (float)$release['suggested_price'] : 0;
                            @endphp
                            R$ {{ number_format($suggestedPrice, 2, ',', '.') }}
                        </p>
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                            Este preço é calculado com base nos dados do Discogs, ajustado pela raridade do item.
                            <span class="text-xs">{{ isset($release['price_source']) ? '(Fonte: ' . $release['price_source'] . ')' : '' }}</span>
                        </p>
                    </div>

                    <!-- SEÇÃO DE DIAGNÓSTICO - Mostra toda a resposta da API -->
                    <div class="bg-gray-100 p-4 rounded-lg border border-gray-300 mt-4">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-base font-medium text-gray-800">Dados Completos da API (Diagnóstico)</h4>
                            <button
                                x-data="{}"
                                x-on:click="$el.parentNode.nextElementSibling.classList.toggle('hidden')"
                                class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                Mostrar/Ocultar
                            </button>
                        </div>
                        <div class="hidden overflow-auto" style="max-height: 500px;">
                            <div class="text-sm font-medium mb-2 text-gray-700">Market Data:</div>
                            <pre class="text-xs bg-gray-200 p-2 rounded overflow-x-auto mb-3">{{ isset($release['raw_market_data']) ? json_encode($release['raw_market_data'], JSON_PRETTY_PRINT) : 'Dados de mercado não disponíveis' }}</pre>

                            <div class="text-sm font-medium mb-2 text-gray-700">Brazil Listings:</div>
                            <pre class="text-xs bg-gray-200 p-2 rounded overflow-x-auto mb-3">{{ isset($release['brazil_listings']) ? json_encode($release['brazil_listings'], JSON_PRETTY_PRINT) : 'Dados do Brasil não disponíveis' }}</pre>

                            <div class="text-sm font-medium mb-2 text-gray-700">Release Data:</div>
                            <pre class="text-xs bg-gray-200 p-2 rounded overflow-x-auto">{{ json_encode($release, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>

                    <!-- Dados do Brasil (se disponíveis) -->
                    @if(isset($release['brazil_listings']) && is_array($release['brazil_listings']) && count($release['brazil_listings']) > 0)
                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg border border-blue-200 dark:border-blue-700">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="text-base font-medium text-blue-700 dark:text-blue-300">Disponibilidade no Brasil</h4>
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                    {{ count($release['brazil_listings']) }} itens
                                </span>
                            </div>
                            <div class="grid grid-cols-3 gap-4 mb-3">
                                <!-- Preço Baixo Brasil -->
                                <div>
                                    <h5 class="text-xs font-medium text-blue-600 dark:text-blue-400">Menor Preço</h5>
                                    <p class="text-base font-semibold text-blue-900 dark:text-blue-100">
                                        @php
                                            $brLowestPrice = null;
                                            if (!empty($release['brazil_market_stats']['lowest_price']) && is_numeric($release['brazil_market_stats']['lowest_price'])) {
                                                $brLowestPrice = (float)$release['brazil_market_stats']['lowest_price'];
                                            }
                                        @endphp
                                        {{ $brLowestPrice ? 'R$ ' . number_format($brLowestPrice, 2, ',', '.') : 'N/A' }}
                                    </p>
                                </div>

                                <!-- Preço Médio Brasil -->
                                <div>
                                    <h5 class="text-xs font-medium text-blue-600 dark:text-blue-400">Preço Médio</h5>
                                    <p class="text-base font-semibold text-blue-900 dark:text-blue-100">
                                        @php
                                            $brMedianPrice = null;
                                            if (!empty($release['brazil_market_stats']['median_price']) && is_numeric($release['brazil_market_stats']['median_price'])) {
                                                $brMedianPrice = (float)$release['brazil_market_stats']['median_price'];
                                            }
                                        @endphp
                                        {{ $brMedianPrice ? 'R$ ' . number_format($brMedianPrice, 2, ',', '.') : 'N/A' }}
                                    </p>
                                </div>

                                <!-- Preço Alto Brasil -->
                                <div>
                                    <h5 class="text-xs font-medium text-blue-600 dark:text-blue-400">Maior Preço</h5>
                                    <p class="text-base font-semibold text-blue-900 dark:text-blue-100">
                                        @php
                                            $brHighestPrice = null;
                                            if (!empty($release['brazil_market_stats']['highest_price']) && is_numeric($release['brazil_market_stats']['highest_price'])) {
                                                $brHighestPrice = (float)$release['brazil_market_stats']['highest_price'];
                                            }
                                        @endphp
                                        {{ $brHighestPrice ? 'R$ ' . number_format($brHighestPrice, 2, ',', '.') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tracklist -->
            @if(isset($release['tracklist']))
                <div>
                    <h4 class="text-lg font-semibold mb-3 text-gray-900">Tracklist</h4>
                    <ul class="space-y-2">
                        @foreach(is_array($release['tracklist']) ? $release['tracklist'] : [] as $track)
                            <li class="flex items-center text-gray-900">
                                <span class="w-6 h-6 flex items-center justify-center bg-gray-100 rounded-full mr-2 text-sm">
                                    {{ $loop->iteration }}
                                </span>
                                <span class="font-medium">{{ $track['title'] }}</span>
                                @if(isset($track['duration']))
                                    <span class="ml-2 text-sm text-gray-500">({{ $track['duration'] }})</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Notes -->
            @if(isset($release['notes']))
                <div>
                    <h4 class="text-lg font-semibold mb-2 text-gray-900">Notas</h4>
                    <p class="text-sm text-gray-700">{{ $release['notes'] }}</p>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ $release['uri'] }}"
                    target="_blank"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 inline-flex items-center">
                    Link do disco no Discogs
                </a>

                <!-- Botão de salvar disco usando formulário tradicional -->
                <form action="{{ route('admin.vinyls.store') }}" method="POST" class="inline-block">
                    @csrf
                    <input type="hidden" name="release_id" value="{{ $release['id'] }}">
                    <input type="hidden" name="selected_cover_index" x-bind:value="selectedCoverIndex">

                    <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2">
                        <div class="py-2">
                            <span class="text-sm text-gray-600">Imagem de capa selecionada: <span class="font-medium" x-text="selectedCoverIndex + 1"></span></span>
                        </div>

                        <button type="submit"
                                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800 inline-flex items-center">
                            <span>Salvar disco</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
