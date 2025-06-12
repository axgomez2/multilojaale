<x-app-layout>
    <div class="container mx-auto px-4 py-8 max-w-3xl">
        <!-- Banner de sucesso -->
        <div class="bg-green-50 border-l-4 border-green-500 p-6 mb-8 rounded-r shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-green-800 text-2xl font-bold">Pedido Realizado com Sucesso!</h2>
                    <p class="text-green-700 mt-1">
                        Seu pedido número <span class="font-semibold">{{ $order->order_number }}</span> foi registrado e será processado em breve.
                    </p>
                </div>
            </div>
        </div>

        <!-- Status do pedido -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h3 class="text-xl font-semibold mb-4 border-b pb-2">Status do Pedido</h3>
            
            <div class="flex flex-wrap">
                <div class="w-full md:w-1/2 mb-4">
                    <span class="block text-sm text-gray-500">Número do Pedido</span>
                    <span class="block font-medium text-gray-800">{{ $order->order_number }}</span>
                </div>
                <div class="w-full md:w-1/2 mb-4">
                    <span class="block text-sm text-gray-500">Data do Pedido</span>
                    <span class="block font-medium text-gray-800">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="w-full md:w-1/2 mb-4">
                    <span class="block text-sm text-gray-500">Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        @if($order->status->value == 'paid' || $order->status->value == 'payment_approved')
                            bg-green-100 text-green-800
                        @elseif($order->status->value == 'pending')
                            bg-yellow-100 text-yellow-800
                        @else
                            bg-blue-100 text-blue-800
                        @endif">
                        @if($order->status->value == 'paid' || $order->status->value == 'payment_approved')
                            Pagamento Aprovado
                        @elseif($order->status->value == 'pending')
                            Aguardando Pagamento
                        @elseif($order->status->value == 'processing')
                            Em Processamento
                        @elseif($order->status->value == 'shipped')
                            Enviado
                        @elseif($order->status->value == 'delivered')
                            Entregue
                        @elseif($order->status->value == 'cancelled')
                            Cancelado
                        @elseif($order->status->value == 'payment_failed')
                            Pagamento Recusado
                        @else
                            {{ ucfirst($order->status->value) }}
                        @endif
                    </span>
                </div>
                <div class="w-full md:w-1/2 mb-4">
                    <span class="block text-sm text-gray-500">Total</span>
                    <span class="block font-bold text-gray-800">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Resumo do pedido -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h3 class="text-xl font-semibold mb-4 border-b pb-2">Resumo do Pedido</h3>
            
            <!-- Itens do pedido -->
            <div class="mb-6">
                @foreach($order->items as $item)
                <div class="flex items-center py-4 border-b">
                    <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded-md flex items-center justify-center mr-4">
                        <svg class="w-8 h-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800">{{ $item->name }}</h4>
                        <p class="text-sm text-gray-600">Quantidade: {{ $item->quantity }}</p>
                        <p class="text-sm text-gray-500">
                            @if($item->sku)
                                SKU: {{ $item->sku }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-gray-800 font-medium">R$ {{ number_format($item->total_price, 2, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Resumo financeiro -->
            <div class="border-t pt-4">
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="text-gray-800">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Frete</span>
                    <span class="text-gray-800">R$ {{ number_format($order->shipping, 2, ',', '.') }}</span>
                </div>
                @if($order->discount > 0)
                <div class="flex justify-between py-2 text-green-600">
                    <span>Desconto</span>
                    <span>-R$ {{ number_format($order->discount, 2, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between py-2 border-t font-bold mt-2">
                    <span>Total</span>
                    <span>R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Endereço de entrega -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h3 class="text-xl font-semibold mb-4 border-b pb-2">Endereço de Entrega</h3>
            
            @if($order->shippingAddress)
            <div>
                <p class="font-medium">{{ $order->shippingAddress->recipient_name }}</p>
                <p class="text-gray-600">{{ $order->shippingAddress->street }}, {{ $order->shippingAddress->number }}</p>
                @if($order->shippingAddress->complement)
                    <p class="text-gray-600">{{ $order->shippingAddress->complement }}</p>
                @endif
                <p class="text-gray-600">{{ $order->shippingAddress->district }}</p>
                <p class="text-gray-600">{{ $order->shippingAddress->city }} - {{ $order->shippingAddress->state }}</p>
                <p class="text-gray-600">CEP: {{ $order->shippingAddress->zipcode }}</p>
                <p class="text-gray-600 mt-2">Telefone: {{ $order->shippingAddress->recipient_phone }}</p>
            </div>
            @else
            <p class="text-gray-500">Endereço de entrega não disponível.</p>
            @endif
        </div>

        <!-- Botões de ação -->
        <div class="flex flex-wrap justify-between mt-8">
            <a href="{{ route('home') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 px-6 rounded-lg mb-4 md:mb-0 transition duration-200">
                Continuar comprando
            </a>
            <a href="{{ route('site.account.orders') }}" class="bg-purple-600 hover:bg-purple-700 text-white py-3 px-6 rounded-lg transition duration-200">
                Ver meus pedidos
            </a>
        </div>
    </div>
</x-app-layout>
