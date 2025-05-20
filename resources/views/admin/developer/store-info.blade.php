<x-layouts.admin title="Informações da Loja">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4 text-zinc-900 dark:text-zinc-100">Informações da Loja</h1>
            <p class="mb-6 text-zinc-700 dark:text-zinc-300">Configure as informações básicas da sua loja que serão exibidas no site.</p>
            
            @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
            @endif
            
            <form action="{{ route('admin.developer.store.update') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                    <!-- Nome da Loja -->
                    <div class="md:col-span-3">
                        <label for="store_name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Nome da Loja *
                        </label>
                        <input 
                            type="text" 
                            name="store_name" 
                            id="store_name" 
                            value="{{ old('store_name', $settings['store_name']) }}" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                            required
                        >
                        @error('store_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Tipo de Documento -->
                    <div class="md:col-span-1">
                        <label for="store_document_type" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Tipo
                        </label>
                        <select 
                            name="store_document_type" 
                            id="store_document_type" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >
                            <option value="cnpj" {{ old('store_document_type', $settings['store_document_type']) == 'cnpj' ? 'selected' : '' }}>CNPJ</option>
                            <option value="cpf" {{ old('store_document_type', $settings['store_document_type']) == 'cpf' ? 'selected' : '' }}>CPF</option>
                        </select>
                    </div>
                    
                    <!-- Documento (CNPJ/CPF) -->
                    <div class="md:col-span-2">
                        <label for="store_document" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Documento (CNPJ/CPF)
                        </label>
                        <input 
                            type="text" 
                            name="store_document" 
                            id="store_document" 
                            value="{{ old('store_document', $settings['store_document']) }}" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >
                        @error('store_document')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Descrição -->
                    <div class="md:col-span-6">
                        <label for="store_description" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Descrição da Loja
                        </label>
                        <textarea 
                            name="store_description" 
                            id="store_description" 
                            rows="3" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >{{ old('store_description', $settings['store_description']) }}</textarea>
                        @error('store_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- CEP -->
                    <div class="md:col-span-2">
                        <label for="store_zipcode" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            CEP
                        </label>
                        <div class="flex">
                            <input 
                                type="text" 
                                name="store_zipcode" 
                                id="store_zipcode" 
                                value="{{ old('store_zipcode', $settings['store_zipcode']) }}" 
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
                        <label for="store_address" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Endereço
                        </label>
                        <input 
                            type="text" 
                            name="store_address" 
                            id="store_address" 
                            value="{{ old('store_address', $settings['store_address']) }}" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >
                        @error('store_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Bairro -->
                    <div class="md:col-span-3">
                        <label for="store_neighborhood" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Bairro
                        </label>
                        <input 
                            type="text" 
                            name="store_neighborhood" 
                            id="store_neighborhood" 
                            value="{{ old('store_neighborhood', $settings['store_neighborhood']) }}" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >
                        @error('store_neighborhood')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- UF -->
                    <div class="md:col-span-1">
                        <label for="store_state" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            UF
                        </label>
                        <select 
                            name="store_state" 
                            id="store_state" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >
                            <option value="">Selecione...</option>
                            <option value="AC" {{ old('store_state', $settings['store_state']) == 'AC' ? 'selected' : '' }}>AC</option>
                            <option value="AL" {{ old('store_state', $settings['store_state']) == 'AL' ? 'selected' : '' }}>AL</option>
                            <option value="AP" {{ old('store_state', $settings['store_state']) == 'AP' ? 'selected' : '' }}>AP</option>
                            <option value="AM" {{ old('store_state', $settings['store_state']) == 'AM' ? 'selected' : '' }}>AM</option>
                            <option value="BA" {{ old('store_state', $settings['store_state']) == 'BA' ? 'selected' : '' }}>BA</option>
                            <option value="CE" {{ old('store_state', $settings['store_state']) == 'CE' ? 'selected' : '' }}>CE</option>
                            <option value="DF" {{ old('store_state', $settings['store_state']) == 'DF' ? 'selected' : '' }}>DF</option>
                            <option value="ES" {{ old('store_state', $settings['store_state']) == 'ES' ? 'selected' : '' }}>ES</option>
                            <option value="GO" {{ old('store_state', $settings['store_state']) == 'GO' ? 'selected' : '' }}>GO</option>
                            <option value="MA" {{ old('store_state', $settings['store_state']) == 'MA' ? 'selected' : '' }}>MA</option>
                            <option value="MT" {{ old('store_state', $settings['store_state']) == 'MT' ? 'selected' : '' }}>MT</option>
                            <option value="MS" {{ old('store_state', $settings['store_state']) == 'MS' ? 'selected' : '' }}>MS</option>
                            <option value="MG" {{ old('store_state', $settings['store_state']) == 'MG' ? 'selected' : '' }}>MG</option>
                            <option value="PA" {{ old('store_state', $settings['store_state']) == 'PA' ? 'selected' : '' }}>PA</option>
                            <option value="PB" {{ old('store_state', $settings['store_state']) == 'PB' ? 'selected' : '' }}>PB</option>
                            <option value="PR" {{ old('store_state', $settings['store_state']) == 'PR' ? 'selected' : '' }}>PR</option>
                            <option value="PE" {{ old('store_state', $settings['store_state']) == 'PE' ? 'selected' : '' }}>PE</option>
                            <option value="PI" {{ old('store_state', $settings['store_state']) == 'PI' ? 'selected' : '' }}>PI</option>
                            <option value="RJ" {{ old('store_state', $settings['store_state']) == 'RJ' ? 'selected' : '' }}>RJ</option>
                            <option value="RN" {{ old('store_state', $settings['store_state']) == 'RN' ? 'selected' : '' }}>RN</option>
                            <option value="RS" {{ old('store_state', $settings['store_state']) == 'RS' ? 'selected' : '' }}>RS</option>
                            <option value="RO" {{ old('store_state', $settings['store_state']) == 'RO' ? 'selected' : '' }}>RO</option>
                            <option value="RR" {{ old('store_state', $settings['store_state']) == 'RR' ? 'selected' : '' }}>RR</option>
                            <option value="SC" {{ old('store_state', $settings['store_state']) == 'SC' ? 'selected' : '' }}>SC</option>
                            <option value="SP" {{ old('store_state', $settings['store_state']) == 'SP' ? 'selected' : '' }}>SP</option>
                            <option value="SE" {{ old('store_state', $settings['store_state']) == 'SE' ? 'selected' : '' }}>SE</option>
                            <option value="TO" {{ old('store_state', $settings['store_state']) == 'TO' ? 'selected' : '' }}>TO</option>
                        </select>
                        @error('store_state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Telefone -->
                    <div class="md:col-span-2">
                        <label for="store_phone" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Telefone
                        </label>
                        <input 
                            type="text" 
                            name="store_phone" 
                            id="store_phone" 
                            value="{{ old('store_phone', $settings['store_phone']) }}" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >
                        @error('store_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div class="md:col-span-3">
                        <label for="store_email" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Email de Contato
                        </label>
                        <input 
                            type="email" 
                            name="store_email" 
                            id="store_email" 
                            value="{{ old('store_email', $settings['store_email']) }}" 
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900"
                        >
                        @error('store_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
</x-layouts.admin>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Função para formatar o CEP (adiciona hífen)
        const cepInput = document.getElementById('store_zipcode');
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            e.target.value = value;
        });
        
        // Formatar documento conforme tipo (CPF ou CNPJ)
        const documentInput = document.getElementById('store_document');
        const documentTypeSelect = document.getElementById('store_document_type');
        
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
                    document.getElementById('store_address').value = data.logradouro;
                    document.getElementById('store_neighborhood').value = data.bairro;
                    document.getElementById('store_state').value = data.uf;
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
