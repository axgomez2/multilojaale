@props(['searchResults', 'query'])

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