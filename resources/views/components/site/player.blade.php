<div id="vinyl-player" x-data="vinylPlayer" x-bind:class="{'translate-y-0': isOpen, 'translate-y-full': !isOpen}" class="fixed bottom-0 left-0 z-50 w-full h-20 bg-white border-t border-gray-200 shadow-lg transition-transform duration-300 ease-in-out transform">
    <!-- Container invisível para o player do YouTube -->
    <div id="youtube-player-container" class="hidden"></div>
    <div class="  grid w-full grid-cols-1 px-4 py-3 md:grid-cols-3 md:py-4">
        <!-- Informações da faixa (capa, título e artista) -->
        <div class="flex items-center justify-start mb-3 md:mb-0">
            <img class="h-12 w-12 me-3 rounded-sm object-cover" 
                :src="currentVinylCover" :alt="currentVinylTitle">
            <div class="flex flex-col">
                <span class="text-sm font-medium text-gray-900 dark:text-white line-clamp-1" x-text="currentTrackName">Nenhuma faixa selecionada</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1" x-text="currentVinylArtist"></span>
                <span class="text-xs text-purple-600 dark:text-purple-400 line-clamp-1" x-text="currentVinylTitle"></span>
            </div>
        </div>

        <!-- Controles do player e barra de progresso -->
        <div class="flex flex-col items-center w-full">
            <div class="flex items-center justify-center mx-auto mb-2">
                <!-- Botão anterior -->
                <button type="button" @click="prevTrack()" class="p-2 group rounded-full hover:bg-gray-100 prev-btn focus:outline-none focus:ring-2 focus:ring-purple-300 dark:focus:ring-purple-600 dark:hover:bg-gray-700">
                    <svg class="rtl:rotate-180 w-4 h-4 text-gray-600 dark:text-gray-300 group-hover:text-purple-600 dark:group-hover:text-purple-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 12 16">
                        <path d="M10.819.4a1.974 1.974 0 0 0-2.147.33l-6.5 5.773A2.014 2.014 0 0 0 2 6.7V1a1 1 0 0 0-2 0v14a1 1 0 1 0 2 0V9.3c.055.068.114.133.177.194l6.5 5.773a1.982 1.982 0 0 0 2.147.33A1.977 1.977 0 0 0 12 13.773V2.227A1.977 1.977 0 0 0 10.819.4Z"/>
                    </svg>
                    <span class="sr-only">Faixa anterior</span>
                </button>

                <!-- Botão play/pause -->
                <button type="button" @click="togglePlayPause()" class="inline-flex items-center justify-center p-2.5 mx-2 bg-purple-600 rounded-full hover:bg-purple-700 play-pause-btn focus:ring-2 focus:ring-purple-300 focus:outline-none dark:focus:ring-purple-800">
                    <!-- Ícone play (visível quando não estiver tocando) -->
                    <svg class="w-4 h-4 text-white play-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 10 14">
                        <path d="M0 0v14l10-7L0 0z"/>
                    </svg>
                    <!-- Ícone pause (visível quando estiver tocando) -->
                    <svg class="w-4 h-4 text-white pause-icon hidden" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 10 16">
                        <path fill-rule="evenodd" d="M0 .8C0 .358.32 0 .714 0h1.429c.394 0 .714.358.714.8v14.4c0 .442-.32.8-.714.8H.714a.678.678 0 0 1-.505-.234A.851.851 0 0 1 0 15.2V.8Zm7.143 0c0-.442.32-.8.714-.8h1.429c.19 0 .37.084.505.234.134.15.209.354.209.566v14.4c0 .442-.32.8-.714.8H7.857c-.394 0-.714-.358-.714-.8V.8Z" clip-rule="evenodd"/>
                    </svg>
                    <span class="sr-only">Reproduzir/Pausar</span>
                </button>

                <!-- Botão próximo -->
                <button type="button" @click="nextTrack()" class="p-2 group rounded-full hover:bg-gray-100 next-btn focus:outline-none focus:ring-2 focus:ring-purple-300 dark:focus:ring-purple-600 dark:hover:bg-gray-700">
                    <svg class="rtl:rotate-180 w-4 h-4 text-gray-600 dark:text-gray-300 group-hover:text-purple-600 dark:group-hover:text-purple-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 12 16">
                        <path d="M11.78 9.78a1.969 1.969 0 0 0 0-3.56L3.523.94A1.6 1.6 0 0 0 2 2.327v11.345a1.6 1.6 0 0 0 1.523 1.388 1.6 1.6 0 0 0 1.17-.5l7.087-5.282Z"/>
                    </svg>
                    <span class="sr-only">Próxima faixa</span>
                </button>

                <!-- Botão reiniciar -->
                <button type="button" @click="restartTrack()" class="p-2 group rounded-full hover:bg-gray-100 restart-btn focus:outline-none focus:ring-2 focus:ring-purple-300 dark:focus:ring-purple-600 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300 group-hover:text-purple-600 dark:group-hover:text-purple-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 1v5h-5M2 19v-5h5m10-4a8 8 0 0 1-14.947 3.97M1 10a8 8 0 0 1 14.947-3.97"/>
                    </svg>
                    <span class="sr-only">Reiniciar faixa</span>
                </button>
            </div>

            <!-- Barra de progresso -->
            <div class="flex items-center justify-between w-full px-2 space-x-2 rtl:space-x-reverse">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 current-time" x-text="currentTimeFormatted">0:00</span>
                <div class="w-full h-1.5 bg-gray-200 rounded-full cursor-pointer dark:bg-gray-700" @click="seekTo($event)">
                    <div class="bg-purple-600 h-1.5 rounded-full progress-bar" style="width: 0%"></div>
                </div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 duration-time" x-text="durationFormatted">0:00</span>
            </div>
        </div>

        <!-- Controles à direita (volume e fechar) -->
        <div class="flex items-center justify-end mt-3 md:mt-0">
            <!-- Controle de volume -->
            <div class="hidden md:flex items-center mr-4">
                <button type="button" @click="toggleMute()" class="p-2 group rounded-full hover:bg-gray-100 mute-btn focus:outline-none focus:ring-2 focus:ring-purple-300 dark:focus:ring-purple-600 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300 group-hover:text-purple-600 dark:group-hover:text-purple-400 volume-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                        <path d="M10.836.357a1.978 1.978 0 0 0-2.138.3L3.63 5H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h1.63l5.07 4.344a1.985 1.985 0 0 0 2.142.299A1.98 1.98 0 0 0 12 15.826V2.174A1.98 1.98 0 0 0 10.836.357Zm2.728 4.695a1.001 1.001 0 0 0-.29 1.385 4.887 4.887 0 0 1 0 5.126 1 1 0 0 0 1.674 1.095A6.645 6.645 0 0 0 16 9a6.65 6.65 0 0 0-1.052-3.658 1 1 0 0 0-1.384-.29Z"/>
                    </svg>
                    <span class="sr-only">Controle de volume</span>
                </button>
                <input type="range" min="0" max="100" x-model="volume" @input="setVolume(parseInt($event.target.value))" class="volume-range w-20 h-1.5 bg-gray-200 rounded-full mx-2 cursor-pointer dark:bg-gray-700 appearance-none [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:h-3 [&::-webkit-slider-thumb]:w-3 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-purple-600">
            </div>

            <!-- Botão fechar -->
            <button type="button" class="p-2 group rounded-full hover:bg-gray-100 close-btn focus:outline-none focus:ring-2 focus:ring-purple-300 dark:focus:ring-purple-600 dark:hover:bg-gray-700" @click="hidePlayer()">
                <svg class="w-4 h-4 text-gray-600 dark:text-gray-300 group-hover:text-purple-600 dark:group-hover:text-purple-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m13 7-6 6m0-6 6 6m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <span class="sr-only">Fechar player</span>
            </button>
        </div>
    </div>

    <!-- Iframe invisível para reprodução de áudio do Youtube -->
    <div id="youtube-player-container" class="hidden">
        <!-- O iframe do player do YouTube será inserido aqui via JavaScript -->
    </div>
</div>
