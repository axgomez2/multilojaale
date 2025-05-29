<x-site-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-8 max-w-3xl mx-auto">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-yellow-100 mb-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Aguardando Pagamento</h1>
                <p class="text-lg text-gray-600 mt-2">Seu pedido foi registrado com sucesso!</p>
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
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Aguardando Pagamento
                    </span>
                </div>
            </div>
            
            @if($payment->method === 'pix')
                <div class="bg-blue-50 p-6 rounded-lg mb-6">
                    <h2 class="text-xl font-semibold mb-4 text-blue-800">Pagamento via PIX</h2>
                    
                    @if($payment->getPixQrCode())
                        <div class="flex flex-col items-center mb-4">
                            <img src="{{ $payment->getPixQrCode() }}" alt="QR Code PIX" class="w-48 h-48 mb-2">
                            <p class="text-sm text-gray-600">Escaneie o QR Code com o aplicativo do seu banco</p>
                        </div>
                    @endif
                    
                    @if($payment->getPixCopyPaste())
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">PIX Copia e Cola</label>
                            <div class="relative">
                                <input type="text" value="{{ $payment->getPixCopyPaste() }}" readonly class="w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm bg-gray-50 focus:outline-none" id="pix-code">
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
            @elseif($payment->method === 'boleto')
                <div class="bg-yellow-50 p-6 rounded-lg mb-6">
                    <h2 class="text-xl font-semibold mb-4 text-yellow-800">Pagamento via Boleto Bancário</h2>
                    
                    @if($payment->getBoletoUrl())
                        <div class="mb-4">
                            <a href="{{ $payment->getBoletoUrl() }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Visualizar / Imprimir Boleto
                            </a>
                        </div>
                    @endif
                    
                    <div class="text-sm text-gray-600">
                        <p>• O boleto vence em 3 dias úteis</p>
                        <p>• Após o pagamento, aguarde até 3 dias úteis para a confirmação</p>
                        <p>• Você receberá um e-mail quando o pagamento for confirmado</p>
                    </div>
                </div>
            @endif
            
            <div class="border-t border-gray-200 pt-6 mt-6">
                <h2 class="text-xl font-semibold mb-4">Resumo do Pedido</h2>
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
                
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span>{{ 'R$ ' . number_format($order->subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between mt-2">
                        <span class="text-gray-600">Frete:</span>
                        <span>{{ 'R$ ' . number_format($order->shipping, 2, ',', '.') }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="flex justify-between mt-2">
                        <span class="text-gray-600">Desconto:</span>
                        <span>-{{ 'R$ ' . number_format($order->discount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-semibold text-lg pt-2 mt-2 border-t border-gray-200">
                        <span>Total:</span>
                        <span>{{ 'R$ ' . number_format($order->total, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 space-y-4">
                <div class="text-center">
                    <p class="text-gray-600">Assim que seu pagamento for confirmado, você receberá um e-mail em <strong>{{ auth()->user()->email }}</strong></p>
                </div>
                
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('site.account.orders') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Ver meus pedidos
                    </a>
                    <a href="{{ route('site.home') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Voltar para a loja
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function copyPixCode() {
            const pixCode = document.getElementById('pix-code');
            pixCode.select();
            document.execCommand('copy');
            
            // Mostrar feedback
            alert('Código PIX copiado para a área de transferência!');
        }
        
        // Verificar o status do pagamento a cada 30 segundos
        function checkPaymentStatus() {
            fetch('{{ route("site.checkout.payment.check-status", ["orderId" => $order->id, "transactionId" => $payment->transaction_id]) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.status === 'approved') {
                        // Se o pagamento foi aprovado, redirecionar para a página de confirmação
                        window.location.href = data.redirect;
                    }
                })
                .catch(error => console.error('Erro ao verificar status do pagamento:', error));
        }
        
        // Verificar o status a cada 30 segundos
        setInterval(checkPaymentStatus, 30000);
        
        // Verificar o status imediatamente após o carregamento da página
        document.addEventListener('DOMContentLoaded', function() {
            checkPaymentStatus();
        });
    </script>
    @endpush
</x-site-layout>
