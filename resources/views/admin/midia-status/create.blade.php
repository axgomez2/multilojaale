<x-admin-layout title="Criar Novo Status de Mídia">
    <div class="px-6 py-4">
        <!-- Cabeçalho -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Novo Status de Mídia</h1>
            <a href="{{ route('admin.midia-status.index') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-600 rounded-lg shadow hover:bg-gray-700">
                <i class="fas fa-arrow-left fa-sm mr-2 text-white-50"></i> Voltar
            </a>
        </div>

        <!-- Mensagens de erro -->
        @if ($errors->any())
            <div class="mb-4 p-4 text-sm text-red-800 bg-red-100 rounded-lg">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulário -->
        <div class="bg-white rounded-lg shadow">
            <div class="border-b px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-700">Dados do Status</h2>
            </div>
            <div class="px-6 py-6">
                <form action="{{ route('admin.midia-status.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Nome do Status <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title"
                               value="{{ old('title') }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 text-sm">
                        <small class="text-gray-500">Ex: Excelente, Muito Bom, Regular, etc.</small>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 text-sm">{{ old('description') }}</textarea>
                        <small class="text-gray-500">Descreva o que este status significa.</small>
                    </div>

                    
                    <div>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg shadow hover:bg-primary-700">
                            <i class="fas fa-save mr-2"></i> Salvar Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
