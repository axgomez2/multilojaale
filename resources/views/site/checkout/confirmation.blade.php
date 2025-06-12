<x-site-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-8 max-w-3xl mx-auto">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Pedido Confirmado!</h1>
                <p class="text-lg text-gray-600 mt-2">Agradecemos por sua compra</p>
            </div>
            
            <div class="border-t border-b border-gray-200 py-4 mb-6">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Número do Pedido:</span>
                    <span class="font-semibold">{{ $order->id }}</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-gray-600">Data:</span>
                    <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-gray-600">Status:</span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-800">
                        {{ $order->status->label() }}
                    </span>
                </div>
            </div>
            
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-4">Itens do Pedido</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-start">
                            <div class="w-16 h-16 flex-shrink-0">
                                @if($item->vinyl->cover_image)
                                    <img src="{{ asset('storage/' . $item->vinyl->cover_image) }}" alt="{{ $item->vinyl->title }}" class="w-full h-full object-cover rounded">
                                @else
                                    <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                                        <span class="text-gray-500 text-xs">Sem imagem</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between">
                                    <div>
                                        <h3 class="text-sm font-medium">{{ $item->vinyl->title }}</h3>
                                        <p class="text-xs text-gray-500">{{ $item->vinyl->artists->pluck('name')->join(', ') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm">{{ $item->quantity }} x {{ $item->formattedUnitPrice() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-4">Resumo</h2>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span>{{ 'R$ ' . number_format($order->subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Frete:</span>
                        <span>{{ 'R$ ' . number_format($order->shipping, 2, ',', '.') }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Desconto:</span>
                        <span>-{{ 'R$ ' . number_format($order->discount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-semibold text-lg pt-2 border-t border-gray-200">
                        <span>Total:</span>
                        <span>{{ 'R$ ' . number_format($order->total, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            @if($order->payment && $order->payment->method === 'pix')
                <div class="bg-blue-50 p-6 rounded-lg mb-6">
                    <h2 class="text-xl font-semibold mb-4 text-blue-800">Pagamento via PIX</h2>
                    
                    @if($order->payment->getPixQrCode())
                        <div class="flex flex-col items-center mb-4">
                            <img src="{{ $order->payment->getPixQrCode() }}" alt="QR Code PIX" class="w-48 h-48 mb-2">
                            <p class="text-sm text-gray-600">Escaneie o QR Code com o aplicativo do seu banco</p>
                        </div>
                    @endif
                    
                    @if($order->payment->getPixCopyPaste())
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">PIX Copia e Cola</label>
                            <div class="relative">
                                <input type="text" value="{{ $order->payment->getPixCopyPaste() }}" readonly class="w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm bg-gray-50 focus:outline-none" id="pix-code">
                                <button type="button" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-primary hover:text-primary-dark" onclick="copyPixCode()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Clique no botão para copiar o código</p>
                        </div>
                    @endif
                    
                    <div class="text-sm text-gray-600">
                        <p>• O pagamento via PIX é processado instantaneamente</p>
                        <p>• Após o pagamento, seu pedido será processado automaticamente</p>
                        <p>• O QR Code é válido por 24 horas</p>
                    </div>
                </div>
            @elseif($order->payment && $order->payment->method === 'boleto')
                <div class="bg-yellow-50 p-6 rounded-lg mb-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-yellow-800">Pagamento via Boleto Bancário</h2>
                            <p class="text-sm text-yellow-700">Seu pedido será confirmado após a compensação do pagamento</p>
                        </div>
                        <div class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            Aguardando pagamento
                        </div>
                    </div>
                    
                    <div class="bg-white p-4 rounded-md border border-yellow-200 mb-4">
                        @if($order->payment->getBoletoUrl())
                            <div class="mb-4">
                                <a href="{{ $order->payment->getBoletoUrl() }}" target="_blank" class="inline-flex items-center justify-center w-full px-4 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    Visualizar e Imprimir Boleto
                                </a>
                            </div>
                            
                            @if(isset($order->payment->gateway_data['barcode']))
                                <div class="bg-gray-50 p-3 rounded border border-gray-200 mb-4">
                                    <div class="text-center mb-2">
                                        <span class="text-xs font-medium text-gray-500">CÓDIGO DE BARRAS</span>
                                    </div>
                                    <div class="bg-white p-2 border border-gray-300 rounded text-center font-mono tracking-widest select-all">
                                        {{ $order->payment->gateway_data['barcode'] }}
                                    </div>
                                    <button 
                                        type="button" 
                                        onclick="copyToClipboard('{{ $order->payment->gateway_data['barcode'] }}', 'Código de barras copiado!')"
                                        class="mt-2 text-xs text-primary hover:text-primary-dark flex items-center justify-center w-full"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                        </svg>
                                        Copiar código de barras
                                    </button>
                                </div>
                            @endif
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                            <div class="space-y-1">
                                <p class="font-medium">Valor do Boleto</p>
                                <p class="text-lg font-bold text-gray-900">R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="font-medium">Vencimento</p>
                                <p>{{ \Carbon\Carbon::parse($order->payment->gateway_data['expiration_date'] ?? now()->addDays(3))->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h2a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Importante</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>O boleto pode levar até 3 dias úteis para ser compensado após o pagamento</li>
                                        <li>Após o pagamento, seu pedido será processado automaticamente</li>
                                        <li>Você receberá um e-mail de confirmação quando o pagamento for aprovado</li>
                                        <li>O boleto pode ser pago em qualquer banco ou internet banking</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="mt-8 space-y-4">
                <div class="text-center">
                    <p class="text-gray-600">Enviamos um e-mail de confirmação para <strong>{{ auth()->user()->email }}</strong></p>
                </div>
                
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('site.account.orders') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Ver meus pedidos
                    </a>
                    <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Voltar para a loja
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        // Função para copiar texto para a área de transferência
        function copyToClipboard(text, message) {
            navigator.clipboard.writeText(text).then(function() {
                // Feedback visual para o usuário
                const toast = document.createElement('div');
                toast.textContent = message || 'Copiado para a área de transferência!';
                toast.style.position = 'fixed';
                toast.style.bottom = '20px';
                toast.style.left = '50%';
                toast.style.transform = 'translateX(-50%)';
                toast.style.backgroundColor = '#10B981';
                toast.style.color = 'white';
                toast.style.padding = '10px 20px';
                toast.style.borderRadius = '4px';
                toast.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
                toast.style.zIndex = '1000';
                toast.style.animation = 'fadeInOut 3s forwards';
                
                // Adiciona animação ao CSS
                const style = document.createElement('style');
                style.textContent = `
                    @keyframes fadeInOut {
                        0% { opacity: 0; transform: translate(-50%, 10px); }
                        10% { opacity: 1; transform: translate(-50%, 0); }
                        90% { opacity: 1; transform: translate(-50%, 0); }
                        100% { opacity: 0; transform: translate(-50%, -10px); }
                    }
                `;
                document.head.appendChild(style);
                
                document.body.appendChild(toast);
                
                // Remove o toast após a animação
                setTimeout(() => {
                    document.body.removeChild(toast);
                    document.head.removeChild(style);
                }, 3000);
            }).catch(err => {
                console.error('Erro ao copiar texto: ', err);
                alert('Não foi possível copiar o texto. Tente novamente.');
            });
        }

        function copyPixCode() {
            const pixCode = document.getElementById('pix-code');
            copyToClipboard(pixCode.value, 'Código PIX copiado!');
        }
        
        // Se for pagamento PIX ou boleto e o status estiver pendente, verificar o status periodicamente
        @if(($order->payment && ($order->payment->method === 'pix' || $order->payment->method === 'boleto')) && $order->status->value === 'pending')
            function checkPaymentStatus() {
                fetch('{{ route("site.checkout.payment.check-status", ["orderId" => $order->id, "transactionId" => $order->payment->transaction_id]) }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.status === 'approved') {
                            // Se o pagamento foi aprovado, redirecionar para a página de confirmação
                            window.location.reload();
                        }
                    })
                    .catch(error => console.error('Erro ao verificar status do pagamento:', error));
            }
            
            // Verificar o status a cada 30 segundos
            setInterval(checkPaymentStatus, 30000);
        @endif
    </script>
    @endpush
</x-site-layout>
