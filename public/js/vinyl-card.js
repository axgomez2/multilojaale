// Definição do componente Alpine.js para o vinyl-card
document.addEventListener('alpine:init', () => {
    Alpine.data('vinylCard', () => ({
        vinylId: null,
        vinylTitle: null,
        vinylArtist: null,
        vinylCover: null,
        
        // Método para adicionar ao carrinho
        addToCart(event) {
            // Prevenir o comportamento padrão do link
            if (event) event.preventDefault();
            
            // Exibir um pequeno feedback visual
            const toastElement = document.createElement('div');
            toastElement.className = 'fixed bottom-5 right-5 bg-gray-800 text-white px-4 py-2 rounded shadow-lg z-50';
            toastElement.textContent = 'Adicionando ao carrinho...';
            document.body.appendChild(toastElement);
            
            // Fazer a requisição AJAX para adicionar o item ao carrinho
            fetch('/carrinho/adicionar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ vinyl_master_id: this.vinylId, quantity: 1 })
            })
            .then(response => response.json())
            .then(data => {
                // Remover o toast de carregamento
                document.body.removeChild(toastElement);
                
                // Criar um novo toast com a mensagem de sucesso/erro
                const resultToast = document.createElement('div');
                resultToast.className = `fixed bottom-5 right-5 ${data.success ? 'bg-green-600' : 'bg-red-600'} text-white px-4 py-2 rounded shadow-lg z-50`;
                resultToast.textContent = data.message;
                document.body.appendChild(resultToast);
                
                // Atualizar o contador do carrinho no header, se existir
                const cartCountElement = document.querySelector('.cart-count');
                if (cartCountElement && data.cart_count) {
                    cartCountElement.textContent = data.cart_count;
                    cartCountElement.classList.remove('hidden');
                }
                
                // Remover o toast após 3 segundos
                setTimeout(() => {
                    document.body.removeChild(resultToast);
                }, 3000);
                
                // Disparar evento para o carrinho (compatibilidade com código existente)
                const event = new CustomEvent('add-to-cart', { 
                    detail: { 
                        id: this.vinylId, 
                        success: data.success 
                    } 
                });
                window.dispatchEvent(event);
            })
            .catch(error => {
                console.error('Erro ao adicionar ao carrinho:', error);
                document.body.removeChild(toastElement);
                
                // Criar um toast de erro
                const errorToast = document.createElement('div');
                errorToast.className = 'fixed bottom-5 right-5 bg-red-600 text-white px-4 py-2 rounded shadow-lg z-50';
                errorToast.textContent = 'Erro ao adicionar ao carrinho. Tente novamente.';
                document.body.appendChild(errorToast);
                
                // Remover o toast após 3 segundos
                setTimeout(() => {
                    document.body.removeChild(errorToast);
                }, 3000);
            });
        },
        
        // Método para tocar áudio
        playAudio() {
            // Verificar se o player está inicializado
            if (window.vinylPlayer) {
                // Chamar diretamente o método do player
                window.vinylPlayer.loadVinylTracks(
                    this.vinylId, 
                    this.vinylTitle, 
                    this.vinylArtist, 
                    this.vinylCover
                );
            } else {
                // Fallback para o método anterior usando eventos
                console.warn('Player não inicializado. Usando método de fallback.');
                // Disparar evento para o player de áudio (compatibilidade)
                const event = new CustomEvent('play-vinyl', { 
                    detail: { 
                        id: this.vinylId,
                        title: this.vinylTitle,
                        artist: this.vinylArtist,
                        cover: this.vinylCover
                    } 
                });
                window.dispatchEvent(event);
            }
        }
    }));
});
