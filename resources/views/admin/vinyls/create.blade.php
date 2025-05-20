<x-admin-layout title="Adicionar novo disco">
<!-- Meta tags para URLs -->
<meta name="store-vinyl-url" content="{{ route('admin.vinyls.store') }}">
<meta name="vinyl-index-url" content="{{ route('admin.vinyls.index') }}">
<meta name="complete-vinyl-url" content="{{ route('admin.vinyls.complete', ':id') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<div
    x-data="{
        loading: false,
        search() {
            this.loading = true;
            document.getElementById('search-form').submit();
        }
    }" 
    class="p-4">
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Pesquisar novo disco:</h2>

        <form action="{{ route('admin.vinyls.create') }}" method="GET" @submit="startSearch()">
            <div class="flex flex-wrap md:flex-nowrap items-center gap-3">
                <div class="w-full">
                    <input type="text"
                           name="query"
                           value="{{ $query }}"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                           placeholder="Encontre o disco pelo artista, título ou código do disco"
                           required>
                </div>
                <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 inline-flex items-center"
                        :disabled="loading"
                        :class="{'opacity-75 cursor-not-allowed': loading}">
                    <template x-if="loading">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <svg x-show="!loading" class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span>Pesquisar</span>
                </button>
            </div>
        </form>

        <div id="searchResults" class="mt-6">
            @if($selectedRelease)
                <!-- Selected Release Content -->
                <div class="max-w-6xl mx-auto">
                    <h3 class="text-xl font-semibold mb-4 text-gray-900">Você selecionou o disco: {{ $selectedRelease['title'] }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Image Column -->                        
                        <div class="md:col-span-1">
                            <!-- Capa do disco -->
                            <div class="mb-5">
                                @if(isset($selectedRelease['images']) && count($selectedRelease['images']) > 0)
                                    <img src="{{ $selectedRelease['images'][0]['uri'] }}"
                                        alt="{{ $selectedRelease['title'] }}"
                                        class="rounded-lg shadow-lg w-full max-w-full object-cover mb-3">
                                    
                                    <!-- Miniaturas das outras imagens -->
                                    @if(count($selectedRelease['images']) > 1)
                                        <div class="grid grid-cols-4 gap-2">
                                            @foreach(array_slice($selectedRelease['images'], 1, 4) as $image)
                                                <img src="{{ $image['uri150'] ?? $image['uri'] }}"
                                                    alt="{{ $selectedRelease['title'] }} - Imagem {{ $loop->iteration + 1 }}"
                                                    class="w-full h-20 object-cover rounded-md shadow cursor-pointer hover:opacity-80"
                                                    onclick="window.open('{{ $image['uri'] }}', '_blank')">
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    <div class="rounded-lg shadow-lg w-full max-w-full h-80 flex items-center justify-center bg-gray-200 dark:bg-gray-700">
                                        <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Informações do artista com imagem -->
                            @if(isset($selectedRelease['artists']))
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
                                                @if(isset($selectedRelease['artists']))
                                                    @foreach($selectedRelease['artists'] as $artist)
                                                        {{ $artist['name'] }}
                                                        @if(!$loop->last), @endif
                                                    @endforeach
                                                @else
                                                    Artista Desconhecido
                                                @endif
                                            </p>
                                            @if(isset($selectedRelease['genres']))
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ implode(', ', $selectedRelease['genres']) }}
                                                </p>
                                            @endif
                                            @if(isset($selectedRelease['artists'][0]['resource_url']))
                                                <!-- Link para o perfil do artista no Discogs -->
                                                <a href="{{ str_replace('api.', '', $selectedRelease['artists'][0]['resource_url']) }}" 
                                                   target="_blank"
                                                   class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                                                    Ver perfil no Discogs
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if(isset($selectedRelease['styles']))
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach($selectedRelease['styles'] as $style)
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
                                    <p class="text-base text-gray-900">{{ implode(', ', array_column($selectedRelease['artists'], 'name')) }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Título</h4>
                                    <p class="text-base text-gray-900">{{ $selectedRelease['title'] }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Ano</h4>
                                    <p class="text-base text-gray-900">{{ $selectedRelease['year'] ?? 'Desconhecido' }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Gênero</h4>
                                    <p class="text-base text-gray-900">{{ implode(', ', $selectedRelease['genres'] ?? ['Não especificado']) }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Estilos</h4>
                                    <p class="text-base text-gray-900">{{ implode(', ', $selectedRelease['styles'] ?? ['Não especificado']) }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">País</h4>
                                    <p class="text-base text-gray-900">{{ $selectedRelease['country'] ?? 'Desconhecido' }}</p>
                                </div>
                                @if(isset($selectedRelease['labels']))
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Gravadora</h4>
                                        <p class="text-base text-gray-900">{{ implode(', ', array_column($selectedRelease['labels'], 'name')) }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Market Info -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-lg font-semibold mb-3 text-gray-900">Informações de Mercado (Discogs)</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    @if(isset($selectedRelease['community']['have']))
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500">Quantidade em coleções</h5>
                                            <p class="text-base text-gray-900">{{ $selectedRelease['community']['have'] }}</p>
                                        </div>
                                    @endif
                                    @if(isset($selectedRelease['num_for_sale']))
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500">Quantidade à venda</h5>
                                            <p class="text-base text-gray-900">{{ $selectedRelease['num_for_sale'] }}</p>
                                        </div>
                                    @endif
                                    @php
                                        // Usar o valor direto do Discogs para o preço mais baixo
                                        $exchangeRate = 5.8; // Taxa fixa de câmbio USD para BRL
                                        $lowestPriceUSD = $selectedRelease['lowest_price'] ?? 0;
                                        
                                        // Preço médio - usar o valor do Discogs se disponível ou calcular
                                        $medianPriceUSD = 0;
                                        if (isset($selectedRelease['median_price'])) {
                                            $medianPriceUSD = $selectedRelease['median_price'];
                                        } else if ($lowestPriceUSD > 0) {
                                            // Se não tiver preço médio, usar uma estimativa baseada no preço mais baixo
                                            $medianPriceUSD = $lowestPriceUSD * 1.3;
                                        }
                                        
                                        // Preço mais alto - usar o valor do Discogs se disponível ou calcular
                                        $highestPriceUSD = 0;
                                        if (isset($selectedRelease['highest_price'])) {
                                            $highestPriceUSD = $selectedRelease['highest_price'];
                                        } else if ($lowestPriceUSD > 0) {
                                            // Se não tiver preço mais alto, usar uma estimativa baseada no preço mais baixo
                                            $highestPriceUSD = $lowestPriceUSD * 1.8;
                                        }
                                        
                                        // Calcular valores em reais
                                        $lowestPriceBRL = $lowestPriceUSD * $exchangeRate;
                                        $medianPriceBRL = $medianPriceUSD * $exchangeRate;
                                        $highestPriceBRL = $highestPriceUSD * $exchangeRate;
                                    @endphp
                                    @if($lowestPriceUSD > 0)
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500">Preço mais baixo</h5>
                                            <p class="text-base text-gray-900">
                                                US$ {{ number_format($lowestPriceUSD, 2, ',', '.') }}
                                                <span class="text-xs text-gray-500">(R$ {{ number_format($lowestPriceBRL, 2, ',', '.') }})</span>
                                            </p>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500">Preço médio</h5>
                                            <p class="text-base text-gray-900">
                                                US$ {{ number_format($medianPriceUSD, 2, ',', '.') }}
                                                <span class="text-xs text-gray-500">(R$ {{ number_format($medianPriceBRL, 2, ',', '.') }})</span>
                                            </p>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500">Preço mais alto</h5>
                                            <p class="text-base text-gray-900">
                                                US$ {{ number_format($highestPriceUSD, 2, ',', '.') }}
                                                <span class="text-xs text-gray-500">(R$ {{ number_format($highestPriceBRL, 2, ',', '.') }})</span>
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Tracklist -->
                            @if(isset($selectedRelease['tracklist']))
                                <div>
                                    <h4 class="text-lg font-semibold mb-3 text-gray-900">Tracklist</h4>
                                    <ul class="space-y-2">
                                        @foreach($selectedRelease['tracklist'] as $track)
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
                            @if(isset($selectedRelease['notes']))
                                <div>
                                    <h4 class="text-lg font-semibold mb-2 text-gray-900">Notas</h4>
                                    <p class="text-sm text-gray-700">{{ $selectedRelease['notes'] }}</p>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ $selectedRelease['uri'] }}"
                                   target="_blank"
                                   class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 inline-flex items-center">
                                    Link do disco no Discogs
                                </a>
                                <!-- Botão desativar o modal --> 
                                <!-- Botão de salvar disco usando formulário tradicional -->
                                <form action="{{ route('admin.vinyls.store') }}" method="POST" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="release_id" value="{{ $selectedRelease['id'] }}">
                                    <button type="submit"
                                            class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800 inline-flex items-center">
                                        <span>Salvar disco</span>
                                    </button>
                                </form>
                                
                                <!-- Botão para teste direto (POST sem usar o modal) -->
                                <form action="{{ route('admin.vinyls.store') }}" method="POST" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="release_id" value="{{ $selectedRelease['id'] }}">
                                    <button type="submit"
                                            class="text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-amber-600 dark:hover:bg-amber-700 focus:outline-none dark:focus:ring-amber-800 inline-flex items-center">
                                        Salvar Direto (Teste)
                                    </button>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(count($searchResults) > 0)
                <!-- Search Results -->
                <div x-data="{ formatFilter: '', countryFilter: '', yearFilter: '' }">
                    <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Resultados da Busca</h3>

                    <!-- Filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <select x-model="formatFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Todos os Formatos</option>
                            @foreach(collect($searchResults)->pluck('format')->flatten()->unique() as $format)
                                <option value="{{ $format }}">{{ $format }}</option>
                            @endforeach
                        </select>

                        <select x-model="countryFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Todos os Países</option>
                            @foreach(collect($searchResults)->pluck('country')->unique() as $country)
                                <option value="{{ $country }}">{{ $country }}</option>
                            @endforeach
                        </select>

                        <select x-model="yearFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Todos os Anos</option>
                            @foreach(collect($searchResults)->pluck('year')->filter()->unique() as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                            <option value="Desconhecido">Ano desconhecido</option>
                        </select>
                    </div>

                    <!-- Results -->
                    <div class="space-y-4">
                        @foreach($searchResults as $result)
                            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                                 x-show="
                                    (!formatFilter || '{{ $result['format'][0] ?? '' }}'.includes(formatFilter)) &&
                                    (!countryFilter || '{{ $result['country'] }}' === countryFilter) &&
                                    (!yearFilter || '{{ $result['year'] ?? 'Desconhecido' }}' === yearFilter)
                                 ">
                                <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                                    <img src="{{ $result['thumb'] ?? '/placeholder-image.jpg' }}"
                                         alt="{{ $result['title'] }}"
                                         class="w-16 h-16 object-cover rounded-lg">
                                    <div class="flex-grow">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $result['title'] }}</h4>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 flex flex-wrap gap-2">
                                            <span>{{ $result['year'] ?? 'Ano desconhecido' }}</span>
                                            @if(isset($result['format']))
                                                <span class="hidden md:inline">•</span>
                                                <span>{{ $result['format'][0] ?? 'Format unknown' }}</span>
                                            @endif
                                            @if(isset($result['country']))
                                                <span class="hidden md:inline">•</span>
                                                <span>{{ $result['country'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.vinyls.create', ['release_id' => $result['id'], 'query' => $query]) }}"
                                       class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 inline-flex items-center"
                                       x-data="{ loading: false }"
                                       @click.prevent="loading = true; window.location.href = $el.href"
                                       :class="{'opacity-75 cursor-not-allowed': loading}">
                                        <template x-if="loading">
                                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </template>
                                        <span x-text="loading ? 'Carregando...' : 'Selecionar'"></span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($query)
                <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-blue-900 dark:text-blue-300" role="alert">
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span>Nenhum resultado encontrado para "{{ $query }}".</span>
                    </div>
                </div>
            @endif
        </div>
    </div>


    <!-- Nenhum modal é necessário, estamos redirecionando diretamente -->
  
</div>
</x-admin-layout>
@push('scripts')
<script>
// Variável global para armazenar o ID do vinyl salvo
let savedVinylId = null;

// Função para testar o modal
function testModal() {
    // Definir o conteúdo do modal
    document.getElementById('modal-title').textContent = 'Teste do Modal';
    document.getElementById('modal-message').textContent = 'Este é um teste do modal para verificar se está funcionando';
    
    // Mostrar botões de sucesso
    document.getElementById('success-buttons').classList.remove('hidden');
    document.getElementById('exists-button').classList.add('hidden');
    document.getElementById('error-button').classList.add('hidden');
    
    // Configurar URL de completar cadastro
    }
    
    if (!document.querySelector('meta[name="complete-vinyl-url"]')) {
        const completeUrl = document.createElement('meta');
        completeUrl.name = 'complete-vinyl-url';
// Função para salvar o disco com JavaScript puro
function saveVinyl(releaseId) {
    // Pegar o botão que foi clicado
    const saveButton = event.target.closest('button');
    
    // Mostrar loading no botão
    if (saveButton) {
        saveButton.disabled = true;
        saveButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Salvando...';
    }
    
    // Obter o CSRF token do Laravel
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Fazer requisição para salvar o disco
    fetch('{{ route("admin.vinyls.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ release_id: releaseId })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error('Erro HTTP: ' + response.status + ' ' + text);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Resposta:', data);
        
        // Dependendo do status da resposta, fazer uma ação diferente
        if (data.status === 'success' && data.vinyl_id) {
            // Redirecionar para a página de completar o cadastro
            window.location.href = '{{ route("admin.vinyls.complete", ":id") }}'.replace(':id', data.vinyl_id);
        } else if (data.status === 'exists') {
            // Mostrar alerta se o disco já existir
            alert(data.message || 'Este disco já está cadastrado no sistema.');
            // Restaurar o botão
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.innerHTML = '<span>Salvar disco</span>';
            }
        } else {
            // Mostrar erro genérico
            alert(data.message || 'Ocorreu um erro ao salvar o disco.');
            // Restaurar o botão
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.innerHTML = '<span>Salvar disco</span>';
            }
        }
    })
    .catch(error => {
        // Tratar erros de requisição
        console.error('Erro:', error);
        alert(error.message || 'Ocorreu um erro ao salvar o disco.');
        
        // Restaurar o botão
        if (saveButton) {
            saveButton.disabled = false;
            saveButton.innerHTML = '<span>Salvar disco</span>';
        }
    });
}
</script>
@endpush
