<x-admin-layout title="Detalhes da Venda">
    <div class="p-4">
        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="p-6 bg-white rounded-lg shadow-md mb-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">Detalhes da Venda</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.pos.list') }}" class="px-4 py-2 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        Ver Lista
                    </a>
                    <a href="{{ route('admin.pos.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nova Venda
                    </a>
                </div>
            </div>

            <!-- Invoice Header -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6 border border-gray-200">
                <div class="flex justify-between flex-wrap">
                    <div class="mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Nota de Venda</h2>
                        <p class="text-lg text-gray-800 font-semibold mt-1">{{ $sale->invoice_number }}</p>
                        <p class="text-gray-600 text-sm mt-2">Data: {{ $sale->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="text-right mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Cliente</h3>
                        @if($sale->user)
                            <p class="text-gray-700">{{ $sale->user->name }}</p>
                            <p class="text-gray-600 text-sm">{{ $sale->user->email }}</p>
                        @else
                            <p class="text-gray-700">{{ $sale->customer_name }}</p>
                            <p class="text-gray-600 text-sm">Venda direta</p>
                        @endif
                    </div>
                </div>
                <div class="flex justify-between flex-wrap mt-4">
                    <div>
                        <h3 class="text-md font-semibold text-gray-800">Método de Pagamento</h3>
                        <p class="text-gray-700">
                            @switch($sale->payment_method)
                                @case('money')
                                    Dinheiro
                                    @break
                                @case('credit')
                                    Cartão de Crédito
                                    @break
                                @case('debit')
                                    Cartão de Débito
                                    @break
                                @case('pix')
                                    PIX
                                    @break
                                @case('transfer')
                                    Transferência Bancária
                                    @break
                                @default
                                    {{ $sale->payment_method }}
                            @endswitch
                        </p>
                    </div>
                    <div>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                            Venda Finalizada
                        </span>
                    </div>
                </div>
                @if($sale->notes)
                    <div class="mt-4 p-3 bg-yellow-50 rounded-md">
                        <h3 class="text-md font-semibold text-gray-800">Observações</h3>
                        <p class="text-gray-700">{{ $sale->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Items -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Itens da Venda</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disco</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço Unitário</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Desconto</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sale->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" src="{{ asset($item->vinyl->vinylMaster->cover_image ?? 'images/placeholder.jpg') }}" alt="{{ $item->vinyl->vinylMaster->title }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->vinyl->vinylMaster->title }}</div>
                                            <div class="text-sm text-gray-500">
                                                @if($item->vinyl->vinylMaster->artists)
                                                    {{ $item->vinyl->vinylMaster->artists->pluck('name')->implode(', ') }}
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $item->vinyl->catalog_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    R$ {{ number_format($item->price, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($item->item_discount > 0)
                                        R$ {{ number_format($item->item_discount, 2, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    R$ {{ number_format($item->item_total, 2, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Subtotal:</td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-bold">R$ {{ number_format($sale->subtotal, 2, ',', '.') }}</td>
                            </tr>
                            @if($sale->discount > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Desconto:</td>
                                <td class="px-6 py-4 text-sm text-red-600 font-bold">- R$ {{ number_format($sale->discount, 2, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($sale->shipping > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Frete:</td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-bold">+ R$ {{ number_format($sale->shipping, 2, ',', '.') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Total:</td>
                                <td class="px-6 py-4 text-lg text-green-600 font-bold">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 mt-6">
                <a href="#" onclick="window.print()" class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir
                </a>
                <a href="{{ route('admin.pos.list') }}" class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Voltar para Lista
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>
