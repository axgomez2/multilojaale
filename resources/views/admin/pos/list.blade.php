<x-admin-layout title="Histórico de Vendas">
    <div class="p-4">
        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="p-6 bg-white rounded-lg shadow-md mb-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">Histórico de Vendas</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.pos.index') }}" class="px-4 py-2 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        PDV
                    </a>
                    <a href="{{ route('admin.pos.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nova Venda
                    </a>
                </div>
            </div>

            <!-- Lista de vendas -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nota</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Itens</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pagamento</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sales as $sale)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $sale->invoice_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($sale->user)
                                    {{ $sale->user->name }}
                                @else
                                    {{ $sale->customer_name }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $sale->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                R$ {{ number_format($sale->total, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $sale->items->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @switch($sale->payment_method)
                                    @case('money')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Dinheiro</span>
                                        @break
                                    @case('credit')
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Crédito</span>
                                        @break
                                    @case('debit')
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Débito</span>
                                        @break
                                    @case('pix')
                                        <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">PIX</span>
                                        @break
                                    @case('transfer')
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Transferência</span>
                                        @break
                                    @default
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">{{ $sale->payment_method }}</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <a href="{{ route('admin.pos.show', $sale) }}" class="text-blue-600 hover:text-blue-900">Detalhes</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhuma venda registrada.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="mt-4">
                {{ $sales->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
