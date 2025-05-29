<h2 class="text-2xl font-bold mb-6">Forma de Pagamento</h2>

@if(session('error'))
    <div class="bg-red-50 text-red-600 p-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

<form action="{{ route('site.checkout.next-step') }}" method="POST" class="space-y-6">
    @csrf
    <!-- Seleção de métodos de pagamento -->
    <div class="flex flex-col space-y-4">
        <!-- Cartão de Crédito (será implementado com Mercado Pago no futuro) -->
        <div class="border rounded-lg p-4 hover:border-primary @if(old('payment_method') == 'credit_card') border-primary bg-primary/5 @endif">
            <label class="flex items-start cursor-pointer">
                <input type="radio" name="payment_method" value="credit_card" class="h-4 w-4 mt-1 text-primary border-gray-300 focus:ring-primary" @if(old('payment_method') == 'credit_card') checked @endif>
                <div class="ml-3 w-full">
                    <span class="block text-sm font-medium text-gray-700">Cartão de Crédito</span>
                    <p class="text-xs text-gray-500 mt-1">Pague com seu cartão de crédito em até 3x sem juros</p>
                    
                    <div class="mt-3 text-xs text-gray-500">
                        <p>Integração com Mercado Pago em desenvolvimento.</p>
                        <p>Por enquanto, o pagamento será processado manualmente após a confirmação do pedido.</p>
                    </div>
                </div>
            </label>
        </div>
        
        <!-- PIX -->
        <div class="border rounded-lg p-4 hover:border-primary @if(old('payment_method') == 'pix' || !old('payment_method')) border-primary bg-primary/5 @endif">
            <label class="flex items-start cursor-pointer">
                <input type="radio" name="payment_method" value="pix" class="h-4 w-4 mt-1 text-primary border-gray-300 focus:ring-primary" @if(old('payment_method') == 'pix' || !old('payment_method')) checked @endif>
                <div class="ml-3 w-full">
                    <span class="block text-sm font-medium text-gray-700">PIX (5% de desconto)</span>
                    <div class="text-xs text-gray-500 mt-1">
                        <p>Pagamento instantâneo com PIX. Você receberá um QR Code para pagamento após finalizar o pedido.</p>
                        <p class="mt-2">
                            Valor com desconto: <span class="font-semibold">R$ {{ number_format(($cart->subtotal + ($shippingQuote->selected_price ?? 0)) * 0.95, 2, ',', '.') }}</span>
                        </p>
                        <p class="mt-1">
                            Economia: <span class="font-semibold text-green-600">R$ {{ number_format(($cart->subtotal + ($shippingQuote->selected_price ?? 0)) * 0.05, 2, ',', '.') }}</span>
                        </p>
                    </div>
                </div>
            </label>
        </div>
        
        <!-- Boleto -->
        <div class="border rounded-lg p-4 hover:border-primary @if(old('payment_method') == 'boleto') border-primary bg-primary/5 @endif">
            <label class="flex items-start cursor-pointer">
                <input type="radio" name="payment_method" value="boleto" class="h-4 w-4 mt-1 text-primary border-gray-300 focus:ring-primary" @if(old('payment_method') == 'boleto') checked @endif>
                <div class="ml-3 w-full">
                    <span class="block text-sm font-medium text-gray-700">Boleto Bancário</span>
                    <div class="text-xs text-gray-500 mt-1">
                        <p>Você receberá um boleto para pagamento após finalizar o pedido.</p>
                        <p class="mt-2">
                            Valor: <span class="font-semibold">R$ {{ number_format($cart->subtotal + ($shippingQuote->selected_price ?? 0), 2, ',', '.') }}</span>
                        </p>
                        <p class="mt-1"><i class="fas fa-info-circle"></i> O prazo de entrega começa a contar após a confirmação do pagamento.</p>
                    </div>
                </div>
            </label>
        </div>
    </div>

    <!-- Botão para avançar -->
    <div class="mt-6">
        <button type="submit" class="w-full py-3 bg-primary text-white rounded-md font-medium hover:bg-primary-dark transition-colors">
            Continuar para Confirmação
        </button>
    </div>
</form>

