/**
 * Integração de componentes Livewire com o player de vinil e alertas
 */

// Função global para tocar áudio
function playVinylTrack(vinylId, title, artist, coverImage) {
    console.log('playVinylTrack chamado com:', vinylId, title, artist, coverImage);
    
    if (window.vinylPlayer) {
        // Mostrar o player
        if (typeof window.vinylPlayer.showPlayer === 'function') {
            window.vinylPlayer.showPlayer();
        } else {
            // Fallback para mostrar o player
            const playerEl = document.getElementById('vinyl-player');
            if (playerEl) {
                playerEl.classList.remove('translate-y-full');
                playerEl.classList.add('translate-y-0');
            }
        }
        
        // Carregar e reproduzir as faixas
        window.vinylPlayer.loadVinylTracks(vinylId, title, artist, coverImage);
        return false; // Evitar navegação
    } else {
        console.error('vinylPlayer não está inicializado');
    }
}

// Função global para mostrar toasts
function showToast(message, type = 'success', duration = 3000) {
    // Verificar se estamos usando o sistema nativo de toast
    if (window.siteToast) {
        window.siteToast(message, type, duration);
        return;
    }
    
    // Implementação fallback usando Livewire
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 z-50 px-4 py-2 rounded-lg text-sm shadow-lg ${
        type === 'success' ? 'bg-green-600 text-white' :
        type === 'error' ? 'bg-red-600 text-white' :
        type === 'warning' ? 'bg-yellow-500 text-white' : 'bg-blue-500 text-white'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Remover o toast após o tempo especificado
    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

// Inicialização dos listeners de eventos
document.addEventListener('DOMContentLoaded', () => {
    // Escutar eventos do Livewire
    if (window.Livewire) {
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-toast', params => {
                showToast(params.message, params.type || 'success', params.duration || 3000);
            });
            
            Livewire.on('update-cart-count', params => {
                const cartCountEl = document.getElementById('cart-count');
                if (cartCountEl && params.count !== undefined) {
                    cartCountEl.textContent = params.count;
                    cartCountEl.classList.remove('hidden');
                }
            });
        });
    }
    
    // Escutar eventos diretos no documento (para compatibilidade)
    document.addEventListener('play-audio', (e) => {
        playVinylTrack(
            e.detail.vinylId,
            e.detail.title,
            e.detail.artist,
            e.detail.coverImage
        );
    });
});
