<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Mensagem de sucesso -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-4">Pedido Confirmado!</h1>
            <p class="text-xl text-gray-600 dark:text-gray-300">
                Obrigado pela sua compra. Seu pedido foi recebido e está sendo processado.
            </p>
        </div>

        <!-- Detalhes do pedido -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row sm:justify-between mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="mb-4 sm:mb-0">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Pedido #{{ $order->order_number }}</h2>
                        <p class="text-gray-600 dark:text-gray-300">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>

                <!-- Produtos -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Produtos</h3>
                    
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-start">
                                <div class="w-16 h-16 rounded overflow-hidden flex-shrink-0 bg-gray-100">
                                    @if($item->cover_image)
                                    <img src="{{ asset('storage/' . $item->cover_image) }}" 
                                         alt="{{ $item->product_name }}" 
                                         class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-base font-medium text-gray-800 dark:text-white">
                                        {{ $item->product_name }}
                                    </h4>
                                    
                                    @if($item->product_artist)
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        {{ $item->product_artist }}
                                    </p>
                                    @endif
                                    
                                    <div class="flex justify-between items-center mt-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-300">
                                            Qtd: {{ $item->quantity }} x {{ 'R$ ' . number_format($item->unit_price, 2, ',', '.') }}
                                        </span>
                                        <span class="text-base font-medium text-gray-800 dark:text-white">
                                            {{ 'R$ ' . number_format($item->quantity * $item->unit_price, 2, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Endereço de entrega -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Endereço de Entrega</h3>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-base font-medium text-gray-800 dark:text-white">
                                {{ $order->shipping_address_name }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                {{ $order->recipient_name }} • {{ $order->recipient_phone }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                {{ $order->shipping_address }}, {{ $order->shipping_number }}
                                @if($order->shipping_complement) - {{ $order->shipping_complement }} @endif
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ $order->shipping_district }}, {{ $order->shipping_city }} - {{ $order->shipping_state }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                CEP: {{ $order->shipping_zipcode }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Informações de pagamento -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Pagamento</h3>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            @php
                                $methodName = '';
                                if ($order->payment_method == 'credit_card') {
                                    $methodName = 'Cartão de Crédito';
                                } elseif ($order->payment_method == 'pix') {
                                    $methodName = 'Pix';
                                } elseif ($order->payment_method == 'boleto') {
                                    $methodName = 'Boleto Bancário';
                                }
                            @endphp
                            
                            <p class="text-base font-medium text-gray-800 dark:text-white">
                                {{ $methodName }}
                            </p>
                            
                            @if($order->payment_method == 'credit_card' && $order->payment_installments)
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    {{ $order->payment_installments }}x de {{ 'R$ ' . number_format($order->total / $order->payment_installments, 2, ',', '.') }}
                                </p>
                            @elseif($order->payment_method == 'pix')
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    Com 5% de desconto aplicado
                                </p>
                            @elseif($order->payment_method == 'boleto')
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    Com 3% de desconto aplicado
                                </p>
                            @endif
                            
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                Status: <span class="font-medium">{{ $order->payment_status }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Resumo de valores -->
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="space-y-2">
                        <div class="flex justify-between text-gray-600 dark:text-gray-300">
                            <span>Subtotal</span>
                            <span>{{ 'R$ ' . number_format($order->subtotal, 2, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between text-gray-600 dark:text-gray-300">
                            <span>Frete</span>
                            <span>{{ 'R$ ' . number_format($order->shipping_cost, 2, ',', '.') }}</span>
                        </div>
                        
                        @if($order->discount > 0)
                        <div class="flex justify-between text-green-600 dark:text-green-400">
                            <span>Desconto</span>
                            <span>-{{ 'R$ ' . number_format($order->discount, 2, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between font-bold text-gray-800 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700 text-xl">
                            <span>Total</span>
                            <span>{{ 'R$ ' . number_format($order->total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instruções de pagamento -->
        @if($order->payment_method == 'pix')
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Instruções de Pagamento - PIX</h3>
                
                <div class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="w-48 h-48 bg-white p-2 rounded-lg mb-4">
                        <!-- QR Code do PIX (exemplo) -->
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=00020126580014br.gov.bcb.pix0136123e4567-e12b-12d1-a456-4266554400005204000053039865802BR5913Recipient6008BRASILIA62070503***63041D3D" 
                             alt="QR Code PIX" 
                             class="w-full h-full">
                    </div>
                    
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                            Escaneie o QR code acima com o aplicativo do seu banco ou copie o código PIX abaixo:
                        </p>
                        
                        <div class="relative flex items-center mb-4">
                            <input type="text" 
                                   value="00020126580014br.gov.bcb.pix0136123e4567-e12b-12d1-a456-4266554400005204000053039865802BR5913Recipient6008BRASILIA62070503***63041D3D" 
                                   class="w-full py-2 px-3 pr-10 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm" 
                                   readonly 
                                   id="pix-code">
                            <button onclick="copyPixCode()" 
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                    <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                </svg>
                            </button>
                        </div>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            O pagamento será confirmado automaticamente em alguns instantes após a transferência.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @elseif($order->payment_method == 'boleto')
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Instruções de Pagamento - Boleto</h3>
                
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Seu boleto foi gerado com sucesso. Você pode pagá-lo em qualquer agência bancária, internet banking ou casas lotéricas até a data de vencimento.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4 mb-4">
                        <a href="#" 
                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-lg text-white font-medium transition-all duration-300 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            Baixar Boleto
                        </a>
                        
                        <a href="#" 
                           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg text-gray-800 dark:text-white font-medium transition-all duration-300 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                            Enviar por Email
                        </a>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-300 dark:border-gray-600">
                        <p class="text-sm text-gray-800 dark:text-gray-200 font-mono">
                            Código de barras: 34191.79001 01043.510047 91020.150008 9 87770026000
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Botões finais -->
        <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('site.account.orders') }}" 
               class="px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-lg text-white font-medium transition-all duration-300 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                </svg>
                Meus Pedidos
            </a>
            
            <a href="{{ route('site.home') }}" 
               class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg text-gray-800 dark:text-white font-medium transition-all duration-300 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                Voltar para Home
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyPixCode() {
        var copyText = document.getElementById("pix-code");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        
        // Mostrar feedback de copiado (pode ser implementado com um toast/notification)
        alert("Código PIX copiado!");
    }
</script>
@endpush
</x-app-layout>
