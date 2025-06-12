<x-app-layout>
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Pagamento via PIX</h1>
                <a href="{{ route('site.checkout.payment', $order->id) }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            </div>
            
            <!-- Informações do pedido -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h2 class="text-lg font-semibold mb-2">Resumo do Pedido #{{ $order->id }}</h2>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <p class="text-gray-600">Data:</p>
                        <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Valor Total:</p>
                        <p class="font-medium text-lg text-primary">R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Status do pagamento -->
            <div id="pix-status" class="mb-6 p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center justify-center mb-2">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary mr-2"></div>
                    <span class="font-medium">Aguardando pagamento...</span>
                </div>
                <p class="text-sm text-gray-600 text-center">O pagamento será confirmado automaticamente após ser processado.</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- QR Code -->
                <div class="flex flex-col items-center">
                    <h3 class="text-lg font-semibold mb-3">Escaneie o QR code</h3>
                    <div class="bg-white border p-3 rounded-lg mb-4">
                        <img id="pix-qrcode" src="" alt="QR Code PIX" class="max-w-full h-auto">
                    </div>
                    <p class="text-sm text-gray-600 text-center">Use o aplicativo do seu banco para escanear o código</p>
                </div>
                
                <!-- Código PIX -->
                <div>
                    <h3 class="text-lg font-semibold mb-3">Código PIX</h3>
                    <div class="relative mb-4">
                        <input id="pix-code" type="text" readonly class="w-full p-3 border border-gray-300 rounded-md bg-gray-50 text-sm" value="">
                        <button id="copy-pix-code" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-primary hover:text-primary-dark">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
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
                                    <li>Escaneie o QR code ou copie o código acima</li>
                                    <li>Confirme os dados e valor</li>
                                    <li>Finalize o pagamento</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Importante:</strong> Não feche esta página até a confirmação do pagamento.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-center space-x-4">
                <button id="check-pix-status" class="py-2 px-6 bg-primary text-white font-medium rounded-md hover:bg-primary-dark transition duration-200">
                    Verificar Status
                </button>
                <button id="i-paid-button" class="py-2 px-6 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition duration-200">
                    Já Paguei
                </button>
            </div>
        </div>
        
        <!-- Links úteis -->
        <div class="flex justify-center space-x-6 mt-4">
            <a href="{{ route('site.checkout.payment', $order->id) }}" class="text-gray-600 hover:text-primary">
                <span class="flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Voltar para o checkout
                </span>
            </a>
            <a href="{{ route('site.profile.orders.show', $order->id) }}" class="text-gray-600 hover:text-primary">
                <span class="flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Detalhes do pedido
                </span>
            </a>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pixQrcode = document.getElementById('pix-qrcode');
            const pixCode = document.getElementById('pix-code');
            const pixStatus = document.getElementById('pix-status');
            const orderId = '{{ $order->id }}';
            let pollingInterval = null;
            let checkCount = 0;
            const maxChecks = 30; // Verificar por até 5 minutos (10s * 30)
            
            // Preencher os dados do PIX
            @if(isset($pixData))
                @if(isset($pixData['qr_code_base64']))
                    pixQrcode.src = `data:image/png;base64,{{ $pixData['qr_code_base64'] }}`;
                @elseif(isset($pixData['ticket_url']))
                    // Abrir ticket em nova aba se não tiver QR code
                    window.open('{{ $pixData['ticket_url'] }}', '_blank');
                @endif
                
                @if(isset($pixData['qr_code']))
                    pixCode.value = '{{ $pixData['qr_code'] }}';
                @endif
            @endif
            
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
                        <p class="text-sm text-gray-600 text-center">Você será redirecionado em instantes...</p>
                    `;
                    pixStatus.className = 'mb-6 p-4 bg-green-50 rounded-lg';
                } else if (status === 'pending') {
                    pixStatus.innerHTML = `
                        <div class="flex items-center justify-center mb-2">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary mr-2"></div>
                            <span class="font-medium">Aguardando confirmação do pagamento...</span>
                        </div>
                        <p class="text-sm text-gray-600 text-center">Isso pode levar alguns instantes.</p>
                    `;
                    pixStatus.className = 'mb-6 p-4 bg-blue-50 rounded-lg';
                } else if (status === 'rejected') {
                    pixStatus.innerHTML = `
                        <div class="flex items-center justify-center mb-2">
                            <svg class="h-6 w-6 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="font-medium text-red-600">Pagamento rejeitado</span>
                        </div>
                        <p class="text-sm text-gray-600 text-center">Por favor, tente novamente ou escolha outro método de pagamento.</p>
                    `;
                    pixStatus.className = 'mb-6 p-4 bg-red-50 rounded-lg';
                }
            }
            
            // Iniciar verificação periódica do status do pagamento (a cada 10 segundos)
            pollingInterval = setInterval(checkPaymentStatus, 10000);
            
            // Verificar status imediatamente também
            checkPaymentStatus();
            
            // Copiar código PIX
            document.getElementById('copy-pix-code').addEventListener('click', function() {
                const pixCode = document.getElementById('pix-code');
                pixCode.select();
                document.execCommand('copy');
                
                // Feedback visual
                const button = this;
                const originalHTML = button.innerHTML;
                button.innerHTML = `
                    <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                `;
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                }, 2000);
            });
            
            // Verificar status manualmente
            document.getElementById('check-pix-status').addEventListener('click', function() {
                checkPaymentStatus();
            });
            
            // Botão "Já paguei"
            document.getElementById('i-paid-button').addEventListener('click', function() {
                updatePixStatus('pending');
                checkPaymentStatus();
            });
        });
    </script>
</x-app-layout>
