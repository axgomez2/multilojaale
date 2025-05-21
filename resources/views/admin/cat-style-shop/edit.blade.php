<x-admin-layout title="Editar Categoria de Disco">
    <div class="px-6 py-4">
        <!-- Cabeçalho -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">
                Editar Categoria: {{ $catStyleShop->nome }}
            </h1>
            <a href="{{ route('admin.cat-style-shop.index') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-500 rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2 text-white"></i> Voltar
            </a>
        </div>

        <!-- Mensagens de erro -->
        @if ($errors->any())
            <div class="mb-4 p-4 text-sm text-red-800 bg-red-100 rounded-lg">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulário -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Dados da Categoria</h2>

            <form action="{{ route('admin.cat-style-shop.update', $catStyleShop->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="nome" class="block mb-1 text-sm font-medium text-gray-700">Nome da Categoria <span class="text-red-500">*</span></label>
                    <input type="text" id="nome" name="nome"
                           class="w-full px-4 py-2 text-sm border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                           value="{{ old('nome', $catStyleShop->nome) }}" required>
                    <p class="mt-1 text-xs text-gray-500">Ex: Rock, Jazz, Soul, Nacional, etc.</p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">
                        Use categorias para organizar seus discos em diferentes seções como DJs, Colecionadores, Lotes, etc.
                    </p>
                </div>

                <button type="submit"
                        class="inline-flex items-center px-5 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                    <i class="fas fa-save mr-2"></i> Atualizar Categoria
                </button>
            </form>
        </div>
    </div>
</x-admin-layout>
