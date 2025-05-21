<x-admin-layout title="Editar Status de Mídia">
    <div class="px-6 py-4">
        <!-- Cabeçalho -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">
                Editar Status: {{ $midiaStatus->title }}
            </h1>
            <a href="{{ route('admin.midia-status.index') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-500 rounded-lg shadow hover:bg-gray-600">
                <i class="fas fa-arrow-left fa-sm mr-2 text-white"></i> Voltar
            </a>
        </div>

        <!-- Mensagens de erro -->
        @if ($errors->any())
            <div class="mb-4 p-4 text-sm text-red-800 bg-red-100 border border-red-200 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulário -->
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.midia-status.update', $midiaStatus->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">
                        Título do Status <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('title', $midiaStatus->title) }}" required>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea id="description" name="description" rows="3"
                              class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $midiaStatus->description) }}</textarea>
                </div>

                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <i class="fas fa-save mr-2"></i> Atualizar Status
                </button>
            </form>
        </div>
    </div>
</x-admin-layout>
