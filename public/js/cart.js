// Função para formatar CEP no padrão 00000-000
function maskCEP(value) {
    value = value.replace(/\D/g, ""); // Remove caracteres não numéricos
    value = value.replace(/(\d{5})(\d)/, "$1-$2"); // Insere hífen depois de 5 dígitos
    return value.substring(0, 9); // Limita a 9 caracteres (00000-000)
}

// Função para usar endereço do usuário para calcular frete
function useAddress(zipcode) {
    // Remove formatação do CEP
    zipcode = zipcode.replace(/\D/g, "");
    
    // Preenche o campo de CEP
    const zipInput = document.getElementById('zip_code');
    if (zipInput) {
        zipInput.value = maskCEP(zipcode);
        
        // Submete o formulário de cálculo automaticamente
        zipInput.form.submit();
    }
}

// Função para incrementar quantidade
function incrementQuantity(itemId) {
    const input = document.getElementById('quantity-' + itemId);
    const maxValue = parseInt(input.getAttribute('max') || 10);
    const currentValue = parseInt(input.value);
    
    if (currentValue < maxValue) {
        input.value = currentValue + 1;
        document.getElementById('update-form-' + itemId).submit();
    }
}

// Função para decrementar quantidade
function decrementQuantity(itemId) {
    const input = document.getElementById('quantity-' + itemId);
    const currentValue = parseInt(input.value);
    
    if (currentValue > 1) {
        input.value = currentValue - 1;
        document.getElementById('update-form-' + itemId).submit();
    }
}

// Função para adicionar/remover da wishlist
function toggleWishlist(vinylId, element) {
    fetch(`/site/wishlist/toggle/${vinylId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const icon = element.querySelector('.wishlist-icon');
            
            if (data.action === 'added') {
                icon.classList.add('text-red-600');
                icon.setAttribute('fill', 'currentColor');
                element.textContent = '';
                element.appendChild(icon);
                element.insertAdjacentText('beforeend', ' Remover dos favoritos');
            } else {
                icon.classList.remove('text-red-600');
                icon.setAttribute('fill', 'none');
                element.textContent = '';
                element.appendChild(icon);
                element.insertAdjacentText('beforeend', ' Adicionar aos favoritos');
            }
        }
    })
    .catch(error => console.error('Erro:', error));
}

// Função para selecionar opção de frete
function selectShippingOption(optionId) {
    // Seleciona o radio button correto
    const radioInput = document.getElementById('shipping_' + optionId);
    if (radioInput) {
        radioInput.checked = true;
        
        // Destaca a opção selecionada
        document.querySelectorAll('.shipping-option-container').forEach(container => {
            container.classList.remove('bg-green-50', 'border', 'border-green-200');
        });
        
        radioInput.closest('.shipping-option-container').classList.add('bg-green-50', 'border', 'border-green-200');
        
        // Habilita o botão de submissão
        const submitButton = document.getElementById('select-shipping-btn');
        if (submitButton) {
            submitButton.disabled = false;
        }
    }
}

// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar máscaras de CEP
    const zipInput = document.getElementById('zip_code');
    if (zipInput) {
        zipInput.addEventListener('input', function() {
            this.value = maskCEP(this.value);
        });
    }
    
    // Verificar se há opções de frete na página
    const shippingForm = document.getElementById('shipping-options-form');
    if (shippingForm) {
        // Verifica se alguma opção já está pré-selecionada
        const selectedOption = shippingForm.querySelector('input[name="shipping_option"]:checked');
        if (selectedOption) {
            selectShippingOption(selectedOption.value);
        }
        
        // Adiciona validação ao formulário
        shippingForm.addEventListener('submit', function(e) {
            const hasSelection = shippingForm.querySelector('input[name="shipping_option"]:checked');
            if (!hasSelection) {
                e.preventDefault();
                alert('Por favor, selecione uma opção de frete antes de continuar.');
            }
        });
    }
});
