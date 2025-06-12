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
                    <div class="flex justify-between pt-2 border-t border-gray-200 mt-2">
                        <span class="text-lg font-bold">Total:</span>
                        <span class="text-lg font-bold text-purple-700">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Container para o Mercado Pago Brick -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Escolha a forma de pagamento
                </h2>
                
                <div id="payment-alert" class="hidden mb-4"></div>
                
                <!-- Formulário de pagamento do Mercado Pago -->
                <div id="mercadopago-bricks-container"></div>
                <div id="wallet_container"></div>
            </div>
        </div>
        
        <!-- Coluna lateral (1/3 da largura em desktop) -->
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
                
                <address class="not-italic text-gray-600 mt-2">
                    <p class="font-medium">{{ $order->address->name ?? 'Nome não informado' }}</p>
                    <p>{{ $order->address->street ?? '' }}, {{ $order->address->number ?? '' }}</p>
                    @if(isset($order->address->complement) && !empty($order->address->complement))
                        <p>{{ $order->address->complement }}</p>
                    @endif
                    <p>{{ $order->address->neighborhood ?? '' }}</p>
                    <p>{{ $order->address->city ?? '' }} - {{ $order->address->state ?? '' }}</p>
                    <p>{{ $order->address->zipcode ?? '' }}</p>
                </address>
            </div>
            
            <!-- Resumo dos itens -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Itens do Pedido
                </h2>
                
                <div class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <div class="py-3">
                            <div class="flex justify-between">
                                <span class="font-medium">{{ $item->name }}</span>
                                <span>{{ $item->quantity }}x</span>
                            </div>
                            <div class="flex justify-between text-gray-600 text-sm mt-1">
                                <span>Preço unitário:</span>
                                <span>R$ {{ number_format($item->unit_price, 2, ',', '.') }}</span>
                            </div>
                            @if($item->discount > 0)
                                <div class="flex justify-between text-green-600 text-sm">
                                    <span>Desconto:</span>
                                    <span>- R$ {{ number_format($item->discount, 2, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
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
        
        // Mostrar informação de ambiente para debug
        if ('{{ $isSandbox }}' === '1' || '{{ $isSandbox }}' === 'true') {
            const alert = document.createElement('div');
            alert.className = 'bg-yellow-100 text-yellow-800 p-3 mb-4 rounded text-sm';
            alert.innerHTML = '<strong>Ambiente de testes:</strong> Os pagamentos não são reais.';
            document.getElementById('payment-alert').appendChild(alert);
            document.getElementById('payment-alert').classList.remove('hidden');
        }
        
        // Configurar os Bricks do Mercado Pago
        
        // 1. Payment Brick - Para todos os métodos de pagamento
        mp.bricks().create("payment", "mercadopago-bricks-container", {
            initialization: {
                preferenceId: '{{ $preference->id ?? "" }}',
                redirectMode: 'self'
            },
            customization: {
                visual: {
                    style: {
                        theme: 'default',
                        customVariables: {
                            baseColor: '#7e22ce', // Roxo como cor principal
                            baseColorSecondary: '#4c1d95' // Roxo escuro como secundária
                        }
                    },
                    texts: {
                        action: 'Finalizar pagamento',
                        valuePropTitle: 'Pagamento seguro via Mercado Pago',
                        valuePropSubtitle: 'Todos os dados são criptografados'
                    },
                    hideFormTitle: false,
                    hidePaymentButton: false
                },
                paymentMethods: {
                    maxInstallments: 12,
                    minInstallments: 1,
                    creditCard: 'all',
                    debitCard: 'all',
                    bankTransfer: 'all',
                    atm: 'all',
                    ticket: 'all'
                }
            },
            callbacks: {
                onReady: function() {
                    console.log('Brick de pagamento carregado com sucesso');
                    document.querySelector('.mp-loading-container')?.remove();
                },
                onSubmit: function(formData) {
                    // Mostrar indicador de carregamento
                    const loadingElement = document.createElement('div');
                    loadingElement.className = 'fixed top-0 left-0 w-full h-full flex items-center justify-center bg-black bg-opacity-50 z-50';
                    loadingElement.innerHTML = `
                        <div class="bg-white p-5 rounded-lg shadow-lg text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-700 mx-auto"></div>
                            <p class="mt-3 text-gray-700">Processando pagamento...</p>
                        </div>
                    `;
                    document.body.appendChild(loadingElement);
                    
                    // Enviar dados para o backend
                    return new Promise((resolve, reject) => {
                        fetch('/payment/process/{{ $order->id }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                payment_id: formData.payment_id,
                                payment_data: formData,
                                preference_id: '{{ $preference->id ?? "" }}'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showAlert('success', 'Pagamento processado com sucesso!');
                                setTimeout(() => {
                                    window.location.href = data.redirect_url || '/checkout/success';
                                }, 1500);
                                resolve();
                            } else {
                                showAlert('error', data.message || 'Ocorreu um erro ao processar o pagamento.');
                                document.body.removeChild(loadingElement);
                                reject();
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao processar pagamento:', error);
                            showAlert('error', 'Ocorreu um erro de comunicação com o servidor. Por favor, tente novamente.');
                            document.body.removeChild(loadingElement);
                            reject();
                        });
                    });
                },
                onError: function(error) {
                    console.error('Erro no Mercado Pago:', error);
                    showAlert('error', 'Erro ao processar pagamento: ' + (error.message || 'Tente novamente ou utilize outro método de pagamento.'));
                }
            }
        });
        
        // 2. Wallet Brick - Para pagamento com conta Mercado Pago (PIX e outros métodos)
        mp.bricks().create("wallet", "wallet_container", {
            initialization: {
                preferenceId: '{{ $preference->id ?? "" }}'
            },
            customization: {
                texts: {
                    action: 'pay',
                    valuePropMain: 'Pague mais rápido com Mercado Pago',
                },
                visual: {
                    buttonBackground: '#7e22ce',
                    borderRadius: '8px',
                    buttonHeight: '48px',
                }
            },
            callbacks: {
                onReady: () => {},
                onSubmit: () => {},
                onError: (error) => {
                    console.error('Wallet Brick error:', error);
                }
            }
        });
        
        // Função para exibir alertas
        function showAlert(type, message) {
            const alertElement = document.getElementById('payment-alert');
            alertElement.innerHTML = '';
            alertElement.classList.remove('hidden');
            
            const alertBox = document.createElement('div');
            alertBox.className = type === 'success' 
                ? 'bg-green-100 text-green-800 p-4 rounded mb-4' 
                : 'bg-red-100 text-red-800 p-4 rounded mb-4';
                
            alertBox.innerHTML = `<p class="font-medium">${message}</p>`;
            alertElement.appendChild(alertBox);
            
            // Scroll para o alerta
            alertElement.scrollIntoView({behavior: 'smooth', block: 'center'});
        }
        
        // Adicionar loading enquanto os bricks carregam
        const loadingContainer = document.createElement('div');
        loadingContainer.className = 'mp-loading-container flex flex-col items-center justify-center space-y-4 py-8';
        loadingContainer.innerHTML = `
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-700"></div>
            <p class="text-gray-600">Carregando opções de pagamento...</p>
        `;
        
        // Inserir o loading antes dos containers de pagamento
        const bricksContainer = document.getElementById('mercadopago-bricks-container');
        bricksContainer.parentNode.insertBefore(loadingContainer, bricksContainer);
    });
</script>
</x-app-layout>
