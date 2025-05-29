<h2 class="text-2xl font-bold mb-6">Opções de Frete</h2>

@if (!isset($shippingQuote) || !$shippingQuote->options)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    Não foi possível carregar as opções de frete. Por favor, volte ao carrinho e recalcule o frete.
                </p>
            </div>
        </div>
    </div>
    <a href="{{ route('site.cart.index') }}" class="inline-block px-6 py-2 bg-primary hover:bg-primary-dark text-white rounded-md">
        Voltar ao carrinho
    </a>
@else
    <div class="space-y-4">
        @php
            // Garantir que options seja um array decodificado
            $shippingOptions = is_string($shippingQuote->options) ? json_decode($shippingQuote->options, true) : $shippingQuote->options;
        @endphp
        
        @foreach($shippingOptions as $option)
            <div class="border rounded-lg p-4 hover:border-primary {{ $shippingQuote->selected_service_id == $option['id'] ? 'border-primary bg-primary/5' : 'border-gray-200' }}">
                <label class="flex items-start cursor-pointer">
                    <input type="radio" name="shipping_option" value="{{ $option['id'] }}" 
                           class="mt-1 h-4 w-4 text-primary border-gray-300 focus:ring-primary"
                           {{ $shippingQuote->selected_service_id == $option['id'] ? 'checked' : '' }}>
                    
                    <div class="ml-3 flex-1">
                        <div class="flex justify-between">
                            <span class="block text-sm font-medium text-gray-700">{{ $option['name'] }}</span>
                            <span class="block text-sm font-medium text-gray-900">R$ {{ number_format($option['price'], 2, ',', '.') }}</span>
                        </div>
                        
                        <div class="mt-1 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="ml-1 text-xs text-gray-500">
                                Entrega em {{ $option['delivery_time'] }} {{ $option['delivery_time'] == 1 ? 'dia útil' : 'dias úteis' }}
                            </span>
                        </div>
                        
                        @if(isset($option['company']))
                        <div class="mt-1 text-xs text-gray-500">
                            Transportadora: {{ $option['company']['name'] }}
                        </div>
                        @endif
                    </div>
                </label>
            </div>
        @endforeach
    </div>
    
    <div class="mt-6 text-sm text-gray-500">
        <p>O prazo de entrega começa a contar a partir da confirmação do pagamento e pode variar de acordo com a região de entrega.</p>
    </div>
    
    <!-- Botão para avançar para o próximo passo -->
    <div class="mt-6">
        <form action="{{ route('site.checkout.next-step') }}" method="POST" id="next-step-form">
            @csrf
            <input type="hidden" name="selected_shipping_id" id="selected_shipping_id" value="{{ $shippingQuote->selected_service_id }}">
            <button type="submit" id="next-step-button" class="w-full py-3 bg-primary text-white rounded-md font-medium hover:bg-primary-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed" {{ !$shippingQuote->selected_service_id ? 'disabled' : '' }}>
                Continuar para Pagamento
            </button>
        </form>
    </div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const shippingOptions = document.querySelectorAll('input[name="shipping_option"]');
        const nextStepButton = document.getElementById('next-step-button');
        const nextStepForm = document.getElementById('next-step-form');
        
        // Atualizar o serviço de envio selecionado quando o usuário selecionar uma opção
        shippingOptions.forEach(option => {
            option.addEventListener('change', function() {
                // Atualizar a UI
                document.querySelectorAll('.border.rounded-lg').forEach(div => {
                    div.classList.remove('border-primary', 'bg-primary/5');
                    div.classList.add('border-gray-200');
                });
                
                this.closest('.border.rounded-lg').classList.remove('border-gray-200');
                this.closest('.border.rounded-lg').classList.add('border-primary', 'bg-primary/5');
                
                // Atualizar o campo hidden com o ID da opção selecionada
                document.getElementById('selected_shipping_id').value = this.value;
                
                // Habilitar o botão de próximo passo
                nextStepButton.disabled = false;
                
                // Criar um indicador de carregamento
                const loadingIndicator = document.createElement('div');
                loadingIndicator.className = 'text-sm text-gray-600 mt-2';
                loadingIndicator.innerHTML = 'Atualizando frete...';
                this.closest('.border.rounded-lg').appendChild(loadingIndicator);
                
                // Enviar a seleção para o servidor usando FormData
                const formData = new FormData();
                formData.append('shipping_option', this.value);
                formData.append('is_checkout', 'true'); // Indicar que estamos no checkout
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                
                fetch('{{ route("site.cart.select-shipping") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    // Remover o indicador de carregamento
                    loadingIndicator.remove();
                    
                    try {
                        const data = JSON.parse(html);
                        if (data.success) {
                            // Atualizar o preço do frete e o total se disponível
                            if (data.shipping_price) {
                                const shippingElements = document.querySelectorAll('.checkout-shipping-price');
                                shippingElements.forEach(el => {
                                    // Primeiro salvar o valor atual para comparação
                                    const oldValue = el.textContent;
                                    // Atualizar com o novo valor
                                    const newValue = 'R$ ' + parseFloat(data.shipping_price).toFixed(2).replace('.', ',');
                                    el.textContent = newValue;
                                    
                                    // Destacar a mudança com uma animação
                                    if (oldValue !== newValue) {
                                        el.classList.add('bg-yellow-100');
                                        setTimeout(() => {
                                            el.classList.remove('bg-yellow-100');
                                        }, 1500);
                                    }
                                });
                            }
                            
                            if (data.cart_total) {
                                const totalElements = document.querySelectorAll('.checkout-total-price');
                                totalElements.forEach(el => {
                                    // Primeiro salvar o valor atual para comparação
                                    const oldValue = el.textContent;
                                    // Atualizar com o novo valor
                                    const newValue = 'R$ ' + parseFloat(data.cart_total).toFixed(2).replace('.', ',');
                                    el.textContent = newValue;
                                    
                                    // Destacar a mudança com uma animação
                                    if (oldValue !== newValue) {
                                        el.classList.add('bg-yellow-100');
                                        setTimeout(() => {
                                            el.classList.remove('bg-yellow-100');
                                        }, 1500);
                                    }
                                });
                                
                                // Adicionar uma notificação no resumo do pedido
                                const orderSummary = document.querySelector('.bg-white.rounded-lg.shadow-md.p-6.sticky');
                                if (orderSummary) {
                                    const notification = document.createElement('div');
                                    notification.className = 'text-sm text-green-600 mt-2 p-2 bg-green-50 rounded text-center';
                                    notification.innerHTML = 'Resumo do pedido atualizado!';
                                    orderSummary.appendChild(notification);
                                    
                                    // Remover a notificação após 3 segundos
                                    setTimeout(() => {
                                        notification.remove();
                                    }, 3000);
                                }
                            }
                            
                            // Exibir mensagem de sucesso
                            const successMsg = document.createElement('div');
                            successMsg.className = 'text-sm text-green-600 mt-2';
                            successMsg.innerHTML = 'Opção de frete atualizada com sucesso!';
                            this.closest('.border.rounded-lg').appendChild(successMsg);
                            
                            // Remover a mensagem após 3 segundos
                            setTimeout(() => {
                                successMsg.remove();
                            }, 3000);
                        } else {
                            alert(data.message || 'Erro ao selecionar o serviço de entrega');
                            nextStepButton.disabled = true;
                        }
                    } catch (e) {
                        // Se não conseguir parsear como JSON, pode ser que tenha sido redirecionado
                        // Recarregar a página para mostrar o novo conteúdo
                        window.location.reload();
                    }
                })
                .catch(error => {
                    // Remover o indicador de carregamento
                    loadingIndicator.remove();
                    
                    console.error('Erro:', error);
                    alert('Erro ao selecionar o serviço de entrega');
                });
            });
        });
        
        // Desabilitar o botão de próximo passo se nenhuma opção estiver selecionada
        if (shippingOptions.length > 0 && ![...shippingOptions].some(option => option.checked)) {
            nextStepButton.disabled = true;
        }
        
        // Verificar se o formulário pode ser enviado
        nextStepForm.addEventListener('submit', function(e) {
            if (shippingOptions.length > 0 && ![...shippingOptions].some(option => option.checked)) {
                e.preventDefault();
                alert('Por favor, selecione uma opção de frete');
            }
        });
    });
</script>
@endpush
