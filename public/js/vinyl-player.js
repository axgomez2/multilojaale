/**
 * Vinyl Player - JavaScript responsável pelo controle do player de áudio
 * Permite reproduzir faixas de um disco de vinil usando a API do YouTube
 */
class VinylPlayer {
    constructor() {
        this.youtubePlayer = null;
        this.tracks = [];
        this.currentTrackIndex = 0;
        this.isPlaying = false;
        this.playerElement = document.getElementById('vinyl-player');
        this.progressBar = document.querySelector('#vinyl-player .progress-bar');
        this.currentTimeElement = document.querySelector('#vinyl-player .current-time');
        this.durationElement = document.querySelector('#vinyl-player .duration-time');
        this.titleElement = document.getElementById('track-title');
        this.artistElement = document.getElementById('track-artist');
        this.coverElement = document.getElementById('track-cover');
        
        // Log para debug
        console.log('Elementos encontrados:', {
            player: this.playerElement,
            title: this.titleElement,
            artist: this.artistElement,
            cover: this.coverElement,
            progressBar: this.progressBar,
            currentTime: this.currentTimeElement,
            duration: this.durationElement
        });
        this.youtubeContainer = document.getElementById('youtube-player-container');
        this.progressInterval = null;
        
        // Carrega a API do YouTube
        this.loadYouTubeAPI();
        this.initEvents();
    }

    loadYouTubeAPI() {
        // Adiciona o script da API do YouTube se ainda não estiver carregado
        if (!window.YT) {
            const tag = document.createElement('script');
            tag.src = 'https://www.youtube.com/iframe_api';
            const firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
            
            // Função que será chamada quando a API estiver pronta
            window.onYouTubeIframeAPIReady = () => {
                this.initYouTubePlayer();
            };
        } else if (window.YT.Player) {
            this.initYouTubePlayer();
        }
    }
    
    initYouTubePlayer() {
        // Cria um elemento div para o player do YouTube
        if (!document.getElementById('youtube-iframe')) {
            const youtubeIframe = document.createElement('div');
            youtubeIframe.id = 'youtube-iframe';
            this.youtubeContainer.appendChild(youtubeIframe);
        }
        
        // Inicializa o player do YouTube
        this.youtubePlayer = new YT.Player('youtube-iframe', {
            height: '0',
            width: '0',
            playerVars: {
                'controls': 0,
                'disablekb': 1,
                'fs': 0,
                'rel': 0,
                'modestbranding': 1
            },
            events: {
                'onReady': this.onPlayerReady.bind(this),
                'onStateChange': this.onPlayerStateChange.bind(this),
                'onError': this.onPlayerError.bind(this)
            }
        });
    }
    
    onPlayerReady(event) {
        console.log('YouTube player está pronto');
    }
    
    onPlayerStateChange(event) {
        // YT.PlayerState.ENDED = 0
        if (event.data === 0) {
            // A faixa terminou, tocar a próxima
            this.playNextTrack();
        } else if (event.data === 1) {
            // YT.PlayerState.PLAYING = 1
            this.isPlaying = true;
            this.updatePlayerState();
            this.startProgressInterval();
        } else if (event.data === 2) {
            // YT.PlayerState.PAUSED = 2
            this.isPlaying = false;
            this.updatePlayerState();
            this.stopProgressInterval();
        }
    }
    
    onPlayerError(event) {
        console.error('Erro no player do YouTube:', event);
        this.playNextTrack(); // Tenta tocar a próxima faixa em caso de erro
    }

