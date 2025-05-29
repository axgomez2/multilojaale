<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6 my-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Pedido #{{ $order->order_number }}</h1>
            <a href="{{ route('site.profile.orders.index') }}" class="text-purple-600 hover:text-purple-900">← Voltar para meus pedidos</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Status do Pedido -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="font-semibold text-lg mb-2">Status</h2>
                @if($order->status == 'pending')
                    <span class="px-2 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">Pendente</span>
                @elseif($order->status == 'payment_approved')
                    <span class="px-2 py-1 text-sm rounded-full bg-green-100 text-green-800">Pagamento Aprovado</span>
                @elseif($order->status == 'delivered')
                    <span class="px-2 py-1 text-sm rounded-full bg-blue-100 text-blue-800">Entregue</span>
                @elseif($order->status == 'canceled')
                    <span class="px-2 py-1 text-sm rounded-full bg-red-100 text-red-800">Cancelado</span>
                @else
                    <span class="px-2 py-1 text-sm rounded-full bg-gray-100 text-gray-800">{{ is_string($order->status) ? ucfirst($order->status) : $order->status->value }}</span>
                @endif
                
                <p class="text-sm text-gray-600 mt-2">Realizado em {{ $order->created_at->format('d/m/Y \à\s H:i') }}</p>
            </div>
            
            <!-- Informações de Pagamento -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="font-semibold text-lg mb-2">Pagamento</h2>
                @if($order->payment)
                    <p class="text-sm">
                        <span class="font-medium">Método:</span>
                        @if($order->payment->payment_method == 'credit_card')
                            Cartão de Crédito
                        @elseif($order->payment->payment_method == 'pix')
                            PIX
                        @elseif($order->payment->payment_method == 'boleto')
                            Boleto
                        @else
                            {{ $order->payment->payment_method }}
                        @endif
                    </p>
                    <p class="text-sm">
                        <span class="font-medium">Status:</span>
                        @if($order->payment_status == 'pending')
                            <span class="text-yellow-600">Pendente</span>
                        @elseif($order->payment_status == 'approved')
                            <span class="text-green-600">Aprovado</span>
                        @elseif($order->payment_status == 'failed')
                            <span class="text-red-600">Falhou</span>
                        @else
                            {{ ucfirst($order->payment_status) }}
                        @endif
                    </p>
                @else
                    <p class="text-sm text-gray-600">Informações de pagamento não disponíveis</p>
                @endif
            </div>
            
            <!-- Endereço de Entrega -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="font-semibold text-lg mb-2">Endereço de Entrega</h2>
                @if($order->address)
                    <p class="text-sm">{{ $order->address->street }}, {{ $order->address->number }}</p>
                    @if($order->address->complement)
                        <p class="text-sm">{{ $order->address->complement }}</p>
                    @endif
                    <p class="text-sm">{{ $order->address->neighborhood }}</p>
                    <p class="text-sm">{{ $order->address->city }} - {{ $order->address->state }}</p>
                    <p class="text-sm">CEP: {{ $order->address->zipcode }}</p>
                @else
                    <p class="text-sm text-gray-600">Endereço não disponível</p>
                @endif
            </div>
        </div>
        
        <!-- Produtos -->
        <div class="mb-6">
            <h2 class="font-semibold text-lg mb-4">Itens do Pedido</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left">Produto</th>
                            <th class="py-3 px-4 text-left">Qtd</th>
                            <th class="py-3 px-4 text-right">Preço Unit.</th>
                            <th class="py-3 px-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="py-4 px-4">
                                    <div class="flex items-center">
                                        @if($item->vinylMaster && $item->vinylMaster->cover)
                                            <img src="{{ asset('storage/' . $item->vinylMaster->cover) }}" alt="{{ $item->name }}" class="h-16 w-16 object-cover mr-3">
                                        @else
                                            <div class="h-16 w-16 bg-gray-200 flex items-center justify-center mr-3">
                                                <span class="text-gray-500">Sem imagem</span>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="font-medium">{{ $item->name }}</h3>
                                            @if($item->vinylMaster && $item->vinylMaster->artists->count() > 0)
                                                <p class="text-sm text-gray-600">{{ $item->vinylMaster->artists->pluck('name')->join(', ') }}</p>
                                            @endif
                                            @if($item->sku)
                                                <p class="text-xs text-gray-500">SKU: {{ $item->sku }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">{{ $item->quantity }}</td>
                                <td class="py-4 px-4 text-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td class="py-4 px-4 text-right font-medium">R$ {{ number_format($item->total_price, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="py-3 px-4 text-right font-medium">Subtotal:</td>
                            <td class="py-3 px-4 text-right font-medium">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="py-3 px-4 text-right font-medium">Frete:</td>
                            <td class="py-3 px-4 text-right font-medium">R$ {{ number_format($order->shipping, 2, ',', '.') }}</td>
                        </tr>
                        @if($order->discount > 0)
                        <tr>
                            <td colspan="3" class="py-3 px-4 text-right font-medium">Desconto:</td>
                            <td class="py-3 px-4 text-right font-medium text-green-600">-R$ {{ number_format($order->discount, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="py-3 px-4 text-right font-semibold">Total:</td>
                            <td class="py-3 px-4 text-right font-semibold">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        @if($order->customer_note)
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h2 class="font-semibold text-lg mb-2">Observações</h2>
                <p class="text-sm">{{ $order->customer_note }}</p>
            </div>
        @endif
        
        @if($order->status == 'pending')
            <div class="mt-6 flex justify-end">
                <button type="button" class="px-4 py-2 bg-red-600 text-white font-medium rounded hover:bg-red-700">Cancelar Pedido</button>
            </div>
        @endif
    </div>
</div>
</x-app-layout>
