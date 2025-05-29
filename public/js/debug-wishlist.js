/**
 * Script de depuração para wishlist/wantlist
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Debug Wishlist iniciado');
    
    // Adicionar um botão de teste na página para testar a funcionalidade diretamente
    const debugContainer = document.createElement('div');
    debugContainer.style.position = 'fixed';
    debugContainer.style.bottom = '20px';
    debugContainer.style.right = '20px';
    debugContainer.style.zIndex = '9999';
    debugContainer.style.background = 'rgba(0,0,0,0.7)';
    debugContainer.style.padding = '10px';
    debugContainer.style.borderRadius = '5px';
    debugContainer.style.color = 'white';
    debugContainer.innerHTML = `
        <h3>Debug Wishlist</h3>
        <div>
            <input type="text" id="debug-vinyl-id" placeholder="ID do vinil" style="color: black; padding: 5px; margin: 5px 0; width: 100%;">
            <button id="debug-wishlist-btn" style="background: #f43f5e; color: white; border: none; padding: 5px 10px; margin: 5px 0; cursor: pointer; width: 100%;">Testar Wishlist</button>
            <button id="debug-wantlist-btn" style="background: #818cf8; color: white; border: none; padding: 5px 10px; margin: 5px 0; cursor: pointer; width: 100%;">Testar Wantlist</button>
            <div id="debug-result" style="margin-top: 10px; font-size: 12px; max-height: 200px; overflow-y: auto;"></div>
        </div>
    `;
    document.body.appendChild(debugContainer);
    
    // Função para adicionar mensagem ao resultado
    function addDebugMessage(message) {
        const resultEl = document.getElementById('debug-result');
        const messageEl = document.createElement('div');
        messageEl.style.borderBottom = '1px solid rgba(255,255,255,0.2)';
        messageEl.style.padding = '5px 0';
        messageEl.textContent = message;
        resultEl.prepend(messageEl);
    }
    
    // Função para testar wishlist
    document.getElementById('debug-wishlist-btn').addEventListener('click', function() {
        const vinylId = document.getElementById('debug-vinyl-id').value;
        if (!vinylId) {
            addDebugMessage('⚠️ Por favor, insira um ID de vinil');
            return;
        }
        
        addDebugMessage(`🔄 Tentando adicionar vinil ID ${vinylId} à wishlist...`);
        
        // Obter token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Fazer a requisição
        fetch('/wishlist/toggle/' + vinylId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            addDebugMessage(`📊 Status da resposta: ${response.status}`);
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    addDebugMessage(`⚠️ Resposta não é JSON válido: ${text.substring(0, 100)}...`);
                    throw new Error('Resposta inválida');
                }
            });
        })
        .then(data => {
            addDebugMessage(`✅ Sucesso: ${JSON.stringify(data)}`);
        })
        .catch(error => {
            addDebugMessage(`❌ Erro: ${error.message}`);
        });
    });
    
    // Função para testar wantlist
    document.getElementById('debug-wantlist-btn').addEventListener('click', function() {
        const vinylId = document.getElementById('debug-vinyl-id').value;
        if (!vinylId) {
            addDebugMessage('⚠️ Por favor, insira um ID de vinil');
            return;
        }
        
        addDebugMessage(`🔄 Tentando adicionar vinil ID ${vinylId} à wantlist...`);
        
        // Obter token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Fazer a requisição
        fetch('/wantlist/toggle/' + vinylId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            addDebugMessage(`📊 Status da resposta: ${response.status}`);
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    addDebugMessage(`⚠️ Resposta não é JSON válido: ${text.substring(0, 100)}...`);
                    throw new Error('Resposta inválida');
                }
            });
        })
        .then(data => {
            addDebugMessage(`✅ Sucesso: ${JSON.stringify(data)}`);
        })
        .catch(error => {
            addDebugMessage(`❌ Erro: ${error.message}`);
        });
    });
    
    // Capturar cliques em botões do wishlist/wantlist para debug
    document.addEventListener('click', function(e) {
        // Verificar se é um botão relacionado à wishlist
        if (e.target.closest('[wire\\:click="toggle"]')) {
            const vinylEl = e.target.closest('[wire\\:id]');
            if (vinylEl) {
                const livewireId = vinylEl.getAttribute('wire:id');
                addDebugMessage(`🔍 Clique em botão Livewire Wishlist/Wantlist (ID: ${livewireId})`);
            }
        }
    });
});
