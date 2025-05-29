<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Título da página -->
            <h1 class="text-3xl font-bold mb-6">Finalizar Compra</h1>
            
            <!-- Alertas -->
            @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
            @endif
            
            <!-- Grid de conteúdo -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Coluna principal -->
                <div class="lg:col-span-2">
                    <!-- Produtos -->
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4">Produtos</h2>
                        <div class="divide-y">
                            @foreach($cart->items as $item)
                            <div class="py-4 flex items-start">
                                <!-- Imagem -->
                                <div class="w-16 h-16 flex-shrink-0">
                                    <!-- Imagem do produto -->
                                </div>
                                
                                <!-- Detalhes -->
                                <div class="ml-4 flex-grow">
                                    <h3 class="font-medium">{{ $item->vinylMaster->title }}</h3>
                                    <p class="text-sm text-gray-500">
                                        {{ $item->vinylMaster->artists->pluck('name')->join(', ') }}
                                    </p>
                                </div>
                                
                                <!-- Preço e Quantidade -->
                                <div class="text-right">
                                    <p class="font-medium">R$ {{ number_format($item->price, 2, ',', '.') }}</p>
                                    <p class="text-sm text-gray-500">Qtd: {{ $item->quantity }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Endereço de entrega -->
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4">Endereço de Entrega</h2>
                        
                        @if($addresses->count() > 0)
                            <form id="address-form">
                                <div class="space-y-3">
                                    @foreach($addresses as $address)
                                    <div class="border rounded-lg p-3 hover:bg-gray-50">
                                        <label class="flex items-start cursor-pointer">
                                            <input type="radio" name="address_id" value="{{ $address->id }}" 
                                                   class="mt-1 mr-3" 
                                                   {{ $selectedAddress && $selectedAddress->id == $address->id ? 'checked' : '' }}>
                                            <div>
                                                <p class="font-medium">{{ $address->street }}, {{ $address->number }}</p>
                                                <p class="text-sm text-gray-600">
                                                    {{ $address->neighborhood }} - {{ $address->city }}/{{ $address->state }}
                                                </p>
                                                <p class="text-sm text-gray-600">CEP: {{ $address->zipcode }}</p>
                                            </div>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </form>
                        @else
                            <p class="text-red-600">Você precisa cadastrar um endereço para continuar.</p>
                            <!-- Formulário de novo endereço -->
                        @endif
                    </div>
                    
                    <!-- Opções de pagamento -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold mb-4">Forma de Pagamento</h2>
                        
                        <form action="{{ route('site.checkout.process-payment') }}" method="POST" id="payment-form">
                            @csrf
                            <input type="hidden" name="address_id" id="selected_address_id" 
                                   value="{{ $selectedAddress ? $selectedAddress->id : '' }}">
                            
                            <div class="space-y-4">
                                <!-- Integração com Mercado Pago -->
                                <div class="border rounded-lg p-4 mb-6">
                                    <h3 class="font-medium mb-4">Opções de Pagamento</h3>
                                    
                                    <!-- Tabs para opções de pagamento -->
                                    <div class="mb-6">
                                        <div class="flex border-b">
                                            <button type="button" id="tab-credit-card" class="payment-tab active px-4 py-2 border-b-2 border-purple-600 -mb-px">
                                                Cartão de Crédito
                                            </button>
                                            <button type="button" id="tab-pix" class="payment-tab px-4 py-2">
                                                PIX
                                            </button>
                                            <button type="button" id="tab-boleto" class="payment-tab px-4 py-2">
                                                Boleto
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" name="payment_method" id="payment_method" value="credit_card">
                                    
                                    <!-- Form de Cartão de Crédito -->
                                    <div id="form-credit-card" class="payment-form">
                                        <div id="cardPaymentBrick_container"></div>
                                    </div>
                                    
                                    <!-- Form de PIX -->
                                    <div id="form-pix" class="payment-form hidden">
                                        <div id="pixPaymentBrick_container"></div>
                                        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 mt-3 rounded">
                                            <p class="text-sm"><strong>Nota:</strong> No ambiente de sandbox do Mercado Pago, o PIX pode não gerar um código válido para testes. Para testar completamente, use o cartão de crédito ou boleto.</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Form de Boleto -->
                                    <div id="form-boleto" class="payment-form hidden">
                                        <div id="boletoPaymentBrick_container"></div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg font-medium">
                                    Finalizar Compra
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Resumo lateral -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                        <h2 class="text-xl font-semibold mb-4">Resumo do Pedido</h2>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span>R$ {{ number_format($cart->subtotal, 2, ',', '.') }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <div>
                                    <span>Frete:</span>
                                    <div class="text-xs text-gray-500">{{ $selectedShipping['name'] ?? 'Frete selecionado' }}</div>
                                </div>
                                <span>R$ {{ number_format($selectedShipping['price'] ?? 0, 2, ',', '.') }}</span>
                            </div>
                            
                            @if($cart->discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Desconto:</span>
                                <span>-R$ {{ number_format($cart->discount, 2, ',', '.') }}</span>
                            </div>
                            @endif
                            
                            <div class="flex justify-between font-bold pt-3 border-t">
                                <span>Total:</span>
                                <span>R$ {{ number_format($cart->subtotal + ($selectedShipping['price'] ?? 0) - $cart->discount, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts do Mercado Pago -->
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <script>
        // Verificar se há uma chave válida, se não houver, usar uma chave de teste temporária
        let mpPublicKey = '{{ $mpPublicKey }}';
        console.log('Chave pública MP recebida do controlador:', mpPublicKey);
        
        if (!mpPublicKey || mpPublicKey.trim() === '') {
            mpPublicKey = 'TEST-743d4315-c4ce-4e92-950c-7538e83bb29c'; // Chave temporária para testes
            console.log('Usando chave de teste temporária');
        }
        
        const mp = new MercadoPago(mpPublicKey, {
            locale: 'pt-BR',
            advancedFraudPrevention: true,
            siteId: 'MLB' // Definindo explicitamente o site_id para o Brasil
        });
        
        // Variáveis globais para controlar os bricks
        let cardBrickController = null;
        let pixBrickController = null;
        let boletoBrickController = null;
        let currentTab = 'credit-card';
        
        // Função para inicializar o Brick de Cartão de Crédito
        async function initCardBrick() {
            if (cardBrickController) {
                return; // Já inicializado
            }
            try {
                cardBrickController = await mp.bricks().create('cardPayment', 'cardPaymentBrick_container', {
                    initialization: {
                        amount: {{ $cart->subtotal + ($selectedShipping['price'] ?? 0) - $cart->discount }},
                        payer: {
                            email: '{{ auth()->user()->email ?? "cliente@exemplo.com" }}'
                        }
                    },
                    customization: {
                        visual: {
                            style: {
                                theme: 'default'
                            }
                        },
                        paymentMethods: {
                            minInstallments: 1,
                            maxInstallments: 12
                        }
                    },
                    callbacks: {
                        onReady: () => {
                            console.log('Cartão de crédito inicializado');
                        },
                        onSubmit: (cardFormData) => {
                            document.getElementById('payment_method').value = 'credit_card';
                            document.getElementById('checkout-form').submit();
                        },
                        onError: (error) => {
                            console.error('Erro no cartão:', error);
                        }
                    }
                });
            } catch (error) {
                console.error('Erro ao inicializar cartão:', error);
            }
        }
        
        // Função para inicializar o Brick de PIX
        async function initPixBrick() {
            if (pixBrickController) {
                return; // Já inicializado
            }
            try {
                pixBrickController = await mp.bricks().create('pix', 'pixPaymentBrick_container', {
                    initialization: {
                        amount: {{ $cart->subtotal + ($selectedShipping['price'] ?? 0) - $cart->discount }}
                    },
                    callbacks: {
                        onReady: () => {
                            console.log('PIX inicializado');
                        },
                        onSubmit: (formData) => {
                            document.getElementById('payment_method').value = 'pix';
                            document.getElementById('checkout-form').submit();
                        },
                        onError: (error) => {
                            console.error('Erro no PIX:', error);
                        }
                    }
                });
            } catch (error) {
                console.error('Erro ao inicializar PIX:', error);
            }
        }
        
        // Função para inicializar o Brick de Boleto
        async function initBoletoBrick() {
            if (boletoBrickController) {
                return; // Já inicializado
            }
            try {
                boletoBrickController = await mp.bricks().create('payment', 'boletoPaymentBrick_container', {
                    initialization: {
                        amount: {{ $cart->subtotal + ($selectedShipping['price'] ?? 0) - $cart->discount }},
                        paymentMethods: {
                            ticket: 'all'
                        }
                    },
                    customization: {
                        paymentMethods: {
                            ticket: 'all'
                        }
                    },
                    callbacks: {
                        onReady: () => {
                            console.log('Boleto inicializado');
                        },
                        onSubmit: (formData) => {
                            document.getElementById('payment_method').value = 'boleto';
                            document.getElementById('checkout-form').submit();
                        },
                        onError: (error) => {
                            console.error('Erro no boleto:', error);
                        }
                    }
                });
            } catch (error) {
                console.error('Erro ao inicializar boleto:', error);
            }
        }
        
        // Inicialização dos Bricks do Mercado Pago
        document.addEventListener('DOMContentLoaded', async function() {
            // Inicializar o brick de cartão por padrão
            await initCardBrick();
            
            // Tabs para alternar entre os métodos de pagamento
            const tabs = document.querySelectorAll('.payment-tab');
            const forms = document.querySelectorAll('.payment-form');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', async function() {
                    const tabId = this.id.split('-')[1];
                    
                    // Não faz nada se já estiver na mesma tab
                    if (currentTab === tabId) return;
                    currentTab = tabId;
                    
                    // Remover classe active de todas as tabs
                    tabs.forEach(t => t.classList.remove('active', 'border-b-2', 'border-purple-600', '-mb-px'));
                    
                    // Adicionar classe active na tab clicada
                    this.classList.add('active', 'border-b-2', 'border-purple-600', '-mb-px');
                    
                    // Esconder todos os formulários
                    forms.forEach(form => form.classList.add('hidden'));
                    
                    // Mostrar o formulário correspondente
                    const formId = 'form-' + tabId;
                    document.getElementById(formId).classList.remove('hidden');
                    
                    // Inicializar o brick correto se ainda não estiver inicializado
                    if (tabId === 'credit-card') {
                        await initCardBrick();
                    } else if (tabId === 'pix') {
                        await initPixBrick();
                    } else if (tabId === 'boleto') {
                        await initBoletoBrick();
                    }
                    
                    // Atualizar o tipo de pagamento
                    document.getElementById('payment_method').value = tabId;
                });
            });
        });
        });
        
        // Sincronizar seleção de endereço com o formulário de pagamento
        document.querySelectorAll('input[name="address_id"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('selected_address_id').value = this.value;
            });
        });
    </script>
    
    <style>
        .payment-tab.active {
            color: #9333ea;
            font-weight: 500;
        }
    </style>
</x-app-layout>