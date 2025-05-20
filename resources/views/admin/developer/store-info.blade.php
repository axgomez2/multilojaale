<x-admin-layout title="Informações da Loja">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4 text-zinc-900 dark:text-zinc-100">Informações da Loja</h1>
            <p class="mb-6 text-zinc-700 dark:text-zinc-300">Configure as informações básicas da sua loja que serão exibidas no site.</p>
            
            @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
            @endif
            
            <form action="{{ route('admin.developer.store.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                    <!-- Nome da Loja -->
                    <div class="md:col-span-3">
                        <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Nome da Loja *
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $store->name) }}" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                            required
                        >
                        @error('store_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Tipo de Documento -->
                    <div class="md:col-span-1">
                        <label for="document_type" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Tipo
                        </label>
                        <select 
                            name="document_type" 
                            id="document_type" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >
                            <option value="cnpj" {{ old('document_type', $store->document_type) == 'cnpj' ? 'selected' : '' }}>CNPJ</option>
                            <option value="cpf" {{ old('document_type', $store->document_type) == 'cpf' ? 'selected' : '' }}>CPF</option>
                        </select>
                    </div>
                    
                    <!-- Documento (CNPJ/CPF) -->
                    <div class="md:col-span-2">
                        <label for="document" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Documento (CNPJ/CPF)
                        </label>
                        <input 
                            type="text" 
                            name="document" 
                            id="document" 
                            value="{{ old('document', $store->document) }}" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >
                        @error('store_document')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Descrição -->
                    <div class="md:col-span-6">
                        <label for="description" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Descrição da Loja
                        </label>
                        <textarea 
                            name="description" 
                            id="description" 
                            rows="3" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >{{ old('description', $store->description) }}</textarea>
                        @error('store_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- CEP -->
                    <div class="md:col-span-2">
                        <label for="zipcode" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            CEP
                        </label>
                        <div class="flex">
                            <input 
                                type="text" 
                                name="zipcode" 
                                id="zipcode" 
                                value="{{ old('zipcode', $store->zipcode) }}" 
                                class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                                maxlength="9"
                            >
                            <button 
                                type="button" 
                                id="search_cep" 
                                class="ml-2 px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-700 dark:text-zinc-300 rounded-md"
                            >
                                Buscar
                            </button>
                        </div>
                        @error('store_zipcode')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Endereço -->
                    <div class="md:col-span-4">
                        <label for="address" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Endereço
                        </label>
                        <input 
                            type="text" 
                            name="address" 
                            id="address" 
                            value="{{ old('address', $store->address) }}" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >
                        @error('store_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Bairro -->
                    <div class="md:col-span-3">
                        <label for="neighborhood" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Bairro
                        </label>
                        <input 
                            type="text" 
                            name="neighborhood" 
                            id="neighborhood" 
                            value="{{ old('neighborhood', $store->neighborhood) }}" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >
                        @error('store_neighborhood')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- UF -->
                    <div class="md:col-span-1">
                        <label for="state" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            UF
                        </label>
                        <select 
                            name="state" 
                            id="state" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >
                            <option value="">Selecione...</option>
                            <option value="AC" {{ old('state', $store->state) == 'AC' ? 'selected' : '' }}>AC</option>
                            <option value="AL" {{ old('state', $store->state) == 'AL' ? 'selected' : '' }}>AL</option>
                            <option value="AP" {{ old('state', $store->state) == 'AP' ? 'selected' : '' }}>AP</option>
                            <option value="AM" {{ old('state', $store->state) == 'AM' ? 'selected' : '' }}>AM</option>
                            <option value="BA" {{ old('state', $store->state) == 'BA' ? 'selected' : '' }}>BA</option>
                            <option value="CE" {{ old('state', $store->state) == 'CE' ? 'selected' : '' }}>CE</option>
                            <option value="DF" {{ old('state', $store->state) == 'DF' ? 'selected' : '' }}>DF</option>
                            <option value="ES" {{ old('state', $store->state) == 'ES' ? 'selected' : '' }}>ES</option>
                            <option value="GO" {{ old('state', $store->state) == 'GO' ? 'selected' : '' }}>GO</option>
                            <option value="MA" {{ old('state', $store->state) == 'MA' ? 'selected' : '' }}>MA</option>
                            <option value="MT" {{ old('state', $store->state) == 'MT' ? 'selected' : '' }}>MT</option>
                            <option value="MS" {{ old('state', $store->state) == 'MS' ? 'selected' : '' }}>MS</option>
                            <option value="MG" {{ old('state', $store->state) == 'MG' ? 'selected' : '' }}>MG</option>
                            <option value="PA" {{ old('state', $store->state) == 'PA' ? 'selected' : '' }}>PA</option>
                            <option value="PB" {{ old('state', $store->state) == 'PB' ? 'selected' : '' }}>PB</option>
                            <option value="PR" {{ old('state', $store->state) == 'PR' ? 'selected' : '' }}>PR</option>
                            <option value="PE" {{ old('state', $store->state) == 'PE' ? 'selected' : '' }}>PE</option>
                            <option value="PI" {{ old('state', $store->state) == 'PI' ? 'selected' : '' }}>PI</option>
                            <option value="RJ" {{ old('state', $store->state) == 'RJ' ? 'selected' : '' }}>RJ</option>
                            <option value="RN" {{ old('state', $store->state) == 'RN' ? 'selected' : '' }}>RN</option>
                            <option value="RS" {{ old('state', $store->state) == 'RS' ? 'selected' : '' }}>RS</option>
                            <option value="RO" {{ old('state', $store->state) == 'RO' ? 'selected' : '' }}>RO</option>
                            <option value="RR" {{ old('state', $store->state) == 'RR' ? 'selected' : '' }}>RR</option>
                            <option value="SC" {{ old('state', $store->state) == 'SC' ? 'selected' : '' }}>SC</option>
                            <option value="SP" {{ old('state', $store->state) == 'SP' ? 'selected' : '' }}>SP</option>
                            <option value="SE" {{ old('state', $store->state) == 'SE' ? 'selected' : '' }}>SE</option>
                            <option value="TO" {{ old('state', $store->state) == 'TO' ? 'selected' : '' }}>TO</option>
                        </select>
                        @error('store_state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Telefone -->
                    <div class="md:col-span-2">
                        <label for="phone" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Telefone
                        </label>
                        <input 
                            type="text" 
                            name="phone" 
                            id="phone" 
                            value="{{ old('phone', $store->phone) }}" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >
                        @error('store_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div class="md:col-span-3">
                        <label for="email" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Email de Contato
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email', $store->email) }}" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >
                        @error('store_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Logo da Loja -->
                    <div class="md:col-span-3">
                        <label for="logo" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Logo da Loja
                        </label>
                        
                        <div class="mb-3">
                            @if($store->logo_path)
                                <div class="mb-2 p-2 border border-zinc-200 dark:border-zinc-700 rounded-lg inline-block">
                                    <img src="{{ $store->logo_url }}" alt="Logo atual" class="max-h-24">
                                </div>
                            @endif
                            
                            <input 
                                type="file" 
                                name="logo" 
                                id="logo" 
                                accept="image/*"
                                class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                            >
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                Recomendado: PNG ou SVG, tamanho máximo de 2MB. Será redimensionado automaticamente.
                            </p>
                        </div>
                    </div>
                    
                    <!-- Favicon -->
                    <div class="md:col-span-3">
                        <label for="favicon" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Favicon
                        </label>
                        
                        <div class="mb-3">
                            @if($store->favicon_path)
                                <div class="mb-2 p-2 border border-zinc-200 dark:border-zinc-700 rounded-lg inline-block">
                                    <img src="{{ $store->favicon_url }}" alt="Favicon atual" class="max-h-10">
                                </div>
                            @endif
                            
                            <input 
                                type="file" 
                                name="favicon" 
                                id="favicon" 
                                accept="image/*"
                                class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                            >
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                Recomendado: ICO, PNG ou SVG, tamanho máximo de 1MB. Será redimensionado automaticamente para 64x64px.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 text-right">
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md shadow-sm">
                        Salvar Informações
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Função para formatar o CEP (adiciona hífen)
        const cepInput = document.getElementById('zipcode');
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            e.target.value = value;
        });
        
        // Formatar documento conforme tipo (CPF ou CNPJ)
        const documentInput = document.getElementById('document');
        const documentTypeSelect = document.getElementById('document_type');
        
        function formatDocument() {
            let value = documentInput.value.replace(/\D/g, '');
            if (documentTypeSelect.value === 'cpf') {
                // Formatar CPF
                if (value.length > 9) {
                    value = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + value.substring(6, 9) + '-' + value.substring(9, 11);
                } else if (value.length > 6) {
                    value = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + value.substring(6);
                } else if (value.length > 3) {
                    value = value.substring(0, 3) + '.' + value.substring(3);
                }
            } else {
                // Formatar CNPJ
                if (value.length > 12) {
                    value = value.substring(0, 2) + '.' + value.substring(2, 5) + '.' + value.substring(5, 8) + '/' + value.substring(8, 12) + '-' + value.substring(12, 14);
                } else if (value.length > 8) {
                    value = value.substring(0, 2) + '.' + value.substring(2, 5) + '.' + value.substring(5, 8) + '/' + value.substring(8);
                } else if (value.length > 5) {
                    value = value.substring(0, 2) + '.' + value.substring(2, 5) + '.' + value.substring(5);
                } else if (value.length > 2) {
                    value = value.substring(0, 2) + '.' + value.substring(2);
                }
            }
            documentInput.value = value;
        }
        
        documentInput.addEventListener('input', formatDocument);
        documentTypeSelect.addEventListener('change', function() {
            documentInput.value = documentInput.value.replace(/\D/g, '');
            formatDocument();
        });
        
        // Busca de CEP
        const searchCepButton = document.getElementById('search_cep');
        searchCepButton.addEventListener('click', function() {
            const cep = cepInput.value.replace(/\D/g, '');
            
            if (cep.length !== 8) {
                alert('Por favor, digite um CEP válido com 8 dígitos.');
                return;
            }
            
            // Mostra feedback visual de carregamento
            searchCepButton.disabled = true;
            searchCepButton.textContent = 'Buscando...';
            
            // Realiza consulta à API ViaCEP
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        alert('CEP não encontrado.');
                        return;
                    }
                    
                    // Preenche os campos com os dados retornados
                    document.getElementById('address').value = data.logradouro;
                    document.getElementById('neighborhood').value = data.bairro;
                    document.getElementById('state').value = data.uf;
                })
                .catch(error => {
                    console.error('Erro ao buscar CEP:', error);
                    alert('Erro ao buscar CEP. Tente novamente mais tarde.');
                })
                .finally(() => {
                    // Restaura o botão de busca
                    searchCepButton.disabled = false;
                    searchCepButton.textContent = 'Buscar';
                });
        });
    });
</script>
@endpush
