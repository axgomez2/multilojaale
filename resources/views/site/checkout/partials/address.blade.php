<h2 class="text-2xl font-bold mb-6">Endereço de Entrega</h2>

<div id="address-container">
    @if (auth()->check() && count($addresses) > 0)
        <!-- Endereços salvos -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-3">Seus endereços</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($addresses as $address)
                    <div class="border rounded-lg p-4 hover:border-primary cursor-pointer {{ session('checkout_address_id') == $address->id ? 'border-primary bg-primary/5' : 'border-gray-200' }}"
                         data-address-id="{{ $address->id }}" onclick="selectAddressElement(this, '{{ $address->id }}')">
                        <div class="flex justify-between">
                            <div class="font-medium">{{ $address->name }}</div>
                            @if ($address->is_default_shipping)
                                <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded-full">Padrão Entrega</span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-600 mt-1">
                            {{ $address->street }}, {{ $address->number }}
                            @if ($address->complement)
                                - {{ $address->complement }}
                            @endif
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $address->district }}
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $address->city }} - {{ $address->state }}
                        </div>
                        <div class="text-sm text-gray-600">
                            CEP: {{ $address->zipcode }}
                        </div>
                        
                        <button type="button"
                                class="select-address-btn mt-3 w-full py-2 text-sm bg-primary text-white rounded-md hover:bg-primary-dark {{ session('checkout_address_id') == $address->id ? 'hidden' : '' }}">
                            Selecionar este endereço
                        </button>
                        <div class="mt-3 text-sm text-center text-primary font-medium {{ session('checkout_address_id') == $address->id ? '' : 'hidden' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Endereço selecionado
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Botão para adicionar novo endereço -->
    <div class="mt-4">
        <button type="button" onclick="openAddressModal()" class="w-full py-3 px-4 border border-dashed border-gray-300 rounded-lg text-center hover:border-primary hover:bg-primary/5">
            <span class="flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Adicionar novo endereço
            </span>
        </button>
    </div>
    
    <!-- Botão para avançar para o próximo passo -->
    <div class="mt-6">
        <form action="{{ route('site.checkout.next-step') }}" method="POST" id="next-step-form">
            @csrf
            <input type="hidden" name="selected_address_id" id="selected_address_id" value="{{ session('checkout_address_id') }}">
            <div id="address-selection-error" class="hidden text-red-500 text-sm mb-2 p-2 bg-red-50 rounded">Por favor, selecione um endereço para continuar.</div>
            <button type="submit" id="continue-btn" class="w-full py-3 bg-primary text-white rounded-md font-medium hover:bg-primary-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed" {{ !session('checkout_address_id') ? 'disabled' : '' }}>
                Continuar para Frete
            </button>
        </form>
    </div>
    
    @if(session('error'))
    <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
        {{ session('error') }}
    </div>
    @endif
</div>

