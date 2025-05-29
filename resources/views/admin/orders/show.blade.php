<x-admin-layout title="Detalhes do Pedido #{{ $order->order_number }}">
<div class="px-4">
    <h1 class="text-2xl font-bold mt-6 mb-4">Pedido #{{ $order->order_number }}</h1>

    <nav class="flex mb-4 text-sm text-gray-500 space-x-2">
        <a href="{{ route('admin.dashboard') }}" class="hover:underline">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.orders.index') }}" class="hover:underline">Pedidos</a>
        <span>/</span>
        <span class="text-gray-700 font-semibold">Pedido #{{ $order->id }}</span>
    </nav>

    {{-- Alerts --}}
    @foreach (['success' => 'green', 'error' => 'red', 'label_url' => 'blue'] as $type => $color)
        @if(session($type))
            <div class="mb-4 p-4 text-{{ $color }}-800 bg-{{ $color }}-100 rounded-lg flex justify-between items-start" role="alert">
                <div>
                    @if($type === 'label_url')
                        <p>Etiqueta gerada com sucesso!</p>
                        <a href="{{ session($type) }}" target="_blank" class="inline-flex items-center mt-2 px-3 py-1 text-white bg-blue-600 hover:bg-blue-700 rounded">
                            <i class="fas fa-download mr-1"></i> Baixar Etiqueta
                        </a>
                    @else
                        {{ session($type) }}
                    @endif
                </div>
                <button @click="close = false" class="text-xl leading-none hover:text-black">&times;</button>
            </div>
        @endif
    @endforeach

    {{-- Detalhes do pedido --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2 space-y-4">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h2 class="font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i> Informações do Pedido
                    </h2>
                    <div>
                        <span class="mr-2">Status:</span>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-400',
                                'payment_approved' => 'bg-green-500',
                                'delivered' => 'bg-blue-500',
                                'canceled' => 'bg-red-500',
                            ];
                            
                            // Obtém o valor de status de forma segura
                            if (is_string($order->status)) {
                                $statusValue = $order->status;
                            } elseif (is_object($order->status) && method_exists($order->status, 'value')) {
                                $statusValue = $order->status->value;
                            } elseif ($order->payment_status == 'approved') {
                                $statusValue = 'payment_approved';
                            } else {
                                $statusValue = 'pending';
                            }
                            
                            $statusLabels = [
                                'pending' => 'Aguardando Pagamento',
                                'payment_approved' => 'Pagamento Aprovado',
                                'preparing' => 'Em Preparação',
                                'shipped' => 'Enviado',
                                'delivered' => 'Entregue',
                                'canceled' => 'Cancelado',
                            ];
                            $statusText = $statusLabels[$statusValue] ?? ucfirst($statusValue);
                            $color = $statusColors[$statusValue] ?? 'bg-gray-500';
                        @endphp
                        <span class="text-white px-2 py-1 text-sm rounded {{ $color }}">{{ $statusText }}</span>
                    </div>
                </div>
                <div class="p-4 space-y-6">
                    {{-- Cliente e Pagamento --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-bold mb-2">Dados do Cliente</h3>
                            <p>
                                <strong>Nome:</strong> {{ $order->user->name }}<br>
                                <strong>Email:</strong> {{ $order->user->email }}<br>
                                <strong>CPF:</strong> {{ $order->user->cpf ?? 'Não informado' }}<br>
                                <strong>Data:</strong> {{ $order->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div>
                            <h3 class="font-bold mb-2">Dados de Pagamento</h3>
                            <p>
                                <strong>Método:</strong> 
                                {{ [
                                    'credit_card' => 'Cartão de Crédito',
                                    'pix' => 'PIX',
                                    'boleto' => 'Boleto'
                                ][$order->payment->payment_method ?? ''] ?? 'Não informado' }}<br>
                                <strong>Status:</strong> 
                                <span class="font-semibold 
                                    @if($order->payment_status == 'pending') text-yellow-500
                                    @elseif($order->payment_status == 'approved') text-green-600
                                    @elseif($order->payment_status == 'failed') text-red-600
                                    @else text-gray-600
                                    @endif">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    {{-- Endereço + Observações --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-bold mb-2">Endereço de Entrega</h3>
                            @if($order->address)
                                <p class="text-sm leading-relaxed">
                                    {{ $order->address->street }}, {{ $order->address->number }}<br>
                                    @if($order->address->complement) {{ $order->address->complement }}<br> @endif
                                    {{ $order->address->neighborhood }}<br>
                                    {{ $order->address->city }} - {{ $order->address->state }}<br>
                                    CEP: {{ $order->address->zipcode }}
                                </p>
                            @else
                                <p class="text-sm text-gray-500">Endereço não disponível</p>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-bold mb-2">Observações</h3>
                            <p class="text-sm text-gray-700">
                                {{ $order->customer_note ?? 'Nenhuma observação' }}
                            </p>
                        </div>
                    </div>

                    {{-- Tabela de Itens --}}
                    <div>
                        <h3 class="font-bold mb-2">Itens do Pedido</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-600">
                                <thead class="bg-gray-100 text-gray-700">
                                    <tr>
                                        <th class="px-4 py-2">Produto</th>
                                        <th class="text-center px-2 py-2">Qtd</th>
                                        <th class="text-right px-4 py-2">Preço Unit.</th>
                                        <th class="text-right px-4 py-2">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr class="border-b">
                                            <td class="p-2">
                                                <div class="flex items-center space-x-3">
                                                    @if($item->vinylMaster && $item->vinylMaster->cover)
                                                        <img src="{{ asset('storage/' . $item->vinylMaster->cover) }}" class="w-12 h-12 object-cover rounded">
                                                    @else
                                                        <div class="w-12 h-12 flex items-center justify-center bg-gray-300 rounded">
                                                            <i class="fas fa-record-vinyl text-gray-700"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="font-medium">{{ $item->name }}</div>
                                                        @if($item->vinylMaster && $item->vinylMaster->artists->count())
                                                            <div class="text-xs text-gray-500">{{ $item->vinylMaster->artists->pluck('name')->join(', ') }}</div>
                                                        @endif
                                                        @if($item->sku)
                                                            <div class="text-xs text-gray-600">SKU: {{ $item->sku }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                            <td class="text-right">R$ {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="text-right text-sm font-semibold">
                                    <tr><td colspan="3" class="p-2">Subtotal:</td><td>R$ {{ number_format($order->subtotal, 2, ',', '.') }}</td></tr>
                                    <tr><td colspan="3" class="p-2">Frete:</td><td>R$ {{ number_format($order->shipping, 2, ',', '.') }}</td></tr>
                                    @if($order->discount > 0)
                                        <tr><td colspan="3" class="p-2 text-green-600">Desconto:</td><td class="text-green-600">-R$ {{ number_format($order->discount, 2, ',', '.') }}</td></tr>
                                    @endif
                                    <tr class="border-t"><td colspan="3" class="p-2">Total:</td><td>R$ {{ number_format($order->total, 2, ',', '.') }}</td></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Coluna de Ações + Histórico --}}
        <div class="space-y-4">
            {{-- Ações --}}
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-bold mb-3">Ações</h3>

                @include('admin.orders.partials.shipping-label-section', ['order' => $order])

                {{-- Formulário de status --}}
                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="space-y-3 mt-4">
                    @csrf @method('PUT')
                    <select name="status" class="w-full rounded border-gray-300">
                        <option value="pending" @selected($order->status == 'pending')>Pendente</option>
                        <option value="payment_approved" @selected($order->status == 'payment_approved')>Pagamento Aprovado</option>
                        <option value="delivered" @selected($order->status == 'delivered')>Entregue</option>
                        <option value="canceled" @selected($order->status == 'canceled')>Cancelado</option>
                    </select>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Atualizar Status</button>
                </form>

                <hr class="my-4">

                <a href="{{ route('admin.orders.index') }}" class="w-full block text-center border border-gray-300 py-2 rounded hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar para Lista
                </a>
            </div>

            {{-- Histórico --}}
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-bold mb-3">Histórico do Pedido</h3>
                <ul class="space-y-2">
                    <li class="text-sm text-gray-600 flex justify-between">
                        <span><i class="fas fa-shopping-cart text-primary mr-2"></i> Pedido criado</span>
                        <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </li>
                    @if($order->updated_at->gt($order->created_at))
                    <li class="text-sm text-gray-600 flex justify-between">
                        <span><i class="fas fa-edit text-info mr-2"></i> Status atualizado</span>
                        <span>{{ $order->updated_at->format('d/m/Y H:i') }}</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
</x-admin-layout>
