<x-admin-layout title="Gerenciar Status de Mídia">
    <div class="px-6 py-4">
        <!-- Cabeçalho -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Status de Mídia</h1>
            <a href="{{ route('admin.midia-status.create') }}"
   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">

                <i class="fas fa-plus fa-sm mr-2 text-white"></i> Novo Status
            </a>
        </div>

        <!-- Mensagens de alerta -->
        @if(session('success'))
            <div class="mb-4 p-4 text-sm text-green-800 bg-green-100 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 text-sm text-red-800 bg-red-100 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabela de Status -->
        <div class="bg-white rounded-lg shadow">
            <div class="border-b px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-700">Todos os Status de Mídia</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 text-xs font-semibold text-gray-600 uppercase">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Título</th>
                            <th class="px-6 py-3">Descrição</th>
                            <th class="px-6 py-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($statuses as $status)
                            <tr>
                                <td class="px-6 py-4">{{ $status->id }}</td>
                                <td class="px-6 py-4">{{ $status->title }}</td>
                                <td class="px-6 py-4">{{ $status->description }}</td>
                                <td class="px-6 py-4 flex flex-wrap gap-2">
                                    <a href="{{ route('admin.midia-status.edit', $status->id) }}"
                                       class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-blue-500 rounded hover:bg-blue-600">
                                        <i class="fas fa-edit mr-1"></i> Editar
                                    </a>
                                    <form action="{{ route('admin.midia-status.destroy', $status->id) }}" method="POST"
                                          onsubmit="return confirm('Tem certeza que deseja remover este status?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-red-500 rounded hover:bg-red-600">
                                            <i class="fas fa-trash mr-1"></i> Excluir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum status de mídia encontrado</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="px-6 py-4 border-t">
                <div class="flex justify-center">
                    {{ $statuses->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