<!-- Formulário de novo endereço (inicialmente oculto) -->
<div id="new-address-form" class="hidden mt-6">
    <h3 class="text-lg font-semibold mb-3">Novo endereço</h3>
    
    <form id="address-form" class="space-y-4">
        @csrf
        
        <!-- Nome do endereço -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nome do endereço</label>
            <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Ex: Minha casa, Trabalho">
        </div>
        
        <!-- CEP com busca automática -->
        <div>
            <label for="zipcode" class="block text-sm font-medium text-gray-700">CEP</label>
            <div class="mt-1 flex rounded-md shadow-sm">
                <input type="text" name="zipcode" id="zipcode" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="00000-000">
                <button type="button" id="search-zipcode-btn" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Buscar
                </button>
            </div>
            <p id="zipcode-error" class="mt-1 text-sm text-red-600 hidden"></p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Rua -->
            <div class="col-span-2">
                <label for="street" class="block text-sm font-medium text-gray-700">Rua</label>
                <input type="text" name="street" id="street" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>
            
            <!-- Número e Complemento -->
            <div>
                <label for="number" class="block text-sm font-medium text-gray-700">Número</label>
                <input type="text" name="number" id="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>
            
            <div>
                <label for="complement" class="block text-sm font-medium text-gray-700">Complemento</label>
                <input type="text" name="complement" id="complement" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Apto, Bloco, etc.">
            </div>
            
            <!-- Bairro -->
            <div class="col-span-2">
                <label for="district" class="block text-sm font-medium text-gray-700">Bairro</label>
                <input type="text" name="district" id="district" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>
            
            <!-- Cidade e Estado -->
            <div>
                <label for="city" class="block text-sm font-medium text-gray-700">Cidade</label>
                <input type="text" name="city" id="city" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>
            
            <div>
                <label for="state" class="block text-sm font-medium text-gray-700">Estado</label>
                <select name="state" id="state" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="">Selecione</option>
                    <option value="AC">Acre</option>
                    <option value="AL">Alagoas</option>
                    <option value="AP">Amapá</option>
                    <option value="AM">Amazonas</option>
                    <option value="BA">Bahia</option>
                    <option value="CE">Ceará</option>
                    <option value="DF">Distrito Federal</option>
                    <option value="ES">Espírito Santo</option>
                    <option value="GO">Goiás</option>
                    <option value="MA">Maranhão</option>
                    <option value="MT">Mato Grosso</option>
                    <option value="MS">Mato Grosso do Sul</option>
                    <option value="MG">Minas Gerais</option>
                    <option value="PA">Pará</option>
                    <option value="PB">Paraíba</option>
                    <option value="PR">Paraná</option>
                    <option value="PE">Pernambuco</option>
                    <option value="PI">Piauí</option>
                    <option value="RJ">Rio de Janeiro</option>
                    <option value="RN">Rio Grande do Norte</option>
                    <option value="RS">Rio Grande do Sul</option>
                    <option value="RO">Rondônia</option>
                    <option value="RR">Roraima</option>
                    <option value="SC">Santa Catarina</option>
                    <option value="SP">São Paulo</option>
                    <option value="SE">Sergipe</option>
                    <option value="TO">Tocantins</option>
                </select>
            </div>
        </div>
        
        @if (auth()->check())
        <div class="flex items-center mt-4">
            <input type="checkbox" name="is_default" id="is_default" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
            <label for="is_default" class="ml-2 block text-sm text-gray-700">
                Definir como endereço padrão
            </label>
        </div>
        @endif
        
        <div class="flex space-x-4 mt-6">
            <button type="button" id="cancel-address-btn" class="flex-1 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                Cancelar
            </button>
            <button type="submit" id="save-address-btn" class="flex-1 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                Salvar endereço
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Função global para seleção de endereço
    function selectAddressElement(element, addressId) {
        // Atualizar visualmente os elementos
        document.querySelectorAll('[data-address-id]').forEach(card => {
            card.classList.remove('border-primary', 'bg-primary/5');
            card.classList.add('border-gray-200');
            
            const selectBtn = card.querySelector('.select-address-btn');
            if (selectBtn) {
                selectBtn.classList.remove('hidden');
            }
            
            const selectedIndicator = card.querySelector('.mt-3.text-sm.text-center.text-primary');
            if (selectedIndicator) {
                selectedIndicator.classList.add('hidden');
            }
        });
        
        // Destacar o endereço selecionado
        element.classList.add('border-primary', 'bg-primary/5');
        element.classList.remove('border-gray-200');
        
        const selectBtn = element.querySelector('.select-address-btn');
        if (selectBtn) {
            selectBtn.classList.add('hidden');
        }
        
        const selectedIndicator = element.querySelector('.mt-3.text-sm.text-center.text-primary');
        if (selectedIndicator) {
            selectedIndicator.classList.remove('hidden');
        }
        
        // Atualizar o valor do input hidden
        document.getElementById('selected_address_id').value = addressId;
        
        // Habilitar o botão de continuar
        const continueBtn = document.getElementById('continue-btn');
        if (continueBtn) {
            continueBtn.disabled = false;
        }
        
        // Ocultar mensagem de erro
        const errorElement = document.getElementById('address-selection-error');
        if (errorElement) {
            errorElement.classList.add('hidden');
        }
    }

    // Execute quando o DOM estiver completamente carregado
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM carregado');
        
        // Elementos principais
        const addressContainer = document.getElementById('address-container');
        const newAddressForm = document.getElementById('new-address-form');
        const newAddressBtn = document.getElementById('new-address-btn');
        const cancelAddressBtn = document.getElementById('cancel-address-btn');
        
        console.log('Botão novo endereço:', newAddressBtn);
        console.log('Formulário novo endereço:', newAddressForm);
        
        // 1. BOTÃO ADICIONAR NOVO ENDEREÇO
        if (newAddressBtn) {
            newAddressBtn.onclick = function(e) {
                console.log('Botão de novo endereço clicado');
                if (addressContainer) addressContainer.classList.add('hidden');
                if (newAddressForm) newAddressForm.classList.remove('hidden');
                return false;
            };
        }
        
        // 2. BOTÃO CANCELAR NOVO ENDEREÇO
        if (cancelAddressBtn) {
            cancelAddressBtn.onclick = function(e) {
                console.log('Botão cancelar clicado');
                if (newAddressForm) newAddressForm.classList.add('hidden');
                if (addressContainer) addressContainer.classList.remove('hidden');
                return false;
            };
        }
        
        // 3. VALIDAÇÃO DO FORMULÁRIO DE PRÓXIMO PASSO
        const nextStepForm = document.getElementById('next-step-form');
        const selectedAddressIdInput = document.getElementById('selected_address_id');
        
        if (nextStepForm) {
            nextStepForm.onsubmit = function(e) {
                const addressId = selectedAddressIdInput ? selectedAddressIdInput.value : null;
                
                if (!addressId) {
                    e.preventDefault();
                    const errorElement = document.getElementById('address-selection-error');
                    if (errorElement) errorElement.classList.remove('hidden');
                    return false;
                }
                return true;
            };
        }
        
        // 4. BUSCA DE CEP
        const searchZipcodeBtn = document.getElementById('search-zipcode-btn');
        const zipcodeInput = document.getElementById('zipcode');
        
        if (searchZipcodeBtn && zipcodeInput) {
            // Máscara para o CEP se IMask estiver disponível
            if (typeof IMask !== 'undefined') {
                IMask(zipcodeInput, {
                    mask: '00000-000'
                });
            }
            
            searchZipcodeBtn.onclick = function(e) {
                e.preventDefault();
                
                const zipcode = zipcodeInput.value.replace(/\D/g, '');
                const zipcodeError = document.getElementById('zipcode-error');
                
                if (zipcode.length !== 8) {
                    if (zipcodeError) {
                        zipcodeError.textContent = 'CEP inválido';
                        zipcodeError.classList.remove('hidden');
                    }
                    return false;
                }
                
                if (zipcodeError) zipcodeError.classList.add('hidden');
                searchZipcodeBtn.disabled = true;
                searchZipcodeBtn.innerHTML = 'Buscando...';
                
                // Fazer requisição para buscar o CEP
                fetch(`/api/checkout/address/zipcode?zipcode=${zipcodeInput.value}`)
                    .then(response => response.json())
                    .then(data => {
                        searchZipcodeBtn.disabled = false;
                        searchZipcodeBtn.innerHTML = 'Buscar';
                        
                        if (data.success) {
                            document.getElementById('street').value = data.address.street || '';
                            document.getElementById('district').value = data.address.neighborhood || '';
                            document.getElementById('city').value = data.address.city || '';
                            document.getElementById('state').value = data.address.state || '';
                        } else if (zipcodeError) {
                            zipcodeError.textContent = data.message || 'CEP não encontrado';
                            zipcodeError.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        searchZipcodeBtn.disabled = false;
                        searchZipcodeBtn.innerHTML = 'Buscar';
                        if (zipcodeError) {
                            zipcodeError.textContent = 'Erro ao buscar o CEP';
                            zipcodeError.classList.remove('hidden');
                        }
                        console.error('Erro:', error);
                    });
                    
                return false;
            };
        }
        
        // 5. ENVIO DO FORMULÁRIO DE ENDEREÇO
        const addressForm = document.getElementById('address-form');
        const saveAddressBtn = document.getElementById('save-address-btn');
        
        if (addressForm) {
            addressForm.onsubmit = function(e) {
                e.preventDefault();
                
                if (saveAddressBtn) {
                    saveAddressBtn.disabled = true;
                    saveAddressBtn.textContent = 'Salvando...';
                }
                
                // Coletar os dados do formulário
                const formData = new FormData(addressForm);
                
                // Enviar os dados via fetch
                fetch('/api/checkout/address', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (saveAddressBtn) {
                        saveAddressBtn.disabled = false;
                        saveAddressBtn.textContent = 'Salvar Endereço';
                    }
                    
                    if (data.success) {
                        // Redirecionar para a próxima etapa
                        window.location.href = data.redirect;
                    } else {
                        // Exibir erros
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const errorElement = document.getElementById(`${field}-error`);
                                if (errorElement) {
                                    errorElement.textContent = data.errors[field][0];
                                    errorElement.classList.remove('hidden');
                                }
                            });
                        } else {
                            alert(data.message || 'Erro ao salvar o endereço');
                        }
                    }
                })
                .catch(error => {
                    if (saveAddressBtn) {
                        saveAddressBtn.disabled = false;
                        saveAddressBtn.textContent = 'Salvar Endereço';
                    }
                    alert('Erro ao processar a requisição');
                    console.error('Erro:', error);
                });
                
                return false;
            };
        }
        
        // 6. INICIALIZAÇÃO - VERIFICAR ENDEREÇO SELECIONADO
        const selectedAddressId = '{{ session('checkout_address_id') }}';
        
        if (selectedAddressId) {
            const continueBtn = document.getElementById('continue-btn');
            if (continueBtn) {
                continueBtn.disabled = false;
            }
                fetch('{{ route("site.checkout.addresses.select") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ address_id: addressId })
                });
            });
        });
        
        addressForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Desabilitar o botão de envio para evitar cliques duplos
            const saveButton = document.getElementById('save-address-btn');
            const originalButtonText = saveButton.innerHTML;
            saveButton.disabled = true;
            saveButton.innerHTML = 'Salvando...';
            
            const formData = new FormData(addressForm);
            
            // Enviar os dados do formulário via AJAX
            fetch('{{ route("site.checkout.addresses.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensagem de sucesso
                    alert('Endereço adicionado com sucesso!');
                    
                    // Atualizar a lista de endereços sem recarregar a página
                    if (data.address && data.address.id) {
                        // Configurar o endereço como selecionado
                        if (selectedAddressIdInput) {
                            selectedAddressIdInput.value = data.address.id;
                        }
                        if (nextStepBtn) {
                            nextStepBtn.disabled = false;
                        }
                        
                        // Redirecionar para a próxima etapa ou recarregar a página
                        window.location.reload();
                    }
                } else {
                    // Mostrar erros do formulário
                    const errors = data.errors || {};
                    Object.keys(errors).forEach(field => {
                        const errorElement = document.getElementById(`${field}-error`);
                        if (errorElement) {
                            errorElement.textContent = errors[field][0];
                            errorElement.classList.remove('hidden');
                        }
                    });
                    
                    // Restaurar botão
                    saveButton.disabled = false;
                    saveButton.innerHTML = originalButtonText;
                }
            })
            .catch(error => {
                console.error('Erro ao salvar endereço:', error);
                alert('Ocorreu um erro ao salvar o endereço. Por favor, tente novamente.');
                
                // Restaurar botão
                saveButton.disabled = false;
                saveButton.innerHTML = originalButtonText;
            });
        });
    });
</script>
@endpush
