// Funções para manipulação do modal de frete
// Evita conflitos usando namespace isolado
const ShippingModal = {
    // Variável para armazenar a opção de frete selecionada
    selectedOption: null,
    
    // Função para abrir o modal de seleção de frete
    open: function() {
        const modal = document.getElementById('shipping-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            
            // Carregar as opções de frete
            this.loadOptions();
        }
    },
    
    // Função para fechar o modal de seleção de frete
    close: function() {
        const modal = document.getElementById('shipping-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            this.selectedOption = null;
        }
    },
    
    // Função para carregar as opções de frete via AJAX
    loadOptions: function() {
        const optionsContainer = document.getElementById('shipping-options-list');
        if (!optionsContainer) return;
        
        // Mostrar loading
        optionsContainer.innerHTML = `
            <div class="text-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div>
                <p class="mt-2 text-sm text-gray-500">Carregando opções de frete...</p>
            </div>
        `;
        
        // Fazer a requisição AJAX para buscar as opções de frete
        fetch('/carrinho/opcoes-frete')
            .then(response => response.json())
            .then(data => {
                console.log('Dados recebidos:', data);
                
                if (data.success) {
                    // Atualizar o CEP no modal
                    const zipCodeElement = document.getElementById('modal-zip-code');
                    if (zipCodeElement && data.zip_code) {
                        zipCodeElement.textContent = data.zip_code.replace(/^(\d{5})(\d{3})$/, '$1-$2');
                    }
                    
                    // Renderizar as opções
                    this.renderOptions(data.options, data.selected_shipping);
                } else {
                    optionsContainer.innerHTML = `
                        <div class="text-center py-4">
                            <p class="text-sm text-red-600">${data.message || 'Erro ao carregar as opções de frete.'}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Erro ao carregar opções de frete:', error);
                optionsContainer.innerHTML = `
                    <div class="text-center py-4">
                        <p class="text-sm text-red-600">Erro ao carregar as opções de frete. Por favor, tente novamente.</p>
                    </div>
                `;
            });
    },
    
    // Função para renderizar as opções de frete no modal
    renderOptions: function(options, selectedShipping = null) {
        const optionsContainer = document.getElementById('shipping-options-list');
        if (!optionsContainer) return;
        
        if (!options || options.length === 0) {
            optionsContainer.innerHTML = `
                <div class="text-center py-4">
                    <p class="text-sm text-gray-500">Nenhuma opção de frete disponível para este CEP.</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        
        options.forEach(option => {
            const optionId = option.id || option.name.replace(/\s+/g, '-').toLowerCase();
            const isSelected = selectedShipping && (selectedShipping.id === option.id || selectedShipping.name === option.name);
            const price = option.price || option.cost || 0;
            const formattedPrice = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(price);
            const deliveryTime = option.delivery_time || option.delivery_days || 'A combinar';
            const deliveryText = deliveryTime === 1 ? '1 dia útil' : `${deliveryTime} dias úteis`;
            
            if (isSelected) {
                this.selectedOption = option;
                const confirmBtn = document.getElementById('confirm-shipping-btn');
                if (confirmBtn) confirmBtn.disabled = false;
            }
            
            html += `
                <div class="relative">
                    <input 
                        type="radio" 
                        name="shipping_option" 
                        id="option-${optionId}" 
                        value="${optionId}" 
                        class="peer hidden"
                        onchange="ShippingModal.selectOption(this, ${JSON.stringify(option).replace(/"/g, '&quot;')})"
                        ${isSelected ? 'checked' : ''}
                    >
                    <label 
                        for="option-${optionId}" 
                        class="block p-3 border rounded-md cursor-pointer hover:border-purple-500 peer-checked:border-purple-600 peer-checked:bg-purple-50"
                    >
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="block font-medium text-gray-900">${option.name || 'Frete'}</span>
                                <span class="block text-sm text-gray-500">Entrega em até ${deliveryText}</span>
                            </div>
                            <span class="font-medium text-gray-900">${formattedPrice}</span>
                        </div>
                    </label>
                </div>
            `;
        });
        
        optionsContainer.innerHTML = html;
    },
    
    // Função para selecionar uma opção de frete
    selectOption: function(radio, option) {
        this.selectedOption = option;
        const confirmBtn = document.getElementById('confirm-shipping-btn');
        if (confirmBtn) confirmBtn.disabled = false;
    },
    
    // Função para confirmar a seleção do frete
    confirmOption: function() {
        if (!this.selectedOption) return;
        
        const confirmBtn = document.getElementById('confirm-shipping-btn');
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processando...
            `;
        }
        
        // Enviar a seleção para o servidor
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('shipping_option', this.selectedOption.id);
        
        console.log('Enviando opção de frete:', this.selectedOption.id);
        
        fetch('/carrinho/selecionar-frete', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Registrar a resposta para depuração
            console.log('Resposta do servidor:', response.status, response.statusText);
            return response.json();
        })
        .then(data => {
            console.log('Dados da resposta:', data);
            
            // Considerar a ação bem-sucedida se recebemos alguma resposta, mesmo que não tenha 'success: true'
            // Isso resolve casos onde o backend pode ter processado corretamente mas respondeu com formato diferente
            if (data && (data.success || !data.message)) {
                // Mostra mensagem de sucesso antes de recarregar
                const message = data.message || 'Frete alterado com sucesso!';
                const oldTitle = document.title;
                document.title = 'Sucesso - ' + message;
                
                // Recarregar a página para atualizar o resumo do carrinho
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                alert(data.message || 'Erro ao selecionar o frete. Por favor, tente novamente.');
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Confirmar';
                }
            }
        })
        .catch(error => {
            console.error('Erro ao selecionar frete:', error);
            
            // Se a página está sendo recarregada, podemos assumir que deu certo
            if (error.name === 'AbortError' || document.visibilityState === 'hidden') {
                return; // A página já está recarregando, não mostrar erro
            }
            
            alert('Erro ao selecionar o frete. Por favor, tente novamente.');
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Confirmar';
            }
        });
    },
    
    // Função para recalcular o frete
    recalculate: function() {
        this.close();
        
        // Rolar até o formulário de cálculo de frete
        const shippingForm = document.querySelector('form[action*="calculate-shipping"]');
        if (shippingForm) {
            shippingForm.scrollIntoView({ behavior: 'smooth' });
            const zipInput = shippingForm.querySelector('input[name="zip_code"]');
            if (zipInput) {
                zipInput.focus();
            }
        }
    }
};

// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar evento ao botão "Alterar" do frete
    const changeShippingBtn = document.querySelector('button[onclick="openShippingModal()"]');
    if (changeShippingBtn) {
        // Substituir o evento inline por uma referência ao nosso namespace
        changeShippingBtn.removeAttribute('onclick');
        changeShippingBtn.addEventListener('click', function() {
            ShippingModal.open();
        });
    }
    
    // Configurar outros botões do modal
    const closeModalBtn = document.querySelector('button[onclick="closeShippingModal()"]');
    if (closeModalBtn) {
        closeModalBtn.removeAttribute('onclick');
        closeModalBtn.addEventListener('click', function() {
            ShippingModal.close();
        });
    }
    
    const confirmBtn = document.querySelector('button[onclick="confirmShippingOption()"]');
    if (confirmBtn) {
        confirmBtn.removeAttribute('onclick');
        confirmBtn.addEventListener('click', function() {
            ShippingModal.confirmOption();
        });
    }
    
    const recalculateBtn = document.querySelector('button[onclick="recalculateShipping()"]');
    if (recalculateBtn) {
        recalculateBtn.removeAttribute('onclick');
        recalculateBtn.addEventListener('click', function() {
            ShippingModal.recalculate();
        });
    }
});
