<x-admin-layout title="Gerenciar Pedidos">
<div class="px-4">
    <h1 class="text-2xl font-semibold mt-4 text-white">Gerenciar Pedidos</h1>
    <nav class="text-sm text-gray-300 mb-4">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:underline text-yellow-400">Dashboard</a></li>
            <li>/</li>
            <li class="text-white">Pedidos</li>
        </ol>
    </nav>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 mb-4">
        <x-admin.order-stat-card color="blue" icon="shopping-cart" label="Total de Pedidos" :value="$counters['total'] ?? 0"/>
        <x-admin.order-stat-card color="yellow" icon="clock" label="Pendentes" :value="$counters['pending'] ?? 0"/>
        <x-admin.order-stat-card color="green" icon="check-circle" label="Aprovados" :value="$counters['payment_approved'] ?? 0"/>
        <x-admin.order-stat-card color="blue" icon="truck-loading" label="Entregues" :value="$counters['delivered'] ?? 0"/>
        <x-admin.order-stat-card color="red" icon="times-circle" label="Cancelados" :value="$counters['canceled'] ?? 0"/>
    </div>

    <!-- Tabela de Pedidos -->
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow mb-4">
        <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <div class="flex items-center text-gray-900 dark:text-white">
                <i class="fas fa-table mr-2"></i> Pedidos
            </div>
            <div class="flex gap-2">
                <button @click="filterModal = true" class="btn btn-outline-secondary btn-sm text-gray-700 border border-gray-300 px-3 py-1 rounded hover:bg-gray-100 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <i class="fas fa-filter mr-1"></i> Filtrar
                </button>
                @if(request()->has('status') || request()->has('date') || request()->has('search'))
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-danger btn-sm text-red-500 border border-red-300 px-3 py-1 rounded hover:bg-red-100">
                    <i class="fas fa-times mr-1"></i> Limpar Filtros
                </a>
                @endif
            </div>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="px-4 py-2">Pedido #</th>
                        <th class="px-4 py-2">Cliente</th>
                        <th class="px-4 py-2">Data</th>
                        <th class="px-4 py-2 text-right">Total</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Pagamento</th>
                        <th class="px-4 py-2">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr class="border-b dark:border-gray-700">
                        <td class="px-4 py-2">{{ $order->order_number }}</td>
                        <td class="px-4 py-2">
                            <div>{{ $order->user->name }}</div>
                            <div class="text-xs text-gray-400">{{ $order->user->email }}</div>
                        </td>
                        <td class="px-4 py-2">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2 text-right">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                        <td class="px-4 py-2">
                            <x-admin.badge :status="$order->status" />
                        </td>
                        <td class="px-4 py-2">
                            <x-admin.payment-badge :payment="$order->payment" />
                        </td>
                        <td class="px-4 py-2 flex gap-2">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800" title="Ver detalhes">
                                    <i class="fas fa-eye mr-1"></i> Ver
                                </a>

                                @if($order->shipping_label_url)
                                    <!-- Pedido já tem etiqueta gerada -->
                                    <a href="{{ $order->shipping_label_url }}" target="_blank" class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800" title="Ver etiqueta">
                                        <i class="fas fa-tag mr-1"></i> Etiqueta
                                    </a>
                                    <a href="{{ route('admin.orders.shipping-label', $order->id) }}" class="text-purple-700 hover:text-white border border-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center dark:border-purple-400 dark:text-purple-400 dark:hover:text-white dark:hover:bg-purple-500 dark:focus:ring-purple-800" title="Regenerar etiqueta">
                                        <i class="fas fa-sync-alt mr-1"></i> Regenerar
                                    </a>
                                @elseif($order->status == 'payment_approved' || $order->status == 'preparing' || $order->status == 'shipped' || $order->payment_status == 'approved')
                                    <!-- Pedido elegível para gerar etiqueta -->
                                    <a href="{{ route('admin.orders.shipping-label', $order->id) }}" class="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800" title="Gerar etiqueta">
                                        <i class="fas fa-shipping-fast mr-1"></i> Gerar etiqueta
                                    </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-6 text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                Nenhum pedido encontrado
                                @if(request()->has('status') || request()->has('date') || request()->has('search'))
                                <a href="{{ route('admin.orders.index') }}" class="btn mt-2 text-blue-500 hover:underline">Limpar filtros</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal de Filtro -->
<div x-data="{ filterModal: false }">
    <div x-show="filterModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Filtrar Pedidos</h2>
                <button @click="filterModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.orders.index') }}" method="GET" class="space-y-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="status" class="form-select w-full mt-1">
                        <option value="">Todos</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="payment_approved" {{ request('status') == 'payment_approved' ? 'selected' : '' }}>Pagamento Aprovado</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregue</option>
                        <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data</label>
                    <select name="date" id="date" class="form-select w-full mt-1">
                        <option value="">Qualquer data</option>
                        <option value="today" {{ request('date') == 'today' ? 'selected' : '' }}>Hoje</option>
                        <option value="week" {{ request('date') == 'week' ? 'selected' : '' }}>Esta semana</option>
                        <option value="month" {{ request('date') == 'month' ? 'selected' : '' }}>Este mês</option>
                    </select>
                </div>
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buscar</label>
                    <input type="text" name="search" id="search" class="form-input w-full mt-1" value="{{ request('search') }}" placeholder="Número, nome ou email">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="filterModal = false" class="btn px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancelar</button>
                    <button type="submit" class="btn px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Aplicar</button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-admin-layout>
