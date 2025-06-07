/**
 * Funções auxiliares para manipulação de áudio dos vinis
 */

// Função global para iniciar a reprodução de áudio
// Esta função serve como ponte para o sistema de player Alpine.js
window.playAudio = function(vinylId, vinylTitle, vinylArtist, vinylCover) {
    console.log('playAudio chamado para:', vinylId, vinylTitle, vinylArtist);
    
    // Disparar um evento personalizado para o Alpine.js
    // O componente vinylPlayer (em vinyl-player.js) está ouvindo este evento
    const playerComponent = document.querySelector('[x-data="vinylPlayer"]');
    if (playerComponent) {
        // Usar o método Alpine para acessar o componente
        const vinylPlayer = Alpine.$data(playerComponent);
        if (vinylPlayer && vinylPlayer.showPlayer) {
            vinylPlayer.showPlayer(vinylId, vinylTitle, vinylArtist, vinylCover);
        } else {
            console.error('Método showPlayer não encontrado no componente vinylPlayer');
        }
    } else {
        console.error('Componente vinylPlayer não encontrado no DOM');
    }
};
