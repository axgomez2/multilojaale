<x-app-layout>
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <h1 class="text-2xl md:text-3xl font-bold mb-6 text-slate-800">Finalizar Pagamento</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Coluna do formulário de pagamento (ocupa 2/3 em desktop) -->
            <div class="lg:col-span-2">
                <!-- Itens do pedido em card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Pedido: {{ $order->order_number }} - Itens ({{ $order->items->count() }})
                    </h2>
                    
                    @php
                        // Filtrar itens sem estoque
                        $itemsWithStock = [];
                        $itemsWithoutStock = [];
                        $totalWithStock = 0;
                        $totalWithoutStock = 0;
                        
                        foreach($order->items as $item) {
                            $hasStock = true;
                            if ($item->vinylMaster && $item->vinylMaster->vinylSec) {
                                $hasStock = $item->vinylMaster->vinylSec->stock > 0 && $item->vinylMaster->vinylSec->stock >= $item->quantity;
                            }
                            
                            if ($hasStock) {
                                $itemsWithStock[] = $item;
                                $totalWithStock += $item->total_price;
                            } else {
                                $itemsWithoutStock[] = $item;
                                $totalWithoutStock += $item->total_price;
                            }
                        }
                    @endphp
                    
                    <div class="divide-y divide-gray-200">
                        @foreach($itemsWithStock as $item)
                            <div class="py-3 flex items-center">
                                <div class="flex-shrink-0 mr-3">
                                    <div class="bg-gray-100 rounded w-12 h-12 flex items-center justify-center">
                                        <span class="text-xs text-gray-600">Item</span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $item->name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Qtd: {{ $item->quantity }} × R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                                    </p>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    R$ {{ number_format($item->total_price, 2, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                        
                        @if(count($itemsWithoutStock) > 0)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-red-700">
                                                Os seguintes itens não estão disponíveis em estoque e não serão incluídos no pedido:
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                @foreach($itemsWithoutStock as $item)
                                    <div class="py-3 flex items-center bg-red-50 rounded-md px-3 mb-2">
                                        <div class="flex-shrink-0 mr-3">
                                            <div class="bg-red-100 rounded w-12 h-12 flex items-center justify-center">
                                                <span class="text-xs text-red-600">Sem estoque</span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-red-700 truncate">
                                                {{ $item->name }}
                                            </p>
                                            <p class="text-xs text-red-500">
                                                Qtd: {{ $item->quantity }} × R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                                            </p>
                                        </div>
                                        <div class="text-sm font-medium text-red-700">
                                            R$ {{ number_format($item->total_price, 2, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Resumo do pedido em card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Resumo do Pedido
                    </h2>
                    
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-700">Subtotal (itens disponíveis):</span>
                            <span class="font-medium">R$ {{ number_format($totalWithStock, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-700">Frete:</span>
                            <span class="font-medium">R$ {{ number_format($order->shipping ?? 0, 2, ',', '.') }}</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="flex justify-between mb-2 text-green-600">
                            <span>Desconto:</span>
                            <span class="font-medium">- R$ {{ number_format($order->discount, 2, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        @if($totalWithoutStock > 0)
                        <div class="flex justify-between mb-2 text-red-600">
                            <span>Itens indisponíveis (não cobrados):</span>
                            <span class="font-medium">R$ {{ number_format($totalWithoutStock, 2, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between pt-2 border-t border-gray-200 mt-2">
                            <span class="text-lg font-bold">Total:</span>
                            <span class="text-lg font-bold text-purple-700">R$ {{ number_format($totalWithStock + ($order->shipping ?? 0) - ($order->discount ?? 0), 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Métodos de pagamento com Mercado Pago -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Métodos de Pagamento
                    </h2>
                    
                    <div class="bg-gray-100 p-4 rounded-md mb-4">
                        <h3 class="font-medium mb-2">Detalhes do Pedido</h3>
                        <p><strong>Número do Pedido:</strong> {{ $order->order_number }}</p>
                        <p><strong>Status:</strong> {{ $order->status }}</p>
                        <p><strong>Data:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    <!-- Alerta para mensagens de erro/sucesso -->
                    <div id="payment-alert" class="hidden mb-4 p-4 rounded-md"></div>
                    
                    <!-- Indicador de carregamento do formulário de pagamento -->
                    <div id="loading-payment" class="py-8 text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary"></div>
                        <p class="mt-2 text-gray-600">Carregando opções de pagamento...</p>
                    </div>
                    
                    <!-- Formulário de pagamento com múltiplos métodos (cartão, PIX, etc) -->
                    <div id="payment-form" class="hidden">
                        <div id="paymentBrick_container" class="mb-6"></div>
                    </div>
                    
                    <!-- Botão de pagamento -->
                    <div class="mt-6">
                        <button id="pay-button" type="button" class="w-full py-3 px-4 bg-primary text-white font-semibold rounded-md hover:bg-primary-dark transition duration-200">
                            Finalizar Pagamento
                        </button>
                        <div id="processing-payment" class="hidden mt-4 text-center">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary"></div>
                            <p class="mt-2 text-gray-600">Processando seu pagamento...</p>
                        </div>
                    </div>
                    
                    <!-- Modal para exibir QR Code do PIX -->
                    <div id="pix-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
                        <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-bold text-gray-900">Pagamento via PIX</h3>
                                <button type="button" id="close-pix-modal" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Status do pagamento -->
                            <div id="pix-status" class="mb-4 text-center">
                                <div class="flex items-center justify-center mb-2">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary mr-2"></div>
                                    <span class="font-medium">Aguardando pagamento...</span>
                                </div>
                                <p class="text-sm text-gray-600">O pagamento será confirmado automaticamente após ser processado.</p>
                            </div>
                            
                            <div class="text-center">
                                <p class="mb-4 font-medium">Escaneie o QR code abaixo com o aplicativo do seu banco:</p>
                                <div class="flex justify-center mb-4">
                                    <img id="pix-qrcode" src="" alt="QR Code PIX" class="max-w-xs border p-2 rounded">
                                </div>
                                
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4 text-left">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-700 font-medium">Como pagar com PIX:</p>
                                            <ol class="text-sm text-blue-700 list-decimal pl-4 mt-1">
                                                <li>Abra o app do seu banco</li>
                                                <li>Escolha a opção PIX</li>
                                                <li>Escaneie o QR code ou copie o código abaixo</li>
                                                <li>Confirme os dados e valor</li>
                                                <li>Finalize o pagamento</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-2">Ou copie o código PIX:</p>
                                <div class="relative mb-4">
                                    <input id="pix-code" type="text" readonly class="w-full p-2 border border-gray-300 rounded-md bg-gray-50 text-sm" value="">
                                    <button id="copy-pix-code" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-primary hover:text-primary-dark">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                </div>
                                
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4 text-left">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-700">
                                                <strong>Importante:</strong> Não feche esta janela até a confirmação do pagamento.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <button id="check-pix-status" class="py-2 px-4 bg-primary text-white font-medium rounded-md hover:bg-primary-dark transition duration-200">
                                        Verificar Status
                                    </button>
                                    <button id="i-paid-button" class="py-2 px-4 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition duration-200">
                                        Já Paguei
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Scripts do Mercado Pago -->
                <script src="https://sdk.mercadopago.com/js/v2"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Inicializar o SDK do Mercado Pago
                        const mp = new MercadoPago('{{ config('services.mercadopago.public_key') }}', {
                            locale: 'pt-BR'
                        });
                        let paymentBrickController;
                        
                        // Criar preferência de pagamento
                        fetch('{{ route('site.mercadopago.create-preference', $order->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                showAlert('error', 'Erro ao inicializar pagamento: ' + data.message);
                                return;
                            }
                            
                            // Renderizar o componente de pagamento com múltiplos métodos
                            const bricksBuilder = mp.bricks();
                            const renderPaymentBrick = async (bricksBuilder) => {
                                const settings = {
                                    initialization: {
                                        amount: {{ $totalWithStock + ($order->shipping ?? 0) - ($order->discount ?? 0) }},
                                        preferenceId: data.id,
                                        payer: {
                                            email: '{{ $order->user->email }}',
                                            firstName: '{{ $order->user->name }}',
                                            lastName: ''
                                        }
                                    },
                                    customization: {
                                        visual: {
                                            hideFormTitle: true,
                                            hidePaymentButton: true
                                        },
                                        paymentMethods: {
                                            // Incluir apenas os métodos que queremos mostrar
                                            creditCard: 'all',
                                            bankTransfer: 'all',
                                            mercadoPago: 'all', // Saldo em conta Mercado Pago
                                            mercadoCredito: 'all', // Mercado Crédito
                                            pix: 'all'
                                        }
                                    },
                                    callbacks: {
                                        onReady: () => {
                                            // Brick pronto para uso
                                            document.getElementById('loading-payment').classList.add('hidden');
                                            document.getElementById('payment-form').classList.remove('hidden');
                                        },
                                        onError: (error) => {
                                            showAlert('error', 'Erro no formulário de pagamento: ' + error.message);
                                        },
                                        onSubmit: ({ selectedPaymentMethod, formData }) => {
                                            // Armazenar o método de pagamento selecionado
                                            window.selectedPaymentMethod = selectedPaymentMethod;
                                            return false; // Impedir o envio automático do formulário
                                        }
                                    }
                                };
                                
                                paymentBrickController = await bricksBuilder.create('payment', 'paymentBrick_container', settings);
                            };
                            
                            renderPaymentBrick(bricksBuilder);
                        })
                        .catch(error => {
                            showAlert('error', 'Erro ao inicializar pagamento: ' + error.message);
                        });
                        
                        // Função para lidar com pagamento PIX
                        function handlePixPayment(pixData) {
                            // O redirecionamento agora é feito pelo controlador
                            // Apenas exibir uma mensagem de processamento
                            showAlert('info', 'Processando pagamento PIX. Aguarde...');
                            
                            // Função para verificar o status do pagamento
                            async function checkPaymentStatus() {
                                try {
                                    const response = await fetch(`/api/payment/check-status/${orderId}`, {
                                        method: 'GET',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        }
                                    });
                                    
                                    const result = await response.json();
                                    
                                    if (result.status === 'approved') {
                                        // Pagamento aprovado
                                        clearInterval(pollingInterval);
                                        updatePixStatus('approved');
                                        setTimeout(() => {
                                            window.location.href = '{{ route("site.mercadopago.success", $order->id) }}';
                                        }, 2000);
                                    } else if (result.status === 'pending' || result.status === 'in_process') {
                                        // Pagamento pendente, continuar verificando
                                        updatePixStatus('pending');
                                    } else if (result.status === 'rejected') {
                                        // Pagamento rejeitado
                                        clearInterval(pollingInterval);
                                        updatePixStatus('rejected');
                                    }
                                    
                                    // Limitar o número de verificações
                                    checkCount++;
                                    if (checkCount >= maxChecks) {
                                        clearInterval(pollingInterval);
                                    }
                                } catch (error) {
                                    console.error('Erro ao verificar status do pagamento:', error);
                                }
                            }
                            
                            // Função para atualizar o status visual do PIX
                            function updatePixStatus(status) {
                                if (status === 'approved') {
                                    pixStatus.innerHTML = `
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span class="font-medium text-green-600">Pagamento aprovado!</span>
                                        </div>
                                        <p class="text-sm text-gray-600">Você será redirecionado em instantes...</p>
                                    `;
                                } else if (status === 'pending') {
                                    pixStatus.innerHTML = `
                                        <div class="flex items-center justify-center mb-2">
                                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary mr-2"></div>
                                            <span class="font-medium">Aguardando confirmação do pagamento...</span>
                                        </div>
                                        <p class="text-sm text-gray-600">Isso pode levar alguns instantes.</p>
                                    `;
                                } else if (status === 'rejected') {
                                    pixStatus.innerHTML = `
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="h-6 w-6 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            <span class="font-medium text-red-600">Pagamento rejeitado</span>
                                        </div>
                                        <p class="text-sm text-gray-600">Por favor, tente novamente ou escolha outro método de pagamento.</p>
                                    `;
                                }
                            }
                            
                            // Iniciar verificação periódica do status do pagamento (a cada 10 segundos)
                            pollingInterval = setInterval(checkPaymentStatus, 10000);
                            
                            // Verificar status imediatamente também
                            checkPaymentStatus();
                            
                            // Adicionar manipulador para o botão "Verificar Status"
                            document.getElementById('check-pix-status').addEventListener('click', function() {
                                checkPaymentStatus();
                            });
                            
                            // Adicionar manipulador para o botão "Já Paguei"
                            document.getElementById('i-paid-button').addEventListener('click', function() {
                                updatePixStatus('pending');
                                checkPaymentStatus();
                            });
                            
                            // Adicionar manipulador para o botão de copiar código PIX
                            document.getElementById('copy-pix-code').addEventListener('click', function() {
                                const pixCode = document.getElementById('pix-code');
                                pixCode.select();
                                document.execCommand('copy');
                                
                                // Feedback visual
                                this.innerHTML = `
                                    <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                `;
                                
                                setTimeout(() => {
                                    this.innerHTML = `
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    `;
                                }, 2000);
                            });
                            
                            // Adicionar manipulador para o botão de fechar o modal
                            document.getElementById('close-pix-modal').addEventListener('click', function() {
                                if (confirm('Tem certeza que deseja fechar? O pagamento ainda não foi confirmado.')) {
                                    pixModal.classList.add('hidden');
                                    if (pollingInterval) {
                                        clearInterval(pollingInterval);
                                    }
                                }
                            });
                        }
                        
                        // Função para mostrar alertas
                        function showAlert(type, message) {
                            const alertElement = document.getElementById('payment-alert');
                            if (!alertElement) return;
                            
                            alertElement.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
                            
                            if (type === 'success') {
                                alertElement.classList.add('bg-green-100', 'text-green-800');
                            } else {
                                alertElement.classList.add('bg-red-100', 'text-red-800');
                            }
                            
                            alertElement.textContent = message;
                        }
                        
                        // Função para mostrar/esconder processamento
                        function showProcessing(show) {
                            const processingElement = document.getElementById('processing-payment');
                            const payButton = document.getElementById('pay-button');
                            
                            if (show) {
                                processingElement.classList.remove('hidden');
                                payButton.disabled = true;
                                payButton.classList.add('opacity-50', 'cursor-not-allowed');
                            } else {
                                processingElement.classList.add('hidden');
                                payButton.disabled = false;
                                payButton.classList.remove('opacity-50', 'cursor-not-allowed');
                            }
                        }
                        
                        // Configurar eventos do modal PIX
                        document.getElementById('close-pix-modal')?.addEventListener('click', function() {
                            document.getElementById('pix-modal').classList.add('hidden');
                        });
                        
                        document.getElementById('copy-pix-code')?.addEventListener('click', function() {
                            const pixCode = document.getElementById('pix-code');
                            pixCode.select();
                            document.execCommand('copy');
                            alert('Código PIX copiado para a área de transferência!');
                        });
                        
                        document.getElementById('check-pix-status')?.addEventListener('click', function() {
                            window.location.href = '{{ route("site.mercadopago.pending", $order->id) }}';
                        });
                        
                        // Configurar botão de pagamento
                        document.getElementById('pay-button').addEventListener('click', async function() {
                            try {
                                showProcessing(true);
                                
                                // Obter os dados do formulário
                                const formData = await paymentBrickController.getFormData();
                                
                                if (!formData) {
                                    throw new Error('Não foi possível obter os dados do formulário de pagamento');
                                }
                                
                                // Garantir que o payment_method_id esteja definido para PIX
                                // O PIX é identificado como bank_transfer no selectedPaymentMethod
                                if (window.selectedPaymentMethod === 'bank_transfer' || window.selectedPaymentMethod === 'pix') {
                                    // Verificar se o formData já tem payment_method_id
                                    if (!formData.payment_method_id) {
                                        formData.payment_method_id = 'pix';
                                    }
                                    
                                    // Adicionar explicitamente ao formData.formData se existir
                                    if (formData.formData) {
                                        formData.formData.payment_method_id = 'pix';
                                    }
                                }
                                
                                // Para pagamentos PIX, usar um formulário para permitir redirecionamento
                                if (window.selectedPaymentMethod === 'bank_transfer' || window.selectedPaymentMethod === 'pix') {
                                    // Criar um formulário temporário para enviar os dados e permitir redirecionamento
                                    const form = document.createElement('form');
                                    form.method = 'POST';
                                    form.action = '{{ route("site.mercadopago.process", $order->id) }}';
                                    form.style.display = 'none';
                                    
                                    // Adicionar CSRF token
                                    const csrfInput = document.createElement('input');
                                    csrfInput.type = 'hidden';
                                    csrfInput.name = '_token';
                                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                                    form.appendChild(csrfInput);
                                    
                                    // Adicionar dados do pagamento
                                    const dataInput = document.createElement('input');
                                    dataInput.type = 'hidden';
                                    dataInput.name = 'payment_data';
                                    dataInput.value = JSON.stringify(formData);
                                    form.appendChild(dataInput);
                                    
                                    // Adicionar o formulário ao documento e enviar
                                    document.body.appendChild(form);
                                    form.submit();
                                    return;
                                }
                                
                                // Para outros métodos de pagamento, continuar com o fetch normal
                                const response = await fetch('{{ route("site.mercadopago.process", $order->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify(formData)
                                });
                                
                                if (!response.ok) {
                                    throw new Error('Erro ao processar o pagamento: ' + response.status);
                                }
                                
                                const result = await response.json();
                                
                                // Processar a resposta JSON
                                if (result.payment_method === 'pix' && result.pix_data) {
                                    showProcessing(false);
                                    handlePixPayment(result.pix_data);
                                } else {
                                    // Outros métodos de pagamento
                                    if (result.status === 'approved') {
                                        window.location.href = '{{ route("site.mercadopago.success", $order->id) }}';
                                    } else if (result.status === 'pending' || result.status === 'in_process') {
                                        window.location.href = '{{ route("site.mercadopago.pending", $order->id) }}';
                                    } else {
                                        window.location.href = '{{ route("site.mercadopago.failure", $order->id) }}';
                                    }
                                }
                            } catch (error) {
                                console.error('Erro ao processar pagamento:', error);
                                showProcessing(false);
                                showAlert('error', 'Erro ao processar o pagamento: ' + error.message);
                            }
                        });
                    });
                </script>
            </div>
            
            <!-- Coluna lateral (1/3 em desktop) -->
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
                    
                    <div class="text-sm text-gray-700">
                        @if($order->shippingAddress)
                            <p class="font-semibold">{{ $order->shippingAddress->recipient_name }}</p>
                            <p>{{ $order->shippingAddress->street }}, {{ $order->shippingAddress->number }}</p>
                            @if($order->shippingAddress->complement)
                                <p>{{ $order->shippingAddress->complement }}</p>
                            @endif
                            <p>{{ $order->shippingAddress->district }} - {{ $order->shippingAddress->city }}/{{ $order->shippingAddress->state }}</p>
                            <p>CEP: {{ $order->shippingAddress->zipcode }}</p>
                            <p class="mt-1">Tel: {{ $order->shippingAddress->recipient_phone }}</p>
                        @else
                            <p>Nenhum endereço selecionado</p>
                        @endif
                    </div>
                </div>
                
                <!-- Informações de envio -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        Informações de Envio
                    </h2>
                    
                    <div class="text-sm text-gray-700">
                        <p><strong>Método:</strong> {{ $order->shipping_method }}</p>
                        <p><strong>Prazo estimado:</strong> {{ $order->shippingQuote->selected_delivery_time ?? '-' }} dia(s) úteis</p>
                        @if($order->tracking_number)
                            <p><strong>Código de rastreio:</strong> {{ $order->tracking_number }}</p>
                            @if($order->tracking_url)
                                <p class="mt-2">
                                    <a href="{{ $order->tracking_url }}" target="_blank" class="text-purple-600 hover:text-purple-800 underline">Rastrear pedido</a>
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>