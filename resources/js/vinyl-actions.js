/**
 * Funções globais para wishlist e wantlist
 * Esta versão oferece suporte tanto para componentes Livewire quanto para elementos HTML padrão
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('vinyl-actions.js carregado');
    
    // Adicionar listener para eventos de log do Livewire
    window.addEventListener('console-log', event => {
        console.log('%c[Livewire Debug] ' + event.detail.message, 'background: #4338ca; color: white; padding: 2px 5px; border-radius: 3px;', event.detail.data);
    });
    
    // Configurar CSRF token para todas as requisições
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Verifica se o usuário está autenticado observando se existe a classe 'user-authenticated' no body
    // ou se a variável global window.isAuthenticated está definida como true
    function checkAuthentication() {
        return (window.isAuthenticated === true) || document.body.classList.contains('user-authenticated');
    }
    
    // Função para mostrar notificação toast
    function showNotification(message, type = 'info') {
        // Se houver um toast container definido
        const toast = document.getElementById('toast-container');
        if (toast) {
            // Disparar evento para mostrar toast
            const event = new CustomEvent('show-toast', {
                detail: { message, type }
            });
            document.dispatchEvent(event);
        } else {
            // Fallback para alert se não houver toast container
            alert(message);
        }
    }
    
    // Funções globais
    window.toggleWishlist = function(id, button) {
        // Verificar se o usuário está autenticado
        if (!checkAuthentication()) {
            showNotification('É necessário fazer login para adicionar itens à sua lista de desejos', 'warning');
            window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
            return;
        }
        
        // Verificar se o elemento tem um componente Livewire
        const livewireEl = button ? button.closest('[wire\\:id]') : null;
        
        if (livewireEl) {
            // Se for um componente Livewire, usar o método do Livewire
            const componentId = livewireEl.getAttribute('wire:id');
            if (componentId) {
                window.Livewire.find(componentId).call('toggle');
                return;
            }
        }
        
        console.log('Tentando adicionar à wishlist, ID:', id);
        // Se não for Livewire ou não conseguir encontrar o componente, usar fetch como fallback
        fetch('/wishlist/toggle/' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Resposta recebida:', response.status);
            return response.json().then(data => ({ status: response.status, data }))
        })
        .then(({ status, data }) => {
            console.log('Dados da resposta:', data);
            showNotification(data.message || 'Operação concluída', data.type || 'success');
            
            // Atualizar UI
            if (button) {
                const svg = button.querySelector('svg');
                if (data.added) {
                    button.classList.add('wishlist-active');
                    if (svg) {
                        svg.classList.replace('text-gray-700', 'text-red-500');
                        svg.setAttribute('fill', 'currentColor');
                    }
                } else {
                    button.classList.remove('wishlist-active');
                    if (svg) {
                        svg.classList.replace('text-red-500', 'text-gray-700');
                        svg.setAttribute('fill', 'none');
                    }
                }
            }
        })
        .catch(error => {
            showNotification('Erro ao processar solicitação', 'error');
            console.error(error);
        });
    };
    
    window.toggleWantlist = function(id, button) {
        // Verificar se o usuário está autenticado
        if (!checkAuthentication()) {
            showNotification('É necessário fazer login para adicionar itens à sua lista de interesse', 'warning');
            window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
            return;
        }
        
        // Verificar se o elemento tem um componente Livewire
        const livewireEl = button ? button.closest('[wire\\:id]') : null;
        
        if (livewireEl) {
            // Se for um componente Livewire, usar o método do Livewire
            const componentId = livewireEl.getAttribute('wire:id');
            if (componentId) {
                window.Livewire.find(componentId).call('toggle');
                return;
            }
        }
        
        console.log('Tentando adicionar à wantlist, ID:', id);
        // Se não for Livewire ou não conseguir encontrar o componente, usar fetch como fallback
        fetch('/wantlist/toggle/' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Resposta recebida (wantlist):', response.status);
            return response.json().then(data => ({ status: response.status, data }))
        })
        .then(({ status, data }) => {
            console.log('Dados da resposta (wantlist):', data);
            showNotification(data.message || 'Operação concluída', data.type || 'success');
            
            // Atualizar UI
            if (button) {
                const svg = button.querySelector('svg');
                if (data.added) {
                    button.classList.add('wantlist-active');
                    if (svg) {
                        svg.classList.replace('text-gray-700', 'text-purple-500');
                        svg.setAttribute('fill', 'currentColor');
                    }
                } else {
                    button.classList.remove('wantlist-active');
                    if (svg) {
                        svg.classList.replace('text-purple-500', 'text-gray-700');
                        svg.setAttribute('fill', 'none');
                    }
                }
            }
        })
        .catch(error => {
            showNotification('Erro ao processar solicitação', 'error');
            console.error(error);
        });
    };
    
    // Inicializa listeners para eventos relacionados a toast
    document.addEventListener('show-toast', function(e) {
        const toast = document.getElementById('toast-container');
        if (!toast) return;
        
        const { message, type } = e.detail;
        const alert = document.createElement('div');
        let icon = '';
        let bgColor = 'bg-blue-500';
        
        // Configurar aparência baseada no tipo
        if (type === 'success') {
            bgColor = 'bg-green-500';
            icon = '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
        } else if (type === 'error') {
            bgColor = 'bg-red-500';
            icon = '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
        } else if (type === 'warning') {
            bgColor = 'bg-yellow-500';
            icon = '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>';
        } else {
            bgColor = 'bg-blue-500';
            icon = '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
        }
        
        // Adicionar o alerta ao toast
        alert.className = `flex items-center p-4 mb-4 text-white rounded-lg shadow ${bgColor}`;
        alert.innerHTML = `${icon}<span>${message}</span>`;
        toast.appendChild(alert);
        
        // Remover após 3 segundos
        setTimeout(() => {
            if (toast.contains(alert)) {
                alert.remove();
            }
        }, 3000);
    });
});
