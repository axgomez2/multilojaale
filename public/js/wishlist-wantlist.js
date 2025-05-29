/**
 * Script para gerenciar wishlist e wantlist
 */
document.addEventListener('DOMContentLoaded', function() {
    // Função para marcar e desmarcar wishlist
    window.toggleWishlist = function(id, button) {
        // Verificar autenticação
        if (!document.body.classList.contains('user-authenticated')) {
            window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
            return;
        }
        
        // Token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // URL da requisição
        const url = '/wishlist/toggle/' + id;
        
        // Enviar requisição
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Mostrar notificação usando a função global
            if (window.showToast) {
                showToast(data.message, data.success ? 'success' : 'error');
            }
            
            // Atualizar visual do botão
            const svg = button.querySelector('svg');
            
            if (data.added) {
                svg.setAttribute('fill', 'currentColor');
                svg.classList.remove('text-gray-700');
                svg.classList.add('text-red-600');
            } else {
                svg.setAttribute('fill', 'none');
                svg.classList.remove('text-red-600');
                svg.classList.add('text-gray-700');
            }
            
            // Disparar evento para atualizar outros componentes
            window.dispatchEvent(new CustomEvent('wishlist-updated'));
        })
        .catch(error => {
            console.error('Erro:', error);
            if (window.showToast) {
                showToast('Erro ao processar a solicitação', 'error');
            }
        });
    };
    
    // Função para marcar e desmarcar wantlist
    window.toggleWantlist = function(id, button) {
        // Verificar autenticação
        if (!document.body.classList.contains('user-authenticated')) {
            window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
            return;
        }
        
        // Token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // URL da requisição
        const url = '/wantlist/toggle/' + id;
        
        // Enviar requisição
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Mostrar notificação usando a função global
            if (window.showToast) {
                showToast(data.message, data.success ? 'success' : 'error');
            }
            
            // Atualizar visual do botão
            const svg = button.querySelector('svg');
            
            if (data.added) {
                svg.setAttribute('fill', 'currentColor');
                svg.classList.remove('text-gray-700');
                svg.classList.add('text-purple-600');
            } else {
                svg.setAttribute('fill', 'none');
                svg.classList.remove('text-purple-600');
                svg.classList.add('text-gray-700');
            }
            
            // Disparar evento para atualizar outros componentes
            window.dispatchEvent(new CustomEvent('wantlist-updated'));
        })
        .catch(error => {
            console.error('Erro:', error);
            if (window.showToast) {
                showToast('Erro ao processar a solicitação', 'error');
            }
        });
    };
    
    // Verificar e marcar itens que já estão na wishlist/wantlist quando a página carrega
    function checkSavedItems() {
        console.log('Verificando itens salvos...');
        
        if (!document.body.classList.contains('user-authenticated')) {
            console.log('Usuário não autenticado, abortando verificação');
            return; // Se não estiver autenticado, não fazer nada
        }
        
        // Obter todos os botões de wishlist
        const wishlistButtons = document.querySelectorAll('[data-wishlist-id]');
        const wantlistButtons = document.querySelectorAll('[data-wantlist-id]');
        
        console.log('Botões encontrados:', {
            wishlist: wishlistButtons.length,
            wantlist: wantlistButtons.length
        });
        
        if (wishlistButtons.length > 0) {
            const wishlistIds = Array.from(wishlistButtons).map(btn => btn.getAttribute('data-wishlist-id'));
            
            // Verificar quais IDs estão na wishlist
            fetch('/wishlist/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: wishlistIds })
            })
            .then(response => response.json())
            .then(data => {
                // Marcar os botões correspondentes
                console.log('Wishlist check response:', data);
                if (data.items && data.items.length > 0) {
                    data.items.forEach(id => {
                        console.log('Marcando wishlist item:', id);
                        const button = document.querySelector(`[data-wishlist-id="${id}"]`);
                        if (button) {
                            const svg = button.querySelector('.wishlist-icon');
                            if (svg) {
                                svg.setAttribute('fill', 'currentColor');
                                svg.classList.remove('text-gray-700');
                                svg.classList.add('text-red-600');
                                console.log('Wishlist item marcado com sucesso');
                            } else {
                                console.log('SVG wishlist não encontrado');
                            }
                        } else {
                            console.log('Botão wishlist não encontrado para ID:', id);
                        }
                    });
                }
            })
            .catch(error => console.error('Erro ao verificar wishlist:', error));
        }
        
        if (wantlistButtons.length > 0) {
            const wantlistIds = Array.from(wantlistButtons).map(btn => btn.getAttribute('data-wantlist-id'));
            
            // Verificar quais IDs estão na wantlist
            fetch('/wantlist/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: wantlistIds })
            })
            .then(response => response.json())
            .then(data => {
                // Marcar os botões correspondentes
                console.log('Wantlist check response:', data);
                if (data.items && data.items.length > 0) {
                    data.items.forEach(id => {
                        console.log('Marcando wantlist item:', id);
                        const button = document.querySelector(`[data-wantlist-id="${id}"]`);
                        if (button) {
                            const svg = button.querySelector('.wantlist-icon');
                            if (svg) {
                                svg.setAttribute('fill', 'currentColor');
                                svg.classList.remove('text-gray-700');
                                svg.classList.add('text-purple-600');
                                console.log('Wantlist item marcado com sucesso');
                            } else {
                                console.log('SVG wantlist não encontrado');
                            }
                        } else {
                            console.log('Botão wantlist não encontrado para ID:', id);
                        }
                    });
                }
            })
            .catch(error => console.error('Erro ao verificar wantlist:', error));
        }
    }
    
    // Executar a verificação quando a página carregar
    // Aguardar um breve momento para garantir que todos os elementos estão carregados
    setTimeout(checkSavedItems, 1000);
    
    // Adicionar outro listener para executar quando o DOM estiver totalmente carregado
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM carregado, verificando itens salvos novamente...');
        setTimeout(checkSavedItems, 1500);
    });
    
    // Adicionar um listener para o evento turbolinks:load (caso esteja usando Turbolinks)
    document.addEventListener('turbolinks:load', function() {
        console.log('Turbolinks carregado, verificando itens salvos...');
        setTimeout(checkSavedItems, 1000);
    });
    
    // Verificar novamente após 3 segundos (backup final)
    setTimeout(function() {
        console.log('Verificação final...');
        checkSavedItems();
    }, 3000);
});
