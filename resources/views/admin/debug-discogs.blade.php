<x-admin-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Depuração da API do Discogs</h2>
            
            <!-- Formulário de busca -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Consultar Release do Discogs</h3>
                <form action="{{ route('admin.debug.discogs') }}" method="GET" class="flex gap-4">
                    <div class="flex-grow">
                        <label for="release_id" class="block text-sm font-medium text-gray-700 mb-1">ID do Release</label>
                        <input type="text" name="release_id" id="release_id" 
                               value="{{ request('release_id') }}" 
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                               placeholder="Ex: 249504">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Consultar
                        </button>
                    </div>
                </form>
            </div>
            
            @if(isset($releaseData))
                <!-- Dados do Release -->
                <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Dados do Release #{{ request('release_id') }}</h3>
                        <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">
                            Status: {{ $status ?? 'OK' }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Informações básicas -->
                        <div>
                            <h4 class="text-md font-medium text-gray-700 mb-3">Informações Básicas</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dl class="divide-y divide-gray-200">
                                    <div class="py-2 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500">Título</dt>
                                        <dd class="text-sm text-gray-900 col-span-2">{{ $releaseData['title'] ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="py-2 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500">Ano</dt>
                                        <dd class="text-sm text-gray-900 col-span-2">{{ $releaseData['year'] ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="py-2 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500">País</dt>
                                        <dd class="text-sm text-gray-900 col-span-2">{{ $releaseData['country'] ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="py-2 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500">Gêneros</dt>
                                        <dd class="text-sm text-gray-900 col-span-2">
                                            {{ isset($releaseData['genres']) ? implode(', ', $releaseData['genres']) : 'N/A' }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                        
                        <!-- Dados de Mercado -->
                        <div>
                            <h4 class="text-md font-medium text-gray-700 mb-3">Dados de Mercado</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dl class="divide-y divide-gray-200">
                                    <div class="py-2 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500">À Venda</dt>
                                        <dd class="text-sm text-gray-900 col-span-2">{{ $marketData['num_for_sale'] ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="py-2 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500">Preço Mais Baixo</dt>
                                        <dd class="text-sm text-gray-900 col-span-2">
                                            @if(isset($marketData['lowest_price']) && (is_numeric($marketData['lowest_price'])))
                                                R$ {{ number_format((float)$marketData['lowest_price'], 2, ',', '.') }}
                                            @else
                                                <span class="text-red-500">Não disponível</span>
                                                @if(isset($marketData['lowest_price']))
                                                    <small class="block text-gray-500">(Tipo de dados: {{ gettype($marketData['lowest_price']) }})</small>
                                                @endif
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="py-2 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500">Preço Médio</dt>
                                        <dd class="text-sm text-gray-900 col-span-2">
                                            @if(isset($marketData['median_price']) && (is_numeric($marketData['median_price'])))
                                                R$ {{ number_format((float)$marketData['median_price'], 2, ',', '.') }}
                                            @else
                                                <span class="text-red-500">Não disponível</span>
                                                @if(isset($marketData['median_price']))
                                                    <small class="block text-gray-500">(Tipo de dados: {{ gettype($marketData['median_price']) }})</small>
                                                @endif
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="py-2 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500">Preço Mais Alto</dt>
                                        <dd class="text-sm text-gray-900 col-span-2">
                                            @if(isset($marketData['highest_price']) && (is_numeric($marketData['highest_price'])))
                                                R$ {{ number_format((float)$marketData['highest_price'], 2, ',', '.') }}
                                            @else
                                                <span class="text-red-500">Não disponível</span>
                                                @if(isset($marketData['highest_price']))
                                                    <small class="block text-gray-500">(Tipo de dados: {{ gettype($marketData['highest_price']) }})</small>
                                                @endif
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="py-2 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500">Chaves Disponíveis</dt>
                                        <dd class="text-sm text-gray-900 col-span-2">
                                            @if(isset($marketData) && is_array($marketData))
                                                {{ implode(', ', array_keys($marketData)) }}
                                            @else
                                                <span class="text-red-500">Dados de mercado não disponíveis</span>
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dados Brutos -->
                    <div class="mt-8">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-md font-medium text-gray-700">Dados Brutos da API</h4>
                            <button onclick="toggleSection('raw-data')" class="text-sm text-blue-600 hover:text-blue-800">
                                Mostrar/Ocultar
                            </button>
                        </div>
                        <div id="raw-data" class="hidden">
                            <div class="mb-4">
                                <h5 class="text-sm font-medium text-gray-600 mb-2">Release Data</h5>
                                <pre class="bg-gray-100 p-4 rounded-lg overflow-auto text-xs" style="max-height: 400px;">{{ json_encode($releaseData, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                            <div class="mb-4">
                                <h5 class="text-sm font-medium text-gray-600 mb-2">Market Data</h5>
                                <pre class="bg-gray-100 p-4 rounded-lg overflow-auto text-xs" style="max-height: 400px;">{{ json_encode($marketData ?? [], JSON_PRETTY_PRINT) }}</pre>
                            </div>
                            @if(isset($brazilListings))
                            <div>
                                <h5 class="text-sm font-medium text-gray-600 mb-2">Brazil Listings</h5>
                                <pre class="bg-gray-100 p-4 rounded-lg overflow-auto text-xs" style="max-height: 400px;">{{ json_encode($brazilListings, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Logs Recentes -->
                @if(isset($logs) && count($logs) > 0)
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Logs da Consulta</h3>
                        <button onclick="toggleSection('logs-section')" class="text-sm text-blue-600 hover:text-blue-800">
                            Mostrar/Ocultar
                        </button>
                    </div>
                    <div id="logs-section" class="hidden">
                        <div class="bg-gray-50 p-4 rounded-lg overflow-auto" style="max-height: 400px;">
                            @foreach($logs as $log)
                                <div class="mb-4 pb-4 border-b border-gray-200 last:border-0">
                                    <div class="flex items-center mb-2">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $log['level'] == 'info' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }} mr-2">
                                            {{ strtoupper($log['level']) }}
                                        </span>
                                        <span class="text-sm text-gray-500">{{ $log['time'] }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 mb-1">{{ $log['message'] }}</p>
                                    @if(!empty($log['context']))
                                        <pre class="bg-gray-100 p-2 rounded text-xs overflow-auto">{{ json_encode($log['context'], JSON_PRETTY_PRINT) }}</pre>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
    
    <script>
        function toggleSection(id) {
            const element = document.getElementById(id);
            if (element.classList.contains('hidden')) {
                element.classList.remove('hidden');
            } else {
                element.classList.add('hidden');
            }
        }
    </script>
</x-admin-layout>
