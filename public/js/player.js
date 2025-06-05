/**
 * Vinyl Player - Versão simplificada que apenas controla a visibilidade do player
 */
document.addEventListener('alpine:init', () => {
    // Definição do componente do player
    Alpine.data('vinylPlayer', () => ({
        isOpen: false,
        
        init() {
            // Log para debug
            console.log('Player Alpine.js inicializado');
        },
        
        showPlayer() {
            console.log('Abrindo player');
            const playerElement = document.getElementById('vinyl-player');
            if (playerElement) {
                playerElement.classList.remove('translate-y-full');
                playerElement.classList.add('translate-y-0');
                this.isOpen = true;
            }
        },
        
        hidePlayer() {
            console.log('Fechando player');
            const playerElement = document.getElementById('vinyl-player');
            if (playerElement) {
                playerElement.classList.remove('translate-y-0');
                playerElement.classList.add('translate-y-full');
                this.isOpen = false;
            }
        }
    }));
    
    // Componente Alpine para os cards de vinil
    Alpine.data('vinylCard', () => ({
        vinylId: null,
        vinylTitle: null,
        vinylArtist: null,
        vinylCover: null,
        
        playAudio() {
            console.log('Botão play clicado para:', this.vinylTitle);
            
            // Obter o elemento player diretamente
            const playerElement = document.getElementById('vinyl-player');
            if (playerElement && window.Alpine) {
                // Tentar obter o componente Alpine
                const playerData = window.Alpine.$data(playerElement);
                if (playerData && typeof playerData.showPlayer === 'function') {
                    playerData.showPlayer();
                } else {
                    // Fallback direto com classList
                    playerElement.classList.remove('translate-y-full');
                    playerElement.classList.add('translate-y-0');
                }
            } else {
                console.error('Elemento player não encontrado ou Alpine não disponível');
            }
        }
    }));
});
