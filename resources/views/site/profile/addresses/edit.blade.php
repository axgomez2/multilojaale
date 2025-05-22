<x-app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('site.profile.addresses.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Voltar para Meus Endereços
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Editar Endereço</h1>
                <p class="text-gray-600">Atualize as informações do endereço</p>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                <form action="{{ route('site.profile.addresses.update', $address->id) }}" method="POST" id="addressForm">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nome do Endereço -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Endereço</label>
                            <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('name', $address->name) }}" placeholder="Ex: Minha Casa, Trabalho" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Destinatário -->
                        <div class="md:col-span-2">
                            <label for="recipient" class="block text-sm font-medium text-gray-700 mb-1">Nome do Destinatário</label>
                            <input type="text" name="recipient" id="recipient" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('recipient', $address->recipient) }}" placeholder="Nome de quem receberá o pacote" required>
                            @error('recipient')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo de Endereço -->
                        <div class="md:col-span-2">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Endereço</label>
                            <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="residential" {{ old('type', $address->type) == 'residential' ? 'selected' : '' }}>Residencial</option>
                                <option value="commercial" {{ old('type', $address->type) == 'commercial' ? 'selected' : '' }}>Comercial</option>
                                <option value="other" {{ old('type', $address->type) == 'other' ? 'selected' : '' }}>Outro</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- CEP -->
                        <div class="md:col-span-1">
                            <label for="zipcode" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" name="zipcode" id="zipcode" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('zipcode', $address->zipcode) }}" placeholder="00000-000" required>
                                <button type="button" id="searchZipcode" class="ml-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                                    Buscar
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500" id="zipcode-status"></p>
                            @error('zipcode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estado -->
                        <div class="md:col-span-1">
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-1">Estado (UF)</label>
                            <input type="text" name="state" id="state" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('state', $address->state) }}" maxlength="2" required>
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cidade -->
                        <div class="md:col-span-1">
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                            <input type="text" name="city" id="city" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('city', $address->city) }}" required>
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bairro -->
                        <div class="md:col-span-1">
                            <label for="district" class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                            <input type="text" name="district" id="district" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('district', $address->district) }}" required>
                            @error('district')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Logradouro -->
                        <div class="md:col-span-2">
                            <label for="street" class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                            <input type="text" name="street" id="street" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('street', $address->street) }}" placeholder="Rua, Avenida, etc." required>
                            @error('street')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Número -->
                        <div class="md:col-span-1">
                            <label for="number" class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                            <input type="text" name="number" id="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('number', $address->number) }}" required>
                            @error('number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Complemento -->
                        <div class="md:col-span-1">
                            <label for="complement" class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                            <input type="text" name="complement" id="complement" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('complement', $address->complement) }}" placeholder="Apto, Bloco, etc.">
                            @error('complement')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ponto de Referência -->
                        <div class="md:col-span-2">
                            <label for="reference" class="block text-sm font-medium text-gray-700 mb-1">Ponto de Referência</label>
                            <input type="text" name="reference" id="reference" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('reference', $address->reference) }}" placeholder="Perto de...">
                            @error('reference')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Telefone -->
                        <div class="md:col-span-1">
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone para Contato</label>
                            <input type="text" name="phone" id="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('phone', $address->phone) }}" placeholder="(00) 00000-0000">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Endereço Padrão -->
                        <div class="md:col-span-2 mt-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_default" name="is_default" type="checkbox" value="1" {{ old('is_default', $address->is_default) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_default" class="font-medium text-gray-700">Definir como endereço padrão</label>
                                    <p class="text-gray-500">Este será o endereço utilizado como padrão para entregas.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('site.profile.addresses.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 mr-3">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Atualizar Endereço
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Máscara para CEP
            const zipcodeInput = document.getElementById('zipcode');
            if (zipcodeInput) {
                zipcodeInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 8) {
                        value = value.substring(0, 8);
                    }
                    
                    if (value.length > 5) {
                        value = value.replace(/^(\d{5})(\d{0,3}).*/, '$1-$2');
                    }
                    
                    e.target.value = value;
                });
            }
            
            // Máscara para telefone
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 11) {
                        value = value.substring(0, 11);
                    }
                    
                    if (value.length > 10) {
                        value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                    } else if (value.length > 6) {
                        value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
                    } else if (value.length > 2) {
                        value = value.replace(/^(\d{2})(\d{0,5}).*/, '($1) $2');
                    }
                    
                    e.target.value = value;
                });
            }
            
            // Busca de CEP
            const searchZipcodeButton = document.getElementById('searchZipcode');
            const statusElement = document.getElementById('zipcode-status');
            
            if (searchZipcodeButton) {
                searchZipcodeButton.addEventListener('click', function() {
                    const zipcode = zipcodeInput.value.replace(/\D/g, '');
                    
                    if (zipcode.length !== 8) {
                        statusElement.textContent = 'CEP inválido. Informe um CEP com 8 dígitos.';
                        statusElement.className = 'mt-1 text-xs text-red-600';
                        return;
                    }
                    
                    statusElement.textContent = 'Buscando CEP...';
                    statusElement.className = 'mt-1 text-xs text-gray-600';
                    
                    fetch(`/perfil/consulta-cep`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ zipcode: zipcode })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('CEP não encontrado');
                        }
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('state').value = data.state;
                        document.getElementById('city').value = data.city;
                        document.getElementById('district').value = data.district || '';
                        document.getElementById('street').value = data.street || '';
                        
                        statusElement.textContent = 'CEP encontrado! Endereço preenchido automaticamente.';
                        statusElement.className = 'mt-1 text-xs text-green-600';
                    })
                    .catch(error => {
                        statusElement.textContent = 'CEP não encontrado. Verifique o número informado.';
                        statusElement.className = 'mt-1 text-xs text-red-600';
                        console.error('Erro ao buscar CEP:', error);
                    });
                });
            }
        });
    </script>
</x-app-layout>
