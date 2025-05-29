<x-app-layout title="Meus Pedidos">
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6 my-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Meus Pedidos</h1>
        
        @if($orders->isEmpty())
            <div class="bg-gray-50 p-6 rounded-lg text-center">
                <p class="text-gray-600">Você ainda não possui pedidos.</p>
                <a href="{{ route('site.home') }}" class="mt-4 inline-block px-4 py-2 bg-purple-600 text-white font-semibold rounded hover:bg-purple-700">Explorar produtos</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left">Pedido #</th>
                            <th class="py-3 px-4 text-left">Data</th>
                            <th class="py-3 px-4 text-left">Produtos</th>
                            <th class="py-3 px-4 text-left">Total</th>
                            <th class="py-3 px-4 text-left">Status</th>
                            <th class="py-3 px-4 text-left">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium">{{ $order->order_number }}</td>
                                <td class="py-3 px-4">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3 px-4">{{ $order->items->count() }} item(s)</td>
                                <td class="py-3 px-4 font-medium">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                                <td class="py-3 px-4">
                                    @if($order->status == 'pending')
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pendente</span>
                                    @elseif($order->status == 'payment_approved')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Pagamento Aprovado</span>
                                    @elseif($order->status == 'delivered')
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Entregue</span>
                                    @elseif($order->status == 'canceled')
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Cancelado</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">{{ is_string($order->status) ? ucfirst($order->status) : $order->status->value }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Default</button>
                                    <a href="{{ route('site.profile.orders.show', $order->order_number) }}" class="text-purple-600 hover:text-purple-900">Ver detalhes</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
</x-app-layout>