    initEvents() {
        console.log('Inicializando eventos do player');
        
        // Botão de play/pause principal
        const playPauseBtn = document.querySelector('#vinyl-player .play-pause-btn');
        if (playPauseBtn) {
            console.log('Botão play/pause encontrado');
            playPauseBtn.addEventListener('click', () => this.togglePlayPause());
        } else {
            console.log('Botão play/pause NÃO encontrado');
        }

        // Botão de próxima faixa
        const nextBtn = document.querySelector('#vinyl-player .next-btn');
        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.playNextTrack());
        }

        // Botão de faixa anterior
        const prevBtn = document.querySelector('#vinyl-player .prev-btn');
        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.playPreviousTrack());
        }

        // Botão de reiniciar
        const restartBtn = document.querySelector('#vinyl-player .restart-btn');
        if (restartBtn) {
            restartBtn.addEventListener('click', () => this.restartTrack());
        }

        // Clique na barra de progresso
        if (this.progressBar) {
            this.progressBar.parentElement.addEventListener('click', (e) => this.seekTo(e));
        }

        // Esconder o player quando fechado
        const closeBtn = document.querySelector('#vinyl-player .close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.hidePlayer());
        }
    }

    /**
     * Carrega e reproduz as faixas de um vinil
     * @param {number} vinylId - ID do vinil
     * @param {string} vinylTitle - Título do vinil
     * @param {string} vinylArtist - Artista do vinil
     * @param {string} vinylCover - URL da capa do vinil
     */
    loadVinylTracks(vinylId, vinylTitle, vinylArtist, vinylCover) {
        console.log('loadVinylTracks chamada com:', { vinylId, vinylTitle, vinylArtist, vinylCover });
        
        // Mostrar o player
        this.showPlayer();
        
        // Atualizar informações do vinil no player
        console.log('Atualizando informações no player:', { 
            titleElement: this.titleElement, 
            artistElement: this.artistElement, 
            coverElement: this.coverElement 
        });
        
        if (this.titleElement) this.titleElement.textContent = vinylTitle || 'Título não disponível';
        if (this.artistElement) this.artistElement.textContent = vinylArtist || 'Artista não disponível';
        if (this.coverElement) {
            this.coverElement.src = vinylCover || '{{ asset("assets/images/placeholder.jpg") }}';
            console.log('Capa atualizada para:', this.coverElement.src);
        }

        // Buscar as faixas do vinil via AJAX
        const apiUrl = `/api/vinyl/${vinylId}/tracks`;
        console.log('Buscando faixas na API:', apiUrl);
        
        fetch(apiUrl)
            .then(response => {
                console.log('Resposta da API:', response);
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Dados recebidos da API:', data);
                if (data.tracks && data.tracks.length > 0) {
                    console.log(`${data.tracks.length} faixas encontradas`);
                    this.tracks = data.tracks;
                    this.currentTrackIndex = 0;
                    this.loadTrack(this.currentTrackIndex);
                    this.play();
                } else {
                    console.error('Nenhuma faixa encontrada para este vinil');
                    this.showMessage('Nenhuma faixa disponível para este vinil');
                }
            })
            .catch(error => {
                console.error('Erro ao carregar faixas:', error);
                this.showMessage('Erro ao carregar faixas: ' + error.message);
            });
    }



/**
 * Carrega e reproduz as faixas de um vinil
 * @param {number} vinylId - ID do vinil
 * @param {string} vinylTitle - Título do vinil
 * @param {string} vinylArtist - Artista do vinil
 * @param {string} vinylCover - URL da capa do vinil
 */
