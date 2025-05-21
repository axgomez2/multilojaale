<x-admin-layout title="Gerenciar Fornecedores">
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <!-- Cabeçalho -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Fornecedores</h1>
        <a href="{{ route('admin.suppliers.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow">
            <i class="fas fa-plus mr-2"></i> Novo Fornecedor
        </a>
    </div>

    <!-- Alertas -->
    <div class="space-y-4" x-data="{ showSuccess: true, showError: true }">
        @if(session('success'))
        <div x-show="showSuccess" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
            <span @click="showSuccess = false" class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer">
                <svg class="fill-current h-6 w-6 text-green-500" viewBox="0 0 20 20">
                    <path d="M14.348 5.652a1 1 0 010 1.414L11.414 10l2.934 2.934a1 1 0 11-1.414 1.414L10 11.414l-2.934 2.934a1 1 0 11-1.414-1.414L8.586 10 5.652 7.066a1 1 0 011.414-1.414L10 8.586l2.934-2.934a1 1 0 011.414 0z"/>
                </svg>
            </span>
        </div>
        @endif

        @if(session('error'))
        <div x-show="showError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
            <span @click="showError = false" class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer">
                <svg class="fill-current h-6 w-6 text-red-500" viewBox="0 0 20 20">
                    <path d="M14.348 5.652a1 1 0 010 1.414L11.414 10l2.934 2.934a1 1 0 11-1.414 1.414L10 11.414l-2.934 2.934a1 1 0 11-1.414-1.414L8.586 10 5.652 7.066a1 1 0 011.414-1.414L10 8.586l2.934-2.934a1 1 0 011.414 0z"/>
                </svg>
            </span>
        </div>
        @endif
    </div>

    <!-- Filtro -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h2 class="text-lg font-medium text-gray-700 mb-4">Filtrar Fornecedores</h2>
        <form action="{{ route('admin.suppliers.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                <input type="text" name="name" id="name" value="{{ request('name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="text" name="email" id="email" value="{{ request('email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Telefone</label>
                <input type="text" name="phone" id="phone" value="{{ request('phone') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <div class="col-span-1 md:col-span-3 flex items-center gap-3 mt-4">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                    <i class="fas fa-search mr-2"></i> Filtrar
                </button>
                <a href="{{ route('admin.suppliers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-medium rounded-lg">
                    <i class="fas fa-undo mr-2"></i> Limpar Filtros
                </a>
            </div>
        </form>
    </div>

    <!-- Tabela -->
    <div class="bg-white shadow rounded-lg mt-6 p-6">
        <h2 class="text-lg font-medium text-gray-700 mb-4">Todos os Fornecedores</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Nome</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Telefone</th>
                        <th class="px-6 py-3">Endereço</th>
                        <th class="px-6 py-3">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($suppliers as $supplier)
                    <tr>
                        <td class="px-6 py-4">{{ $supplier->id }}</td>
                        <td class="px-6 py-4">{{ $supplier->name }}</td>
                        <td class="px-6 py-4">{{ $supplier->email }}</td>
                        <td class="px-6 py-4">{{ $supplier->phone }}</td>
                        <td class="px-6 py-4">{{ $supplier->address }}</td>
                        <td class="px-6 py-4 flex gap-2">
                            <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1 rounded text-xs">
                                <i class="fas fa-edit mr-1"></i> Editar
                            </a>
                            <form x-data @submit.prevent="if (confirm('Tem certeza que deseja remover este fornecedor?')) $el.submit()" action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-xs">
                                    <i class="fas fa-trash mr-1"></i> Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhum fornecedor encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div class="mt-6">
            {{ $suppliers->appends(request()->query())->links() }}
        </div>
    </div>
</div>
</x-admin-layout>
