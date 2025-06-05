/**
 * Vinyl Player - Implementação do player de vinis com controle de faixas e integração com YouTube
 */

import Alpine from 'alpinejs';

document.addEventListener('alpine:init', () => {
    Alpine.data('vinylPlayer', () => ({
        isOpen: false,
        currentVinylId: null,
        currentVinylTitle: 'Nenhum disco selecionado',
        currentVinylArtist: '',
        currentVinylCover: '/assets/images/placeholder.jpg',
        tracks: [],
        currentTrackIndex: 0,
        currentTrackName: 'Nenhuma faixa selecionada',
        youtubePlayer: null,
        isPlaying: false,
        duration: 0,
        currentTime: 0,
        currentTimeFormatted: '0:00',
        durationFormatted: '0:00',
        progressInterval: null,
        volume: 80,
        
        init() {
            console.log('Player Alpine.js inicializado');
            
            // Inicializar a API do YouTube
            if (window.YT && window.YT.Player) {
                this.initYouTubePlayer();
            } else {
                // Se a API do YouTube ainda não estiver carregada, adicionar script
                if (!document.getElementById('youtube-api')) {
                    const tag = document.createElement('script');
                    tag.id = 'youtube-api';
                    tag.src = 'https://www.youtube.com/iframe_api';
                    const firstScriptTag = document.getElementsByTagName('script')[0];
                    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
                    
                    // Definir callback global para quando a API estiver pronta
                    window.onYouTubeIframeAPIReady = () => {
                        this.initYouTubePlayer();
                    };
                }
            }
            
            // Listener para tocar faixa específica por URL
            window.addEventListener('play-specific-track', (event) => {
                const { youtubeUrl, trackName, vinylId, vinylTitle, vinylArtist, vinylCover } = event.detail;
                this.playSpecificTrack(youtubeUrl, trackName, { vinylId, vinylTitle, vinylArtist, vinylCover });
            });
        },
        
        // Inicializar o player do YouTube
        initYouTubePlayer() {
            console.log('Inicializando YouTube Player');
            const playerContainer = document.getElementById('youtube-player-container');
            if (playerContainer) {
                this.youtubePlayer = new YT.Player('youtube-player-container', {
                    height: '0',
                    width: '0',
                    playerVars: {
                        'autoplay': 0,
                        'controls': 0,
                    },
                    events: {
                        'onReady': this.onPlayerReady.bind(this),
                        'onStateChange': this.onPlayerStateChange.bind(this)
                    }
                });
            } else {
                console.error('Container do player do YouTube não encontrado');
            }
        },
        
        // Callback quando o player estiver pronto
        onPlayerReady(event) {
            console.log('YouTube Player pronto');
            // Definir volume inicial
            this.youtubePlayer.setVolume(this.volume);
            // Se já tiver faixas carregadas, carregar a primeira
            if (this.tracks.length > 0) {
                this.loadCurrentTrack();
            }
        },
        
        // Formatar tempo em segundos para minutos:segundos
        formatTime(seconds) {
            if (isNaN(seconds) || seconds < 0) return '0:00';
            seconds = Math.floor(seconds);
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
        },
        
        // Tocar uma faixa específica por URL do YouTube
        playSpecificTrack(youtubeUrl, trackName, vinylInfo = {}) {
            console.log('Tocando faixa específica:', trackName, youtubeUrl);
            
            if (!youtubeUrl) {
                console.warn('URL do YouTube não fornecida');
                return;
            }
            
            // Extrair o ID do YouTube
            const youtubeId = this.extractYouTubeID(youtubeUrl);
            if (!youtubeId) {
                console.warn('Não foi possível extrair o ID do YouTube da URL:', youtubeUrl);
                return;
            }
            
            // Mostrar o player se estiver oculto
            this.isOpen = true;
            
            // Atualizar informações da faixa atual
            this.currentTrackName = trackName || 'Faixa sem nome';
            
            // Atualizar as informações do vinil se fornecidas
            if (vinylInfo.vinylId) {
                this.currentVinylId = vinylInfo.vinylId;
            }
            
            if (vinylInfo.vinylTitle) {
                this.currentVinylTitle = vinylInfo.vinylTitle;
            }
            
            if (vinylInfo.vinylArtist) {
                this.currentVinylArtist = vinylInfo.vinylArtist;
            }
            
            if (vinylInfo.vinylCover) {
                this.currentVinylCover = vinylInfo.vinylCover;
            }
            
            // Carregar e reproduzir o vídeo do YouTube
            if (this.youtubePlayer) {
                this.youtubePlayer.loadVideoById(youtubeId);
                this.isPlaying = true;
                this.startProgressInterval();
                this.updatePlayPauseButton();
            } else {
                console.warn('Player do YouTube ainda não inicializado');
            }
        },
        
        // Iniciar o intervalo para atualizar o progresso
        startProgressInterval() {
            // Limpar intervalo anterior se existir
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
            }
            
            this.progressInterval = setInterval(() => {
                if (this.youtubePlayer && this.isPlaying) {
                    // Atualizar tempo atual
                    this.currentTime = this.youtubePlayer.getCurrentTime() || 0;
                    this.currentTimeFormatted = this.formatTime(this.currentTime);
                    
                    // Atualizar duração se ainda não definida
                    if (!this.duration || this.duration === 0) {
                        this.duration = this.youtubePlayer.getDuration() || 0;
                        this.durationFormatted = this.formatTime(this.duration);
                    }
                    
                    // Calcular e atualizar a largura da barra de progresso
                    const progressPercent = (this.currentTime / this.duration) * 100;
                    const progressBar = document.querySelector('.progress-bar');
                    if (progressBar) {
                        progressBar.style.width = `${progressPercent}%`;
                    }
                    
                    // Atualizar os elementos de texto de tempo
                    const currentTimeElement = document.querySelector('.current-time');
                    const durationTimeElement = document.querySelector('.duration-time');
                    
                    if (currentTimeElement) {
                        currentTimeElement.textContent = this.currentTimeFormatted;
                    }
                    
                    if (durationTimeElement) {
                        durationTimeElement.textContent = this.durationFormatted;
                    }
                }
            }, 1000); // Atualizar a cada segundo
        },
        
        // Parar o intervalo de progresso
        stopProgressInterval() {
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
                this.progressInterval = null;
            }
        },
        
        // Definir a posição do vídeo ao clicar na barra de progresso
        seekTo(event) {
            if (this.youtubePlayer && this.duration > 0) {
                const progressBar = event.currentTarget;
                const rect = progressBar.getBoundingClientRect();
                const clickPosition = (event.clientX - rect.left) / rect.width;
                const seekToTime = clickPosition * this.duration;
                
                // Definir a posição do player
                this.youtubePlayer.seekTo(seekToTime, true);
                
                // Atualizar a barra de progresso imediatamente
                const progressPercent = clickPosition * 100;
                const progressBarFill = document.querySelector('.progress-bar');
                if (progressBarFill) {
                    progressBarFill.style.width = `${progressPercent}%`;
                }
                
                // Atualizar o tempo atual
                this.currentTime = seekToTime;
                this.currentTimeFormatted = this.formatTime(seekToTime);
                const currentTimeElement = document.querySelector('.current-time');
                if (currentTimeElement) {
                    currentTimeElement.textContent = this.currentTimeFormatted;
                }
            }
        },
        
        // Controle de volume
        setVolume(volumeLevel) {
            if (volumeLevel < 0) volumeLevel = 0;
            if (volumeLevel > 100) volumeLevel = 100;
            
            this.volume = volumeLevel;
            if (this.youtubePlayer) {
                this.youtubePlayer.setVolume(volumeLevel);
            }
            
            // Atualizar a UI do controle de volume
            const volumeRangeElement = document.querySelector('.volume-range');
            if (volumeRangeElement) {
                volumeRangeElement.value = volumeLevel;
            }
        },
        
        // Alternar mudo/som
        toggleMute() {
            if (!this.youtubePlayer) return;
            
            if (this.youtubePlayer.isMuted()) {
                this.youtubePlayer.unMute();
                this.setVolume(this.volume || 50);
            } else {
                this.youtubePlayer.mute();
                const volumeRangeElement = document.querySelector('.volume-range');
                if (volumeRangeElement) {
                    volumeRangeElement.value = 0;
                }
            }
        },
        
        // Alternar play/pause
        togglePlayPause() {
            if (!this.youtubePlayer) {
                console.warn('Player do YouTube ainda não inicializado');
                return;
            }
            
            if (this.isPlaying) {
                // Pausar
                this.youtubePlayer.pauseVideo();
                this.isPlaying = false;
                this.stopProgressInterval();
            } else {
                // Reproduzir
                if (this.tracks.length === 0) {
                    console.warn('Nenhuma faixa disponível para reprodução');
                    return;
                }
                
                this.youtubePlayer.playVideo();
                this.isPlaying = true;
                this.startProgressInterval();
            }
            
            this.updatePlayPauseButton();
        },
        
        // Callback para mudanças de estado do player
        onPlayerStateChange(event) {
            // YT.PlayerState.ENDED = 0, YT.PlayerState.PLAYING = 1, YT.PlayerState.PAUSED = 2
            if (event.data === 0) { // Terminou a reprodução
                console.log('Faixa terminada, passando para a próxima');
                this.nextTrack();
            } else if (event.data === 1) { // Começou a reproduzir
                this.isPlaying = true;
                this.updatePlayPauseButton();
            } else if (event.data === 2) { // Pausado
                this.isPlaying = false;
                this.updatePlayPauseButton();
            }
        },
        
        showPlayer(vinylId, vinylTitle, vinylArtist, vinylCover) {
            console.log('Abrindo player para:', vinylTitle, 'por', vinylArtist);
            
            // Atualizar as informações do vinil atual, se fornecidas
            if (vinylId) {
                this.currentVinylId = vinylId;
                // Carregar as faixas do vinil
                this.loadTracks(vinylId);
            }
            if (vinylTitle) this.currentVinylTitle = vinylTitle;
            if (vinylArtist) this.currentVinylArtist = vinylArtist;
            if (vinylCover) this.currentVinylCover = vinylCover;
            
            const playerElement = document.getElementById('vinyl-player');
            if (playerElement) {
                playerElement.classList.remove('translate-y-full');
                playerElement.classList.add('translate-y-0');
                this.isOpen = true;
            }
        },
        
        hidePlayer() {
            console.log('Fechando player');
            // Pausar a reprodução ao fechar o player
            if (this.youtubePlayer && this.isPlaying) {
                this.youtubePlayer.pauseVideo();
                this.isPlaying = false;
                this.updatePlayPauseButton();
            }
            
            // Parar o intervalo de atualização do progresso
            this.stopProgressInterval();
            
            const playerElement = document.getElementById('vinyl-player');
            if (playerElement) {
                playerElement.classList.remove('translate-y-0');
                playerElement.classList.add('translate-y-full');
                this.isOpen = false;
            }
        },
        
        // Carregar faixas do vinil via API
        loadTracks(vinylId) {
            console.log('Carregando faixas para o vinil ID:', vinylId);
            fetch(`/api/vinyl/${vinylId}/tracks`)
                .then(response => response.json())
                .then(data => {
                    if (data.tracks && data.tracks.length > 0) {
                        this.tracks = data.tracks;
                        this.currentTrackIndex = 0;
                        this.updateCurrentTrack();
                        console.log('Faixas carregadas:', this.tracks.length);
                        
                        // Iniciar reprodução da primeira faixa
                        this.loadCurrentTrack();
                    } else {
                        console.log('Nenhuma faixa encontrada para este vinil');
                        this.tracks = [];
                        this.currentTrackName = 'Nenhuma faixa disponível';
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar faixas:', error);
                    this.tracks = [];
                    this.currentTrackName = 'Erro ao carregar faixas';
                });
        },
        
        // Extrair ID do YouTube de uma URL
        extractYouTubeID(url) {
            if (!url) return null;
            const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            const match = url.match(regExp);
            return (match && match[2].length === 11) ? match[2] : null;
        },
        
        // Carregar a faixa atual no player do YouTube
        loadCurrentTrack() {
            if (this.tracks.length > 0 && this.currentTrackIndex >= 0 && this.currentTrackIndex < this.tracks.length) {
                const track = this.tracks[this.currentTrackIndex];
                const youtubeId = this.extractYouTubeID(track.youtube_url);
                
                if (youtubeId && this.youtubePlayer) {
                    console.log('Carregando faixa do YouTube ID:', youtubeId);
                    // Reiniciar valores de progresso
                    this.duration = 0;
                    this.currentTime = 0;
                    this.currentTimeFormatted = '0:00';
                    this.durationFormatted = '0:00';
                    
                    // Carregar vídeo
                    this.youtubePlayer.loadVideoById(youtubeId);
                    this.isPlaying = true;
                    this.updatePlayPauseButton();
                    
                    // Iniciar intervalo de progresso
                    this.startProgressInterval();
                } else if (!youtubeId) {
                    console.log('URL do YouTube inválida para esta faixa');
                } else if (!this.youtubePlayer) {
                    console.log('Player do YouTube ainda não inicializado');
                }
            }
        },
        
        // Atualizar a aparência do botão de play/pause
        updatePlayPauseButton() {
            const playIcon = document.querySelector('.play-icon');
            const pauseIcon = document.querySelector('.pause-icon');
            
            if (playIcon && pauseIcon) {
                if (this.isPlaying) {
                    playIcon.classList.add('hidden');
                    pauseIcon.classList.remove('hidden');
                } else {
                    playIcon.classList.remove('hidden');
                    pauseIcon.classList.add('hidden');
                }
            }
        },
        
        // Atualizar informações da faixa atual
        updateCurrentTrack() {
            if (this.tracks.length > 0 && this.currentTrackIndex >= 0 && this.currentTrackIndex < this.tracks.length) {
                const track = this.tracks[this.currentTrackIndex];
                this.currentTrackName = track.title || track.name;
                console.log('Faixa atual:', this.currentTrackName, 'Índice:', this.currentTrackIndex);
            } else {
                this.currentTrackName = 'Nenhuma faixa selecionada';
            }
        },
        
        // Ir para a próxima faixa
        nextTrack() {
            console.log('Next track');
            if (this.tracks.length > 0) {
                this.currentTrackIndex = (this.currentTrackIndex + 1) % this.tracks.length;
                this.updateCurrentTrack();
                this.loadCurrentTrack();
            }
        },
        
        // Ir para a faixa anterior
        prevTrack() {
            console.log('Previous track');
            if (this.tracks.length > 0) {
                this.currentTrackIndex = (this.currentTrackIndex - 1 + this.tracks.length) % this.tracks.length;
                this.updateCurrentTrack();
                this.loadCurrentTrack();
            }
        },
        
        // Reiniciar a faixa atual
        restartTrack() {
            if (this.youtubePlayer) {
                this.youtubePlayer.seekTo(0, true);
                this.currentTime = 0;
                this.currentTimeFormatted = '0:00';
                const progressBar = document.querySelector('.progress-bar');
                if (progressBar) {
                    progressBar.style.width = '0%';
                }
                const currentTimeElement = document.querySelector('.current-time');
                if (currentTimeElement) {
                    currentTimeElement.textContent = '0:00';
                }
            }
        }
    }));
    
    // Componente vinylCard
    Alpine.data('vinylCard', () => ({
        playAudio(vinylId, vinylTitle, vinylArtist, vinylCover) {
            // Obter o componente vinylPlayer
            try {
                // Usar Alpine.$data para obter os dados do componente de forma segura
                const playerElement = document.getElementById('vinyl-player');
                if (playerElement && window.Alpine) {
                    // Método recomendado para acessar dados Alpine
                    const player = Alpine.$data(playerElement);
                    
                    if (player && typeof player.showPlayer === 'function') {
                        player.showPlayer(vinylId, vinylTitle, vinylArtist, vinylCover);
                    } else {
                        // Fallback - método alternativo se não conseguir acessar via Alpine.$data
                        this.showPlayerFallback(playerElement, vinylId, vinylTitle, vinylArtist, vinylCover);
                    }
                } else {
                    console.error('Player element not found or Alpine not available');
                }
            } catch (error) {
                console.error('Error accessing player component:', error);
                // Tenta um fallback como último recurso
                const playerElement = document.getElementById('vinyl-player');
                if (playerElement) {
                    this.showPlayerFallback(playerElement, vinylId, vinylTitle, vinylArtist, vinylCover);
                }
            }
        },
        
        // Método fallback para abrir o player sem Alpine
        showPlayerFallback(playerElement, vinylId, vinylTitle, vinylArtist, vinylCover) {
            console.log('Using fallback method to show player');
            // Mostrar o player via manipulação direta do DOM
            playerElement.classList.remove('translate-y-full');
            playerElement.classList.add('translate-y-0');
            
            // Atualizar informações básicas do vinil via DOM
            const titleElement = playerElement.querySelector('.vinyl-title');
            const artistElement = playerElement.querySelector('.vinyl-artist');
            const coverElement = playerElement.querySelector('.vinyl-cover');
            
            if (titleElement) titleElement.textContent = vinylTitle || 'Sem título';
            if (artistElement) artistElement.textContent = vinylArtist || 'Artista desconhecido';
            if (coverElement && vinylCover) coverElement.src = vinylCover;
            
            // Tentar acionar evento personalizado para o player buscar as faixas
            try {
                const event = new CustomEvent('vinyl:selected', { 
                    detail: { vinylId, vinylTitle, vinylArtist, vinylCover } 
                });
                playerElement.dispatchEvent(event);
            } catch (e) {
                console.error('Failed to dispatch custom event', e);
            }
        }
    }));
});
