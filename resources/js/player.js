/**
 * Vinyl Player - Versão simplificada que apenas controla a visibilidade do player
 */
document.addEventListener('DOMContentLoaded', () => {
    // Inicializa o player Alpine.js
    if (typeof Alpine !== 'undefined') {
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
                    
                    // Pausar qualquer reprodução, se necessário
                    // (isso seria implementado depois)
                }
            }
        }));
    }
    
    // Componente Alpine para os cards de vinil
    Alpine.data('vinylCard', () => ({
        vinylId: null,
        vinylTitle: null,
        vinylArtist: null,
        vinylCover: null,
        
        playAudio() {
            console.log('Botão play clicado para:', this.vinylTitle);
            
            // Busca o player Alpine.js
            const playerElements = document.querySelectorAll('[x-data="vinylPlayer"]');
            if (playerElements.length > 0) {
                // Obtém a instância Alpine.js do player
                const playerInstance = Alpine.$data(playerElements[0]);
                // Chama o método para mostrar o player
                playerInstance.showPlayer();
            } else {
                // Fallback caso não encontre o componente Alpine
                const playerElement = document.getElementById('vinyl-player');
                if (playerElement) {
                    playerElement.classList.remove('translate-y-full');
                    playerElement.classList.add('translate-y-0');
                } else {
                    console.error('Elemento do player não encontrado');
                }
            }
        }
    }));
});
