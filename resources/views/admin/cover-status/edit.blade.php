<x-admin-layout title="Editar Status de Capa">
    <div class="px-6 py-4">
        <!-- Cabeçalho -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Editar Status: {{ $coverStatus->name }}</h1>
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
            <form action="{{ route('admin.cover-status.update', $coverStatus->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nome do Status <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500"
                           value="{{ old('name', $coverStatus->name) }}" required>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea id="description" name="description" rows="3"
                              class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('description', $coverStatus->description) }}</textarea>
                </div>

                <div class="mb-6">
                    <label for="color_code" class="block text-sm font-medium text-gray-700">
                        Cor (Código Hexadecimal)
                    </label>
                    <input type="color" id="color_code" name="color_code"
                           value="{{ old('color_code', $coverStatus->color_code ?? '#27ae60') }}"
                           class="mt-1 h-10 w-16 p-0 border-none cursor-pointer rounded">
                </div>

                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <i class="fas fa-save mr-2"></i> Atualizar Status
                </button>
            </form>
        </div>
    </div>
</x-admin-layout>