@push('scripts')
<!-- Scripts para futura integração com Mercado Pago serão adicionados aqui -->
@endpush        const paymentHeaders = document.querySelectorAll('.payment-method-header');
        const nextStepButton = document.getElementById('next-step-button');
        const nextStepForm = document.getElementById('next-step-form');
        
        // Inicializar Mercado Pago SDK (modo sandbox para testes)
        const mp = new MercadoPago('{{ config("services.mercadopago.public_key") }}', {
            locale: 'pt-BR'
        });
        
        // Aplicar máscaras nos campos do cartão
        if (document.getElementById('card_number')) {
            IMask(document.getElementById('card_number'), {
                mask: '0000 0000 0000 0000'
            });
        }
        
        if (document.getElementById('card_expiration')) {
            IMask(document.getElementById('card_expiration'), {
                mask: '00/00'
            });
        }
        
        if (document.getElementById('card_cvv')) {
            IMask(document.getElementById('card_cvv'), {
                mask: '000'
            });
        }
        
        // Função para abrir/fechar formulários de pagamento
        function togglePaymentForm(method) {
            // Fechar todos os formulários
            document.querySelectorAll('.payment-method-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remover destaque de todos os headers
            document.querySelectorAll('.payment-method-header').forEach(header => {
                header.closest('.border').classList.remove('border-primary', 'bg-primary/5');
            });
            
            // Abrir o formulário selecionado
            if (method) {
                const form = document.getElementById(`${method}_form`);
                if (form) {
                    form.classList.remove('hidden');
                }
                
                // Destacar o header selecionado
                const header = document.querySelector(`.payment-method-header[data-method="${method}"]`);
                if (header) {
                    header.closest('.border').classList.add('border-primary', 'bg-primary/5');
                }
            }
        }
        
        // Event listener para os radio buttons
        paymentMethods.forEach(method => {
            method.addEventListener('change', function() {
                togglePaymentForm(this.value);
                
                // Atualizar o campo hidden com o método de pagamento selecionado
                document.getElementById('selected_payment_method').value = this.value;
                
                // Habilitar o botão de próximo passo
                nextStepButton.disabled = false;
            });
        });
        
        // Event listener para os headers (para melhor UX)
        paymentHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const method = this.dataset.method;
                const radio = document.querySelector(`input[name="payment_method"][value="${method}"]`);
                radio.checked = true;
                
                // Disparar o evento change para atualizar a UI
                radio.dispatchEvent(new Event('change'));
            });
        });
        
        // Verificar se já há um método selecionado
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        if (selectedMethod) {
            togglePaymentForm(selectedMethod.value);
        } else {
            // Desabilitar o botão de próximo passo se nenhum método estiver selecionado
            nextStepButton.disabled = true;
        }
        
        // Verificar se o formulário pode ser enviado
        nextStepForm.addEventListener('submit', function(e) {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
            const paymentErrors = document.getElementById('payment-errors');
            
            if (!selectedMethod) {
                e.preventDefault();
                paymentErrors.textContent = 'Por favor, selecione uma forma de pagamento.';
                paymentErrors.classList.remove('hidden');
                return;
            }
            
            // Atualizar o campo hidden com o método selecionado
            document.getElementById('selected_payment_method').value = selectedMethod.value;
            
            // Depuração para verificar se o valor está sendo definido
            console.log('Método de pagamento selecionado:', selectedMethod.value);
            console.log('Valor do campo hidden:', document.getElementById('selected_payment_method').value);
            
            // Se for cartão de crédito, processar com Mercado Pago
            if (selectedMethod.value === 'credit_card') {
                e.preventDefault();
                
                // Desabilitar o botão para evitar cliques múltiplos
                nextStepButton.disabled = true;
                nextStepButton.textContent = 'Processando pagamento...';
                
                // Obter dados do cartão
                const cardNumber = document.getElementById('card_number').value.replace(/\D/g, '');
                const cardExpirationRaw = document.getElementById('card_expiration').value;
                const [expirationMonth, expirationYear] = cardExpirationRaw.split('/').map(part => part.trim());
                const cardholderName = document.getElementById('card_holder_name').value;
                const securityCode = document.getElementById('card_cvv').value;
                const installments = document.getElementById('installments').value;
                
                // Criar o token do cartão
                const cardData = {
                    cardNumber,
                    cardholderName,
                    cardExpirationMonth: expirationMonth,
                    cardExpirationYear: '20' + expirationYear,
                    securityCode
                };
                
                mp.createCardToken(cardData)
                    .then(function(token) {
                        // Preencher campos ocultos com os dados do token
                        document.getElementById('payment_token').value = token.id;
                        document.getElementById('selected_installments').value = installments;
                        
                        // Enviar o formulário
                        nextStepForm.submit();
                    })
                    .catch(function(error) {
                        // Exibir erro
                        paymentErrors.textContent = 'Erro ao processar o cartão: ' + (error.message || 'Verifique os dados e tente novamente');
                        paymentErrors.classList.remove('hidden');
                        
                        // Reativar o botão
                        nextStepButton.disabled = false;
                        nextStepButton.textContent = 'Finalizar Pedido';
                    });
            }

            
            // Validar campos do cartão de crédito
            if (selectedMethod.value === 'credit_card') {
                const cardNumber = document.getElementById('card_number').value;
                const cardExpiration = document.getElementById('card_expiration').value;
                const cardCvv = document.getElementById('card_cvv').value;
                const cardHolderName = document.getElementById('card_holder_name').value;
                
                if (!cardNumber || !cardExpiration || !cardCvv || !cardHolderName) {
                    e.preventDefault();
                    alert('Por favor, preencha todos os campos do cartão de crédito');
                    return;
                }
                
                // Validar formato do cartão
                if (cardNumber.replace(/\s/g, '').length !== 16) {
                    e.preventDefault();
                    alert('Número do cartão inválido');
                    return;
                }
                
                // Validar formato da data
                const [month, year] = cardExpiration.split('/');
                if (!month || !year || month < 1 || month > 12) {
                    e.preventDefault();
                    alert('Data de validade inválida');
                    return;
                }
                
                // Validar CVV
                if (cardCvv.length !== 3) {
                    e.preventDefault();
                    alert('Código de segurança inválido');
                    return;
                }
            }
            
            // Adicionar campos ao formulário antes de enviar
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = 'payment_method';
            methodInput.value = selectedMethod.value;
            this.appendChild(methodInput);
            
            if (selectedMethod.value === 'credit_card') {
                const cardFields = ['card_number', 'card_expiration', 'card_cvv', 'card_holder_name', 'installments'];
                cardFields.forEach(field => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = field;
                    input.value = document.getElementById(field).value;
                    this.appendChild(input);
                });
            }
        });
    });
</script>
@endpush
