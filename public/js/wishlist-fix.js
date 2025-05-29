/**
 * Solução direta para wishlist/wantlist
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de wishlist carregado!');
    
    // Selecionar todos os botões com a classe toggle-list-button
    var buttons = document.querySelectorAll('.toggle-list-button');
    console.log('Botões encontrados:', buttons.length);
    
    // Adicionar evento de clique para cada botão
    buttons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            console.log('Botão clicado!');
            e.preventDefault();
            e.stopPropagation();
            
            var id = this.getAttribute('data-id');
            var type = this.getAttribute('data-type');
            console.log('ID:', id, 'Tipo:', type);
            
            // Token CSRF
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Verificar autenticação
            if (!document.body.classList.contains('user-authenticated')) {
                window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
                return;
            }
            
            // URL da requisição
            var url = '/' + type + '/toggle/' + id;
            console.log('Enviando requisição para:', url);
            
            // Enviar requisição
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                console.log('Resposta:', data);
                alert(data.message);
                
                // Atualizar visual do botão
                if (type === 'wishlist') {
                    if (data.added) {
                        button.classList.add('wishlist-active');
                    } else {
                        button.classList.remove('wishlist-active');
                    }
                } else {
                    if (data.added) {
                        button.classList.add('wantlist-active');
                    } else {
                        button.classList.remove('wantlist-active');
                    }
                }
            })
            .catch(function(error) {
                console.error('Erro:', error);
                alert('Erro ao processar a solicitação');
            });
        });
    });
});