loadVinylTracks(vinylId, vinylTitle, vinylArtist, vinylCover) {
    console.log('loadVinylTracks chamada com:', { vinylId, vinylTitle, vinylArtist, vinylCover });
        
    // Mostrar o player
    this.showPlayer();
        
    // Atualizar informações do vinil no player
    console.log('Atualizando informações no player:', { 
        titleElement: this.titleElement, 
        artistElement: this.artistElement, 
        coverElement: this.coverElement 
    });
        
    if (this.titleElement) this.titleElement.textContent = vinylTitle || 'Título não disponível';
    if (this.artistElement) this.artistElement.textContent = vinylArtist || 'Artista não disponível';
    if (this.coverElement) {
        this.coverElement.src = vinylCover || '{{ asset("assets/images/placeholder.jpg") }}';
        console.log('Capa atualizada para:', this.coverElement.src);
    }

    // Buscar as faixas do vinil via AJAX
    const apiUrl = `/api/vinyl/${vinylId}/tracks`;
    console.log('Buscando faixas na API:', apiUrl);
        
    fetch(apiUrl)
        .then(response => {
            console.log('Resposta da API:', response);
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Dados recebidos da API:', data);
            if (data.tracks && data.tracks.length > 0) {
                console.log(`${data.tracks.length} faixas encontradas`);
                this.tracks = data.tracks;
                this.currentTrackIndex = 0;
                this.loadTrack(this.currentTrackIndex);
                this.play();
            } else {
                console.error('Nenhuma faixa encontrada para este vinil');
                this.showMessage('Nenhuma faixa disponível para este vinil');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar faixas:', error);
            this.showMessage('Erro ao carregar faixas: ' + error.message);
        });
}

/**
 * Carrega uma faixa específica
 * @param {number} index - Índice da faixa a ser carregada
 */
loadTrack(index) {
    console.log('loadTrack chamado com índice:', index);
    console.log('Total de faixas disponíveis:', this.tracks.length);
        
    if (index >= 0 && index < this.tracks.length) {
        const track = this.tracks[index];
        console.log('Faixa selecionada:', track);
            
        // Atualizar título da faixa se disponível
        if (this.titleElement && track.title) {
            console.log('Atualizando título para:', track.title);
            this.titleElement.textContent = track.title;
        }
            
        // Extrair ID do YouTube da URL
        const youtubeId = this.extractYouTubeID(track.youtube_url);
        console.log('URL do YouTube:', track.youtube_url);
        console.log('ID extraído do YouTube:', youtubeId);
            
        if (youtubeId && this.youtubePlayer) {
            console.log('Carregando vídeo com ID:', youtubeId);
            try {
                // Carrega o vídeo no player
                this.youtubePlayer.loadVideoById(youtubeId);
                console.log('Vídeo carregado no player');
                    
                this.currentTrackIndex = index;
                this.updatePlayerState();
            } catch (error) {
                console.error('Erro ao carregar vídeo:', error);
                this.playNextTrack();
            }
        } else {
            console.error('ID do YouTube não encontrado ou player não inicializado');
            console.log('youtubeId:', youtubeId);
            console.log('youtubePlayer:', this.youtubePlayer);
            // Tentar próxima faixa
            this.playNextTrack();
        }
    } else {
        console.error('Índice de faixa inválido:', index);
    }
}

/**
 * Alterna entre reproduzir e pausar a faixa atual
 */
togglePlayPause() {
    if (this.isPlaying) {
        this.pause();
    } else {
        this.play();
    }
}

/**
 * Inicia a reprodução da faixa atual
 */
play() {
    if (this.audioElement && this.audioElement.src) {
        this.audioElement.play();
        this.isPlaying = true;
        this.updatePlayerState();
    }
}

/**
 * Pausa a reprodução da faixa atual
 */
pause() {
    if (this.audioElement) {
        this.audioElement.pause();
        this.isPlaying = false;
        this.updatePlayerState();
    }
}

/**
 * Reproduz a próxima faixa
 */
playNextTrack() {
    const nextIndex = this.currentTrackIndex + 1;
    if (nextIndex < this.tracks.length) {
        this.loadTrack(nextIndex);
        this.play();
    } else {
        // Voltar para a primeira faixa quando chegar ao final
        this.loadTrack(0);
        this.play();
    }
}

/**
 * Reproduz a faixa anterior
 */
playPreviousTrack() {
    const prevIndex = this.currentTrackIndex - 1;
    if (prevIndex >= 0) {
        this.loadTrack(prevIndex);
        this.play();
    } else {
        // Ir para a última faixa se estiver na primeira
        this.loadTrack(this.tracks.length - 1);
        this.play();
    }
}

/**
 * Reinicia a faixa atual
 */
restartTrack() {
    if (this.audioElement) {
        this.audioElement.currentTime = 0;
        this.play();
    }
}

/**
 * Avança ou retrocede na faixa atual
 * @param {Event} event - Evento de clique na barra de progresso
 */
seekTo(event) {
    if (this.audioElement && this.audioElement.duration) {
        const progressBarWidth = event.currentTarget.clientWidth;
        const clickPosition = event.offsetX;
        const percentage = clickPosition / progressBarWidth;
        this.audioElement.currentTime = this.audioElement.duration * percentage;
    }
}

/**
 * Atualiza a barra de progresso durante a reprodução
 */
updateProgress() {
    if (this.audioElement && this.progressBar && this.currentTimeElement) {
        const currentTime = this.audioElement.currentTime;
        const duration = this.audioElement.duration;
            
        if (duration) {
            const progressPercentage = (currentTime / duration) * 100;
            this.progressBar.style.width = `${progressPercentage}%`;
                
            // Atualizar o tempo atual
            this.currentTimeElement.textContent = this.formatTime(currentTime);
        }
    }
}

/**
 * Atualiza a duração total da faixa
 */
updateDuration() {
    if (this.audioElement && this.durationElement) {
        this.durationElement.textContent = this.formatTime(this.audioElement.duration);
    }
}

/**
 * Formata o tempo em minutos e segundos
 * @param {number} timeInSeconds - Tempo em segundos
 * @returns {string} Tempo formatado (MM:SS)
 */
formatTime(timeInSeconds) {
    if (!timeInSeconds || isNaN(timeInSeconds)) return '0:00';
        
    const minutes = Math.floor(timeInSeconds / 60);
    const seconds = Math.floor(timeInSeconds % 60);
    return `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
}

/**
 * Atualiza o estado visual do player (play/pause)
 */
updatePlayerState() {
    const playPauseBtn = document.querySelector('#vinyl-player .play-pause-btn');
    const playIcon = document.querySelector('#vinyl-player .play-icon');
    const pauseIcon = document.querySelector('#vinyl-player .pause-icon');
        
    if (playPauseBtn && playIcon && pauseIcon) {
        if (this.isPlaying) {
            playIcon.classList.add('hidden');
            pauseIcon.classList.remove('hidden');
        } else {
            playIcon.classList.remove('hidden');
            pauseIcon.classList.add('hidden');
        }
    }

    /**
     * Inicia a reprodução da faixa atual
     */
    play() {
        if (this.audioElement && this.audioElement.src) {
            this.audioElement.play();
            this.isPlaying = true;
            this.updatePlayerState();
        }
    }

    /**
     * Pausa a reprodução da faixa atual
     */
    pause() {
        if (this.audioElement) {
            this.audioElement.pause();
            this.isPlaying = false;
            this.updatePlayerState();
        }
    }

    /**
     * Reproduz a próxima faixa
     */
    playNextTrack() {
        const nextIndex = this.currentTrackIndex + 1;
        if (nextIndex < this.tracks.length) {
            this.loadTrack(nextIndex);
            this.play();
        } else {
            // Voltar para a primeira faixa quando chegar ao final
            this.loadTrack(0);
            this.play();
        }
    }

    /**
     * Reproduz a faixa anterior
     */
    playPreviousTrack() {
        const prevIndex = this.currentTrackIndex - 1;
        if (prevIndex >= 0) {
            this.loadTrack(prevIndex);
            this.play();
        } else {
            // Ir para a última faixa se estiver na primeira
            this.loadTrack(this.tracks.length - 1);
            this.play();
        }
    }

    /**
     * Reinicia a faixa atual
     */
    restartTrack() {
        if (this.audioElement) {
            this.audioElement.currentTime = 0;
            this.play();
        }
    }

    /**
     * Avança ou retrocede na faixa atual
     * @param {Event} event - Evento de clique na barra de progresso
     */
    seekTo(event) {
        if (this.audioElement && this.audioElement.duration) {
            const progressBarWidth = event.currentTarget.clientWidth;
            const clickPosition = event.offsetX;
            const percentage = clickPosition / progressBarWidth;
            this.audioElement.currentTime = this.audioElement.duration * percentage;
        }
    }

    /**
     * Atualiza a barra de progresso durante a reprodução
     */
    updateProgress() {
        if (this.audioElement && this.progressBar && this.currentTimeElement) {
            const currentTime = this.audioElement.currentTime;
            const duration = this.audioElement.duration;
            
            if (duration) {
                const progressPercentage = (currentTime / duration) * 100;
                this.progressBar.style.width = `${progressPercentage}%`;
                
                // Atualizar o tempo atual
                this.currentTimeElement.textContent = this.formatTime(currentTime);
            }
        }
    }

    /**
     * Atualiza a duração total da faixa
     */
    updateDuration() {
        if (this.audioElement && this.durationElement) {
            this.durationElement.textContent = this.formatTime(this.audioElement.duration);
        }
    }

    /**
     * Formata o tempo em minutos e segundos
     * @param {number} timeInSeconds - Tempo em segundos
     * @returns {string} Tempo formatado (MM:SS)
     */
    formatTime(timeInSeconds) {
        if (!timeInSeconds || isNaN(timeInSeconds)) return '0:00';
        
        const minutes = Math.floor(timeInSeconds / 60);
        const seconds = Math.floor(timeInSeconds % 60);
        return `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
    }

    /**
     * Atualiza o estado visual do player (play/pause)
     */
    updatePlayerState() {
        const playPauseBtn = document.querySelector('#vinyl-player .play-pause-btn');
        const playIcon = document.querySelector('#vinyl-player .play-icon');
        const pauseIcon = document.querySelector('#vinyl-player .pause-icon');
        
        if (playPauseBtn && playIcon && pauseIcon) {
            if (this.isPlaying) {
                playIcon.classList.add('hidden');
                pauseIcon.classList.remove('hidden');
            } else {
                playIcon.classList.remove('hidden');
                pauseIcon.classList.add('hidden');
            }
        }
    }

    /**
     * Exibe o player
     */
    showPlayer() {
        console.log('Tentando mostrar o player');
        // Buscar o elemento novamente caso this.playerElement esteja null
        const playerEl = this.playerElement || document.getElementById('vinyl-player');
        
        if (playerEl) {
            console.log('Elemento do player encontrado, removendo translate-y-full');
            playerEl.classList.remove('translate-y-full');
            playerEl.classList.add('translate-y-0');
            
            // Atualizar a referência caso tenha sido buscada novamente
            if (!this.playerElement) {
                this.playerElement = playerEl;
            }
        } else {
            console.error('Elemento do player não encontrado!');
        }
    }

    /**
     * Esconde o player
     */
    hidePlayer() {
        if (this.playerElement) {
            this.playerElement.classList.remove('translate-y-0');
            this.playerElement.classList.add('translate-y-full');
            this.pause();
        }
    }



    /**
     * Exibe uma mensagem no player
     * @param {string} message - Mensagem a ser exibida
     */
    showMessage(message) {
        if (this.titleElement) {
            this.titleElement.textContent = message;
        }
    }
}

