<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto text-center">
            <div class="bg-green-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Pedido Realizado com Sucesso!</h1>
            <p class="text-gray-600 mb-8">Seu pedido #{{ $order->order_number }} foi recebido e está sendo processado.</p>
            
            <div class="bg-white rounded-lg shadow p-6 mb-6 text-left">
                <h2 class="text-xl font-semibold mb-4">Detalhes do Pedido</h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="font-medium">Itens do Pedido</h3>
                        <div class="divide-y">
                            @foreach($order->items as $item)
                            <div class="py-3 flex justify-between">
                                <div>
                                    <p>{{ $item->vinylMaster->title }}</p>
                                    <p class="text-sm text-gray-500">Qtd: {{ $item->quantity }}</p>
                                </div>
                                <span>R$ {{ number_format($item->subtotal, 2, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-medium">Endereço de Entrega</h3>
                        <p>{{ $order->address->street }}, {{ $order->address->number }}</p>
                        <p>{{ $order->address->neighborhood }} - {{ $order->address->city }}/{{ $order->address->state }}</p>
                        <p>CEP: {{ $order->address->zipcode }}</p>
                    </div>
                    
                    <div>
                        <h3 class="font-medium">Forma de Pagamento</h3>
                        <p>
                            @if(isset($order->payment) && $order->payment)
                                @if($order->payment->method == 'credit_card')
                                    Cartão de Crédito
                                @elseif($order->payment->method == 'pix')
                                    PIX
                                @elseif($order->payment->method == 'boleto')
                                    Boleto Bancário
                                @else
                                    {{$order->payment->method}}
                                @endif
                            @else 
                                Aguardando pagamento
                            @endif
                        </p>
                    </div>
                    
                    <div class="border-t pt-3">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Frete:</span>
                            <span>R$ {{ number_format($order->shipping, 2, ',', '.') }}</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Desconto:</span>
                            <span>-R$ {{ number_format($order->discount, 2, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-bold mt-2">
                            <span>Total:</span>
                            <span>R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-4 justify-center">
                <a href="{{ route('home') }}" class="bg-gray-200 text-gray-800 py-2 px-4 rounded-lg">
                    Continuar Comprando
                </a>
            </div>
        </div>
    </div>
</x-app-layout>