<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Título da página -->
            <h1 class="text-3xl font-bold mb-6">Finalizar Compra</h1>
            
           
          
            
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
                                <!-- Imagem do disco -->
                                <div class="w-16 h-16 flex-shrink-0 overflow-hidden rounded-md bg-gray-100">
                                    @if($item->vinylMaster->cover_image)
                                        <img src="{{ asset('storage/' . $item->vinylMaster->cover_image) }}" 
                                             alt="{{ $item->vinylMaster->title }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Detalhes -->
                                <div class="ml-4 flex-grow">
                                    <h3 class="font-medium">{{ $item->vinylMaster->title }}</h3>
                                    <p class="text-sm text-gray-500">
                                        {{ $item->vinylMaster->artists->pluck('name')->join(', ') }}
                                    </p>
                                    <p class="text-sm text-gray-700 mt-1">
                                        Valor unitário: <span class="font-medium">R$ {{ number_format($item->vinylMaster->vinylSec->price ?? $item->price, 2, ',', '.') }}</span>
                                    </p>
                                </div>
                                
                                <!-- Preço e Quantidade -->
                                <div class="text-right">
                                    <p class="font-medium text-gray-900">R$ {{ number_format(($item->vinylMaster->vinylSec->price ?? $item->price) * $item->quantity, 2, ',', '.') }}</p>
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
                    
                    <!-- Verificação de CPF -->
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4">Documentos Fiscais</h2>
                        
                        @if(auth()->check() && auth()->user()->cpf)
                            <div class="border rounded-lg p-4 bg-green-50 border-green-200">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="font-medium text-green-700">CPF verificado</span>
                                </div>
                                <p class="mt-2 text-sm text-gray-600">CPF: {{ preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', auth()->user()->cpf) }}</p>
                                <div class="mt-3">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" id="cpf_confirmation" name="cpf_confirmation" class="form-checkbox h-5 w-5 text-blue-600" required>
                                        <span class="ml-2 text-sm text-gray-700">Confirmo que o CPF acima é correto e será utilizado para emissão da nota fiscal</span>
                                    </label>
                                </div>
                            </div>
                        @else
                            <div class="border rounded-lg p-4 bg-red-50 border-red-200">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span class="font-medium text-red-700">CPF não cadastrado</span>
                                </div>
                                <p class="mt-2 text-sm text-gray-700">Você precisa cadastrar seu CPF para finalizar a compra. O CPF é necessário para emissão da nota fiscal.</p>
                                <button type="button" id="open-cpf-modal" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white font-medium text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    Cadastrar CPF Agora
                                </button>
                                <a href="{{ route('site.profile.personal-info.edit') }}" class="inline-block mt-3 ml-2 px-4 py-2 bg-gray-200 text-gray-700 font-medium text-sm rounded-lg hover:bg-gray-300 transition-colors">
                                    Ir para Perfil Completo
                                </a>
                            </div>
                            <script>
                                // Desabilitar o botão de finalizar compra se não tiver CPF
                                document.addEventListener('DOMContentLoaded', function() {
                                    const submitButtons = document.querySelectorAll('button[type="submit"]');
                                    submitButtons.forEach(button => {
                                        if (button.innerText.includes('Finalizar')) {
                                            button.disabled = true;
                                            button.classList.add('opacity-50', 'cursor-not-allowed');
                                            button.title = 'Cadastre seu CPF para continuar';
                                        }
                                    });
                                });
                            </script>
                        @endif
                    </div>
                    
                    <!-- Opções de Frete -->
                    @if(auth()->check() && auth()->user()->cpf && $addresses->count() > 0)
                        <div class="bg-white rounded-lg shadow p-6 mb-6">
                            <h2 class="text-xl font-semibold mb-4">Opções de Frete</h2>
                            
                            <!-- Formulário para trocar a opção de frete -->
                            <form action="{{ route('site.checkout.update-shipping') }}" method="POST" id="update-shipping-form">
                                @csrf
                                <input type="hidden" name="shipping_option" id="selected_shipping_option">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                                    @php
                                        $hasOptions = isset($selectedShipping) && is_array($selectedShipping) && 
                                                     isset($selectedShipping['options']) && count($selectedShipping['options']) > 0;
                                    @endphp
                                    
                                    @if($hasOptions)
                                        @php
                                            $selectedId = isset($selectedShipping['selected']) && isset($selectedShipping['selected']['id']) 
                                                ? $selectedShipping['selected']['id'] : null;
                                        @endphp
                                        @foreach($selectedShipping['options'] as $option)
                                            <div class="border rounded-lg p-4 cursor-pointer shipping-option {{ isset($option['id']) && $option['id'] == $selectedId ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-400' }}" 
                                                data-shipping-id="{{ $option['id'] ?? '' }}">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <div class="flex items-center">
                                                            <div class="w-6 h-6 mr-2">
                                                                @if($option['id'] == $selectedShipping['selected']['id'])
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                                                    </svg>
                                                                @else
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
                                                                    </svg>
                                                                @endif
                                                            </div>
                                                            <span class="font-medium">{{ $option['name'] }}</span>
                                                        </div>
                                                        <div class="text-sm text-gray-600 ml-8">
                                                            <p>{{ $option['company']['name'] }}</p>
                                                            <p>{{ $option['delivery_time'] }} dias úteis</p>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <span class="text-lg font-semibold text-gray-900">R$ {{ number_format($option['price'], 2, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-span-full p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-center">
                                            <p class="text-yellow-700">
                                                @if($addresses->count() > 0)
                                                    Não foi possível calcular opções de frete para o endereço selecionado. Por favor, verifique se o CEP está correto ou selecione outro endereço.
                                                @else
                                                    Não há opções de frete disponíveis. Por favor, cadastre um endereço válido.
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </form>
                            
                            <div class="flex justify-end">
                                <button type="button" id="update-shipping-btn" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed hidden">
                                    Atualizar opção de frete
                                </button>
                            </div>
                        </div>
                    @endif
                    
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

    <!-- Modal para atualização do CPF -->
    <div id="cpf-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg max-w-md w-full p-6 relative">
            <button type="button" id="close-cpf-modal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <h3 class="text-xl font-bold mb-4">Cadastrar CPF</h3>
            <p class="text-sm text-gray-600 mb-6">Informe seu CPF para continuar com a compra e emissão da nota fiscal.</p>
            
            <div id="cpf-modal-error" class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded-md"></div>
            <div id="cpf-modal-success" class="hidden mb-4 p-3 bg-green-100 text-green-700 rounded-md">CPF cadastrado com sucesso!</div>
            
            <form id="update-cpf-form" method="POST" action="{{ route('api.user.update-cpf') }}">
                @csrf
                <div class="mb-4">
                    <label for="cpf" class="block text-gray-700 text-sm font-medium mb-2">CPF</label>
                    <input type="text" id="cpf" name="cpf" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="000.000.000-00" required>
                    <p class="text-xs text-gray-500 mt-1">Digite apenas números ou no formato 000.000.000-00</p>
                </div>
                
                <div class="flex justify-end">
                    <button type="button" id="cancel-cpf-update" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md mr-2 hover:bg-gray-300 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Salvar CPF
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Script para o modal de CPF e opções de frete -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script para opções de frete
            const shippingOptions = document.querySelectorAll('.shipping-option');
            const updateShippingBtn = document.getElementById('update-shipping-btn');
            const shippingForm = document.getElementById('update-shipping-form');
            const selectedShippingInput = document.getElementById('selected_shipping_option');
            
            // Adicionar evento de clique em cada opção de frete
            if (shippingOptions.length > 0) {
                shippingOptions.forEach(option => {
                    option.addEventListener('click', function() {
                        // Remover a classe selecionada de todas as opções
                        shippingOptions.forEach(opt => {
                            opt.classList.remove('border-blue-500', 'bg-blue-50');
                            opt.classList.add('border-gray-200');
                            
                            // Atualizar ícones
                            const iconContainer = opt.querySelector('svg').parentElement.parentElement;
                            iconContainer.innerHTML = `
                                <div class="w-6 h-6 mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
                                    </svg>
                                </div>
                            `;
                        });
                        
                        // Adicionar classe selecionada na opção clicada
                        this.classList.add('border-blue-500', 'bg-blue-50');
                        this.classList.remove('border-gray-200');
                        
                        // Atualizar ícone para selecionado
                        const iconContainer = this.querySelector('svg').parentElement.parentElement;
                        iconContainer.innerHTML = `
                            <div class="w-6 h-6 mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                        `;
                        
                        // Atualizar o campo hidden com o ID da opção selecionada
                        const shippingId = this.getAttribute('data-shipping-id');
                        selectedShippingInput.value = shippingId;
                        
                        // Mostrar botão de atualizar
                        updateShippingBtn.classList.remove('hidden');
                    });
                });
                
                // Adicionar evento de clique ao botão de atualizar frete
                if (updateShippingBtn) {
                    updateShippingBtn.addEventListener('click', function() {
                        if (selectedShippingInput.value) {
                            shippingForm.submit();
                        }
                    });
                }
            }
            
            // Script para o modal de CPF
            const cpfModal = document.getElementById('cpf-modal');
            const openCpfModalBtn = document.getElementById('open-cpf-modal');
            const closeCpfModalBtn = document.getElementById('close-cpf-modal');
            const cancelCpfUpdateBtn = document.getElementById('cancel-cpf-update');
            const cpfForm = document.getElementById('update-cpf-form');
            const cpfInput = document.getElementById('cpf');
            const errorDiv = document.getElementById('cpf-modal-error');
            const successDiv = document.getElementById('cpf-modal-success');
            
            // Formatação de CPF enquanto digita
            cpfInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    value = value.replace(/^(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
                    value = value.replace(/^(\d{3})\.(\d{3})\.(\d{3})(\d)/, '$1.$2.$3-$4');
                    this.value = value;
                }
            });
            
            // Abrir modal
            if (openCpfModalBtn) {
                openCpfModalBtn.addEventListener('click', function() {
                    cpfModal.classList.remove('hidden');
                    errorDiv.classList.add('hidden');
                    successDiv.classList.add('hidden');
                });
            }
            
            // Fechar modal
            if (closeCpfModalBtn) {
                closeCpfModalBtn.addEventListener('click', function() {
                    cpfModal.classList.add('hidden');
                });
            }
            
            if (cancelCpfUpdateBtn) {
                cancelCpfUpdateBtn.addEventListener('click', function() {
                    cpfModal.classList.add('hidden');
                });
            }
            
            // Enviar formulário via AJAX
            if (cpfForm) {
                cpfForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Limpar mensagens anteriores
                    errorDiv.classList.add('hidden');
                    successDiv.classList.add('hidden');
                    
                    // Validação básica
                    const cpfValue = cpfInput.value.replace(/\D/g, '');
                    if (cpfValue.length !== 11) {
                        errorDiv.textContent = 'CPF inválido. Por favor, digite os 11 dígitos.';
                        errorDiv.classList.remove('hidden');
                        return;
                    }
                    
                    // Enviar via fetch API
                    fetch(cpfForm.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            cpf: cpfInput.value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Mostrar mensagem de sucesso
                            successDiv.classList.remove('hidden');
                            
                            // Atualizar a interface após 1s
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            // Mostrar erro
                            errorDiv.textContent = data.message || 'Ocorreu um erro ao atualizar o CPF. Tente novamente.';
                            errorDiv.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        errorDiv.textContent = 'Ocorreu um erro ao atualizar o CPF. Tente novamente.';
                        errorDiv.classList.remove('hidden');
                        console.error('Erro:', error);
                    });
                });
            }
        });
    </script>
</x-app-layout>