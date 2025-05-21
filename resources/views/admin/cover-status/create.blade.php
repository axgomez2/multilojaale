<x-admin-layout title="Criar Novo Status de Capa">
    <div class="px-6 py-4">
        <!-- Cabeçalho -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Novo Status de Capa</h1>
            <a href="{{ route('admin.cover-status.index') }}"
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
            <form action="{{ route('admin.cover-status.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">
                        Nome do Status <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500"
                           value="{{ old('title') }}" required>
                    <p class="mt-1 text-sm text-gray-500">Ex: Excelente, Muito Bom, Bom, Regular, etc.</p>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea id="description" name="description" rows="3"
                              class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('description') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Descreva o que este status significa</p>
                </div>

                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400">
                    <i class="fas fa-save mr-2"></i> Salvar Status
                </button>
            </form>
        </div>
    </div>
</x-admin-layout>