// Inicializar o player quando o documento estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    window.vinylPlayer = new VinylPlayer();
    
    // Adicionar evento nos botões de play dos vinyl cards
    document.querySelectorAll('[x-on\\:click="playAudio"]').forEach(button => {
        button.addEventListener('click', function(e) {
            const vinylCard = this.closest('[x-data="vinylCard"]');
            if (vinylCard) {
                const vinylId = vinylCard.getAttribute('x-init').match(/vinylId\s*=\s*'([^']+)'/)?.[1];
                const vinylTitle = vinylCard.getAttribute('x-init').match(/vinylTitle\s*=\s*'([^']+)'/)?.[1];
                const vinylArtist = vinylCard.getAttribute('x-init').match(/vinylArtist\s*=\s*'([^']+)'/)?.[1];
                const vinylCover = vinylCard.getAttribute('x-init').match(/vinylCover\s*=\s*'([^']+)'/)?.[1];
                
                if (vinylId && window.vinylPlayer) {
                    window.vinylPlayer.loadVinylTracks(vinylId, vinylTitle, vinylArtist, vinylCover);
                }
            }
        });
    });
});

// Alpine.js component for vinyl-card
document.addEventListener('alpine:init', () => {
    Alpine.data('vinylCard', () => ({
        vinylId: null,
        vinylTitle: null,
        vinylArtist: null,
        vinylCover: null,
        
        playAudio() {
            console.log('playAudio chamado com:', {
                vinylId: this.vinylId,
                vinylTitle: this.vinylTitle,
                vinylArtist: this.vinylArtist,
                vinylCover: this.vinylCover
            });
            
            if (window.vinylPlayer) {
                // Primeiro, vamos mostrar o player
                if (typeof window.vinylPlayer.showPlayer === 'function') {
                    console.log('Chamando função showPlayer');
                    window.vinylPlayer.showPlayer();
                } else {
                    console.error('Função showPlayer não encontrada!');
                    // Fallback: tentar mostrar o player manualmente
                    const playerEl = document.getElementById('vinyl-player');
                    if (playerEl) {
                        playerEl.classList.remove('translate-y-full');
                        playerEl.classList.add('translate-y-0');
                    }
                }
                
                // Se temos um ID válido, carregamos as faixas
                if (this.vinylId) {
                    console.log('Carregando faixas para o vinil:', this.vinylId);
                    window.vinylPlayer.loadVinylTracks(this.vinylId, this.vinylTitle, this.vinylArtist, this.vinylCover);
                }
            } else {
                console.error('vinylPlayer não inicializado!');
            }
        }
    }));
});
