<x-app-layout>
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <h1 class="text-2xl md:text-3xl font-bold mb-6 text-slate-800">Finalizar Pagamento</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Coluna do formulário de pagamento (ocupa 2/3 em desktop) -->
            <div class="lg:col-span-2">
                <!-- Resumo do pedido em card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Pedido: {{ $order->order_number }}
                    </h2>
                    
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-700">Subtotal:</span>
                            <span class="font-medium">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-700">Frete:</span>
                            <span class="font-medium">R$ {{ number_format($order->shipping, 2, ',', '.') }}</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="flex justify-between mb-2 text-green-600">
                            <span>Desconto:</span>
                            <span class="font-medium">- R$ {{ number_format($order->discount, 2, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-bold text-lg mt-2 pt-2 border-t border-gray-200">
                            <span>Total:</span>
                            <span>R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Formulário de pagamento -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Pagamento
                    </h2>
                    
                    <form id="form-checkout" action="{{ route('site.payment.process') }}" method="post">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div id="cardPaymentBrick_container"></div>
                        <div class="mt-6">
                            <button id="form-checkout__submit" type="submit" class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-opacity-50 text-lg font-medium transition-colors">
                                Finalizar Pagamento
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-500">Pagamento processado com segurança pelo Mercado Pago</p>
                        <div class="flex justify-center mt-2 space-x-2">
                            <img src="{{ asset('images/cards/visa.svg') }}" alt="Visa" class="h-8">
                            <img src="{{ asset('images/cards/mastercard.svg') }}" alt="Mastercard" class="h-8">
                            <img src="{{ asset('images/cards/elo.svg') }}" alt="Elo" class="h-8">
                            <img src="{{ asset('images/cards/hipercard.svg') }}" alt="Hipercard" class="h-8">
                            <img src="{{ asset('images/cards/pix.svg') }}" alt="PIX" class="h-8">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="lg:col-span-1">
                <!-- Endereço de entrega -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Endereço de Entrega
                    </h2>
                    
                    @if($order->address)
                        <div class="text-gray-700">
                            <p class="font-medium">{{ $order->address->recipient_name }}</p>
                            <p>{{ $order->address->street }}, {{ $order->address->number }}</p>
                            @if($order->address->complement)
                                <p>{{ $order->address->complement }}</p>
                            @endif
                            <p>{{ $order->address->neighborhood }} - {{ $order->address->city }}/{{ $order->address->state }}</p>
                            <p>CEP: {{ $order->address->zipcode }}</p>
                        </div>
                    @else
                        <p class="text-gray-500">Nenhum endereço cadastrado.</p>
                    @endif
                </div>
                
                <!-- Método de envio -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                        Método de Envio
                    </h2>
                    
                    <p class="text-gray-700">{{ $order->shipping_method ?? 'Correios' }}</p>
                    <p class="font-medium mt-1">R$ {{ number_format($order->shipping, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar o SDK do Mercado Pago
            const mp = new MercadoPago('{{ $publicKey }}', {
                locale: 'pt-BR'
            });
            
            // Configurar o Brick de cartão de crédito
            const bricksBuilder = mp.bricks();
            
            const renderCardPaymentBrick = async (bricksBuilder) => {
                const settings = {
                    initialization: {
                        amount: {{ $order->total }},
                        payer: {
                            email: '{{ $order->user->email }}',
                        },
                    },
                    customization: {
                        visual: {
                            style: {
                                theme: 'default', // | 'dark' | 'bootstrap' | 'flat'
                            },
                        },
                        paymentMethods: {
                            creditCard: 'all',
                            debitCard: 'all',
                        },
                    },
                    callbacks: {
                        onReady: () => {
                            // Callback chamado quando o Brick estiver pronto
                            console.log('Brick de pagamento pronto');
                        },
                        onSubmit: (cardFormData) => {
                            // Callback chamado quando o usuário clicar no botão de envio
                            console.log('Enviando dados de pagamento...');

                            // Capture cardFormData data and send it to backend
                            fetch('/payment/process', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    cardFormData: cardFormData,
                                    order_id: '{{ $order->id }}',
                                })
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.status === 'success') {
                                    window.location.href = result.redirect_url;
                                } else {
                                    alert('Erro ao processar pagamento: ' + result.message);
                                }
                            })
                            .catch(error => {
                                console.error('Erro:', error);
                                alert('Ocorreu um erro ao processar o pagamento. Tente novamente.');
                            });

                            return false; // Evita o envio do formulário padrão para poder processar via AJAX
                        },
                        onError: (error) => {
                            // Callback chamado para todos os casos de erro do Brick
                            console.error('Erro no Brick de pagamento:', error);
                        },
                    },
                };
                
                window.cardPaymentBrickController = await bricksBuilder.create('cardPayment', 'cardPaymentBrick_container', settings);
            };

            renderCardPaymentBrick(bricksBuilder);
        });
    </script>
</x-app-layout>
