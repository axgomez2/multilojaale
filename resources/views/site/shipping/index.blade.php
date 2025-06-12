<x-app-layout>
    <!-- Script de integração com ViaCEP - Adicionado no topo para garantir carregamento -->
    <script type="text/javascript">
        // Executar quando a página estiver completamente carregada
        window.onload = function() {
            console.log('Página carregada, iniciando script de CEP...');
            
            // Obter referências aos elementos do DOM
            const cepInput = document.getElementById('form2_zipcode');
            const streetInput = document.getElementById('form2_street');
            const districtInput = document.getElementById('form2_district');
            const cityInput = document.getElementById('form2_city');
            const stateInput = document.getElementById('form2_state');
            const loadingElement = document.getElementById('form2_zipcode-loading');            
            const errorElement = document.getElementById('form2_zipcode-error');
            const feedbackElement = document.getElementById('form2_zipcode-feedback');
            
            console.log('Elementos do formulário:', {
                cepInput: cepInput ? 'Encontrado' : 'Não encontrado',
                streetInput: streetInput ? 'Encontrado' : 'Não encontrado',
                districtInput: districtInput ? 'Encontrado' : 'Não encontrado',
                cityInput: cityInput ? 'Encontrado' : 'Não encontrado',
                stateInput: stateInput ? 'Encontrado' : 'Não encontrado',
                loadingElement: loadingElement ? 'Encontrado' : 'Não encontrado',
                errorElement: errorElement ? 'Encontrado' : 'Não encontrado',
                feedbackElement: feedbackElement ? 'Encontrado' : 'Não encontrado'
            });
            
            // Verificar se o campo CEP existe
            if (!cepInput) {
                console.error('Campo CEP não encontrado!');
                return;
            }
            
            // Adicionar botão de busca de CEP
            const searchButton = document.createElement('button');
            searchButton.type = 'button';
            searchButton.className = 'ml-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none';
            searchButton.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>';
            searchButton.setAttribute('aria-label', 'Buscar CEP');
            
            // Inserir o botão após o input de CEP
            cepInput.parentNode.appendChild(searchButton);
            
            // Aplicar máscara ao campo de CEP
            cepInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 5) {
                    value = value.substring(0, 5) + '-' + value.substring(5, 8);
                }
                e.target.value = value;
            });
            
            // Função para consultar o CEP
            function consultarCEP() {
                const cep = cepInput.value.replace(/\D/g, '');
                
                // Validar o CEP
                if (cep.length !== 8) {
                    errorElement.textContent = 'O CEP deve ter 8 dígitos no formato 00000-000';
                    errorElement.classList.remove('hidden');
                    feedbackElement.textContent = '';
                    return;
                }
                
                // Limpar mensagens anteriores
                errorElement.classList.add('hidden');
                feedbackElement.textContent = 'Consultando CEP...';
                feedbackElement.classList.add('text-blue-500');
                feedbackElement.classList.remove('text-red-500', 'text-green-500');
                
                // Mostrar indicador de carregamento
                loadingElement.classList.remove('hidden');
                
                // Log para depuração
                console.log('Iniciando consulta de CEP:', cep);
                
                // Verificar se o ViaCEP está online com um timeout de 5 segundos
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 5000);
                
                // Tentar primeiro a API do ViaCEP
                console.log('Consultando ViaCEP para o CEP:', cep);
                fetch(`https://viacep.com.br/ws/${cep}/json/`, { signal: controller.signal })
                    .then(response => {
                        clearTimeout(timeoutId);
                        if (!response.ok) {
                            throw new Error(`Erro na consulta do CEP: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Esconder indicador de carregamento
                        loadingElement.classList.add('hidden');
                        console.log('Resposta do ViaCEP:', data);
                        
                        // Verificar se o CEP foi encontrado
                        if (data.erro) {
                            feedbackElement.textContent = 'CEP não encontrado';
                            feedbackElement.classList.add('text-red-500');
                            feedbackElement.classList.remove('text-blue-500', 'text-green-500');
                            return;
                        }
                        
                        // Preencher os campos com os dados retornados
                        if (streetInput) streetInput.value = data.logradouro || '';
                        if (districtInput) districtInput.value = data.bairro || '';
                        if (cityInput) cityInput.value = data.localidade || '';
                        if (stateInput && data.uf) {
                            // Selecionar o estado no dropdown
                            const options = stateInput.options;
                            for (let i = 0; i < options.length; i++) {
                                if (options[i].value === data.uf) {
                                    stateInput.selectedIndex = i;
                                    break;
                                }
                            }
                        }
                        
                        // Mostrar feedback de sucesso
                        feedbackElement.textContent = 'CEP encontrado com sucesso!';
                        feedbackElement.classList.add('text-green-500');
                        feedbackElement.classList.remove('text-blue-500', 'text-red-500');
                        
                        // Focar no campo de número após preencher os dados
                        const numberInput = document.getElementById('form2_number');
                        if (numberInput) {
                            numberInput.focus();
                        }
                    })
                    .catch(error => {
                        clearTimeout(timeoutId);
                        console.error('Erro ao consultar o ViaCEP:', error);
                        
                        // Tentar API alternativa (BrasilAPI) se o ViaCEP falhar
                        console.log('Tentando API alternativa (BrasilAPI)...');
                        fetch(`https://brasilapi.com.br/api/cep/v1/${cep}`)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`Erro na consulta do CEP via BrasilAPI: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                // Esconder indicador de carregamento
                                loadingElement.classList.add('hidden');
                                console.log('Resposta da BrasilAPI:', data);
                                
                                // Preencher os campos com os dados retornados da API alternativa
                                if (streetInput) streetInput.value = data.street || '';
                                if (districtInput) districtInput.value = data.neighborhood || '';
                                if (cityInput) cityInput.value = data.city || '';
                                if (stateInput && data.state) {
                                    // Selecionar o estado no dropdown
                                    const options = stateInput.options;
                                    for (let i = 0; i < options.length; i++) {
                                        if (options[i].value === data.state) {
                                            stateInput.selectedIndex = i;
                                            break;
                                        }
                                    }
                                }
                                
                                // Mostrar feedback de sucesso
                                feedbackElement.textContent = 'CEP encontrado com sucesso (via BrasilAPI)!';
                                feedbackElement.classList.add('text-green-500');
                                feedbackElement.classList.remove('text-blue-500', 'text-red-500');
                                
                                // Focar no campo de número após preencher os dados
                                const numberInput = document.getElementById('form2_number');
                                if (numberInput) {
                                    numberInput.focus();
                                }
                            })
                            .catch(error2 => {
                                // Esconder indicador de carregamento
                                loadingElement.classList.add('hidden');
                                
                                // Mostrar mensagem de erro após tentar ambas as APIs
                                console.error('Erro ao consultar o CEP via BrasilAPI:', error2);
                                feedbackElement.textContent = 'Serviços de CEP indisponíveis. Por favor, digite o endereço manualmente.';
                                feedbackElement.classList.add('text-red-500');
                                feedbackElement.classList.remove('text-blue-500', 'text-green-500');
                            });
                    });
            }
            
            // Adicionar eventos para consultar o CEP
            cepInput.addEventListener('blur', consultarCEP);
            searchButton.addEventListener('click', consultarCEP);
            
            // Adicionar evento para consultar CEP ao pressionar Enter no campo
            cepInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Evitar submit do formulário
                    consultarCEP();
                }
            });
            
            console.log('Script de consulta de CEP carregado e pronto para uso');
        };
    </script>
    <!-- Container para mensagens de depuração, se necessário -->
    
    <!-- Incluindo o componente de modal de endereço -->
   
    
    <div class="max-w-7xl max-lg:max-w-2xl mx-auto p-4">
        <!-- Bloco de depuração para o formulário de endereço -->
       
        <!-- Mensagem de aviso para itens sem estoque -->
        @if(session('warning'))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        {{ session('warning') }}
                    </p>
                </div>
            </div>
        </div>
        @endif
          
        <h1 class="text-xl font-semibold text-slate-900 mb-6">Frete e Entrega:  
            <span class="text-gray-600 text-sm mt-1">{{ $cartItems->count() }} {{ $cartItems->count() == 1 ? 'item' : 'itens' }} ({{ $cartItems->sum('quantity') }} {{ $cartItems->sum('quantity') == 1 ? 'unidade' : 'unidades' }})</span>
        </h1>
        
        <!-- Processo de checkout -->
       
        
        <div class="grid lg:grid-cols-3 lg:gap-x-8 gap-x-6 gap-y-8 mt-6">
            <div class="lg:col-span-2 space-y-6">
                             
                
                <!-- Verificação de dados do usuário -->
                <div class="bg-white rounded-lg shadow-md p-6 mt-4">
                    <h2 class="text-lg font-medium mb-4">Dados do Comprador:</h2>
                    
                    @if(!$userHasRequiredData)
                        <div class="p-4 mb-4 border border-red-200 bg-red-50 rounded-md">
                            <p class="text-red-700">Para prosseguir com a compra, precisamos que você informe alguns dados básicos.</p>
                        </div>
                        
                        <form action="{{ route('site.shipping.save-user-data') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label for="cpf" class="block text-sm font-medium text-gray-700 mb-1">CPF</label>
                                    <input type="text" name="cpf" id="cpf" value="{{ $user->cpf ?? '' }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                                        placeholder="000.000.000-00" required>
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                                    <input type="text" name="phone" id="phone" value="{{ $user->phone ?? '' }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                                        placeholder="(00) 00000-0000" required>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md">
                                    Salvar Dados
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="mb-4 p-4 border border-green-100 bg-green-50 rounded-md">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-green-700">Seus dados estão completos!</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <span class="block text-sm font-medium text-gray-700 mb-1">CPF</span>
                                <p class="text-gray-900">{{ $user->cpf }}</p>
                            </div>
                            <div>
                                <span class="block text-sm font-medium text-gray-700 mb-1">Telefone</span>
                                <p class="text-gray-900">{{ $user->phone }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <span class="block text-sm font-medium text-gray-700 mb-1">Email</span>
                                <p class="text-gray-900">{{ $user->email }}</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Seção de endereços -->
                <div class="bg-white rounded-lg shadow-md p-6 mt-4">
                    <h2 class="text-lg font-medium mb-4">Endereço de Entrega</h2>
                    
                    <!-- Se o usuário não tem dados completos, mostra mensagem -->
                    @if(!$userHasRequiredData)
                        <div class="p-4 mb-4 border border-yellow-200 bg-yellow-50 rounded-md">
                            <p class="text-yellow-700">Por favor, complete seus dados pessoais acima antes de adicionar um endereço.</p>
                        </div>
                    @elseif(!$userHasAddress)
                        <!-- Se não tem endereço cadastrado, mostra formulário -->
                        <div class="p-4 mb-4 border border-red-200 bg-red-50 rounded-md">
                            <p class="text-red-700">Você ainda não possui um endereço cadastrado. Por favor, adicione um endereço para continuar.</p>
                        </div>
                       
                        <form action="{{ route('site.shipping.save-address') }}" method="POST" class="space-y-4 debug-form" id="debug-address-form" style="display: block !important; visibility: visible !important; opacity: 1 !important; position: static !important; height: auto !important; width: auto !important; overflow: visible !important;">
                            
                            @csrf
                            <div>
                                <h3 class="font-medium mb-4 pb-2 border-b">Dados do Destinatário</h3>
                                <div>
                                    <label for="recipient_name" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                                    <input type="text" name="recipient_name" id="recipient_name" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                                        required>
                                    <!-- Campos ocultos para CPF e telefone -->
                                    <input type="hidden" name="recipient_document" value="{{ $user->cpf }}">
                                    <input type="hidden" name="recipient_phone" value="{{ $user->phone }}">
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <h3 class="font-medium mb-4 pb-2 border-b">Endereço de Entrega</h3>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div>
                                        <label for="form2_zipcode" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                                        <div class="flex">
                                            <input type="text" name="zipcode" id="form2_zipcode" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                                                placeholder="00000-000" maxlength="9" required>
                                            <div id="form2_zipcode-loading" class="ml-2 hidden">
                                                <svg class="animate-spin h-5 w-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div id="form2_zipcode-error" class="text-red-500 text-sm mt-1 hidden">O CEP deve ter 8 dígitos no formato 00000-000</div>
                                        <div id="form2_zipcode-feedback" class="text-sm mt-1"></div>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="form2_street" class="block text-sm font-medium text-gray-700 mb-1">Rua/Avenida</label>
                                        <input type="text" name="street" id="form2_street" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                                            required>
                                    </div>
                                    <div>
                                        <label for="form2_number" class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                                        <input type="text" name="number" id="form2_number" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                                            required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="form2_complement" class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                                        <input type="text" name="complement" id="form2_complement" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                    </div>
                                    <div>
                                        <label for="form2_district" class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                                        <input type="text" name="district" id="form2_district" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                                            required>
                                    </div>
                                    <div>
                                        <label for="form2_city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                                        <input type="text" name="city" id="form2_city" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                                            required>
                                    </div>
                                    <div>
                                        <label for="form2_state" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                        <select name="state" id="form2_state" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                                            required>
                                            <option value="">Selecione...</option>
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
                            </div>
                            
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-4 rounded-md shadow-md hover:shadow-lg transition duration-200">
                                    <i class="fas fa-save mr-2"></i> Salvar Endereço
                                </button>
                       
                            </div>
                        </form>
                    
                    @else
                        <!-- Se já tem endereços cadastrados, mostra lista para seleção -->
                        @if(count($addresses) > 0)
                            <form action="{{ route('site.shipping.select-address') }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    @foreach($addresses as $address)
                                        <div class="border rounded-md p-4 {{ $selectedAddress == $address->id ? 'border-purple-600 bg-purple-50' : 'border-gray-200' }}">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <input type="radio" name="address_id" id="address_{{ $address->id }}" value="{{ $address->id }}" 
                                                        {{ $selectedAddress == $address->id ? 'checked' : '' }}
                                                        class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                                                </div>
                                                <label for="address_{{ $address->id }}" class="ml-3 flex-1 cursor-pointer">
                                                    <div>
                                                        <p class="font-medium">{{ $address->recipient_name }}</p>
                                                        <p class="text-gray-600 text-sm">{{ $address->street }}, {{ $address->number }}</p>
                                                        @if($address->complement)
                                                            <p class="text-gray-600 text-sm">{{ $address->complement }}</p>
                                                        @endif
                                                        <p class="text-gray-600 text-sm">{{ $address->district }} - {{ $address->city }}/{{ $address->state }}</p>
                                                        <p class="text-gray-600 text-sm">CEP: {{ $address->zipcode }}</p>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md">
                                        Usar este endereço
                                    </button>
                                </div>
                            </form>
                        @endif
                    @endif
                </div>
                
                <!-- Seção de opções de frete -->
                @if($selectedAddress && count($shippingOptions) > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mt-4">
                    <h2 class="text-lg font-medium mb-4">Opções de Frete</h2>
                    
                    <form action="{{ route('site.shipping.select-shipping') }}" method="POST" id="shipping-form">
                        @csrf
                        <div class="space-y-4">
                            @foreach($shippingOptions as $option)
                                <div class="border rounded-md p-4 {{ isset($selectedShipping['id']) && $selectedShipping['id'] == $option['id'] ? 'border-purple-600 bg-purple-50' : 'border-gray-200' }} {{ isset($option['error']) && $option['error'] ? 'border-yellow-300' : '' }} hover:border-purple-400 transition-colors duration-200">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 pt-1">
                                            <input type="radio" name="shipping_id" id="shipping_{{ $option['id'] }}" value="{{ $option['id'] }}" 
                                                {{ isset($selectedShipping['id']) && $selectedShipping['id'] == $option['id'] ? 'checked' : '' }}
                                                class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300">
                                        </div>
                                        <label for="shipping_{{ $option['id'] }}" class="ml-3 flex-1 cursor-pointer">
                                            <div>
                                                <div class="flex justify-between items-center">
                                                    <div class="flex items-center">
                                                        <p class="font-medium text-base">{{ $option['name'] }}</p>
                                                        @if(isset($option['error']) && $option['error'])
                                                            <span class="ml-2 px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs rounded-full">Estimado</span>
                                                        @endif
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">{{ $option['company'] ?? 'Transportadora' }}</span>
                                                </div>
                                                
                                                <div class="mt-2 flex justify-between items-center">
                                                    <div>
                                                        <p class="text-gray-700 text-sm flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            @if(isset($option['custom_delivery_range']))
                                                                @if($option['custom_delivery_range']['min'] == $option['custom_delivery_range']['max'])
                                                                    {{ $option['custom_delivery_range']['min'] }} {{ $option['custom_delivery_range']['min'] > 1 ? 'dias úteis' : 'dia útil' }}
                                                                @else
                                                                    {{ $option['custom_delivery_range']['min'] }} a {{ $option['custom_delivery_range']['max'] }} dias úteis
                                                                @endif
                                                            @else
                                                                {{ $option['days'] }} {{ $option['days'] > 1 ? 'dias úteis' : 'dia útil' }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="font-bold text-lg text-purple-700">R$ {{ isset($option['custom_price']) ? $option['custom_price'] : number_format($option['price'], 2, ',', '.') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md">
                                Selecionar esta opção de frete
                            </button>
                        </div>
                    </form>
                </div>
                @endif
                
                <!-- Botão para avançar para pagamento -->
                
            </div>
            
            <!-- Resumo do pedido com totais -->
            <div class="bg-white rounded-md px-4 py-6 h-max shadow-sm border border-gray-200">
                <h2 class="text-lg font-medium mb-4">Resumo</h2>
                    <ul>
                    @foreach($cartItems as $item)
                        @php
                            // Verificar estoque diretamente no template para garantir que itens sem estoque não apareçam
                            $hasStock = true;
                            if ($item->vinylMaster && $item->vinylMaster->vinylSec) {
                                $hasStock = $item->vinylMaster->vinylSec->stock > 0 && $item->vinylMaster->vinylSec->stock >= $item->quantity;
                            }
                        @endphp
                        
                        @if($hasStock)
                        <li class="flex items-center px-6 py-3 hover:bg-gray-50 transition">
                            <div class="flex-shrink-0 w-11 h-11 bg-blue-200 rounded-full flex items-center justify-center">
                                @if($item->vinylMaster && $item->vinylMaster->cover_image)
                                    <a href="{{ route('site.vinyl.show', [$item->vinylMaster->artists->first()->slug, $item->vinylMaster->slug]) }}" class="shrink-0">
                                        <img class="h-12 w-12 rounded-full" src="{{ $item->vinylMaster->cover_image }}" alt="{{ $item->vinylMaster->title }}" />
                                    </a>
                                    @elseif($item->product && $item->product->image)
                                    <img class="h-12 w-12 rounded-full" src="{{ $item->product->image }}" alt="{{ $item->product->name }}" />
                                    @else
                                    <div class="h-12 w-12 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500">Sem imagem</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-grow">
                                @if($item->vinylMaster)
                                    <div class="text-[14px] text-slate-800">{{ Str::limit($item->vinylMaster->title, 15) }}</div>
                                    <div class="text-[13px] text-slate-500">
                                    {{ Str::limit($item->vinylMaster->artists->first()->name, 15) }}
                                    </div>
                                @elseif($item->product)
                                    <div class="text-[15px] font-sm text-slate-900 truncate">{{ $item->product->name }}</div>
                                    <div class="text-[13px] text-slate-500">{{ $item->product->description }}</div>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-[13px]">Qtd: {{ $item->quantity }}</span>
                                <span class="text-[13px]">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</span>
                            </div>
                        </li>
                        @endif
                        @endforeach 
                    </ul>

                    @php
                        // Recalcular o subtotal considerando apenas itens com estoque
                        $recalculatedSubtotal = 0;
                        foreach($cartItems as $item) {
                            $hasStock = true;
                            if ($item->vinylMaster && $item->vinylMaster->vinylSec) {
                                $hasStock = $item->vinylMaster->vinylSec->stock > 0 && $item->vinylMaster->vinylSec->stock >= $item->quantity;
                            }
                            
                            if ($hasStock) {
                                $itemPrice = $item->price ?? ($item->product->price ?? ($item->vinylMaster->vinylSec->price ?? 0));
                                $recalculatedSubtotal += $itemPrice * $item->quantity;
                            }
                        }
                    @endphp
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">R$ {{ number_format($recalculatedSubtotal, 2, ',', '.') }}</span>
                    </div>
                    
                    <!-- Frete -->
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Frete:</span>
                        <span class="font-medium">
                            @if(isset($selectedShipping['id']))
                                R$ {{ isset($selectedShipping['custom_price']) ? $selectedShipping['custom_price'] : number_format($selectedShipping['price'], 2, ',', '.') }}
                                <span class="text-xs text-gray-500 block">{{ $selectedShipping['name'] }}</span>
                            @else
                                Selecione uma opção
                            @endif
                        </span>
                    </div>
                
                    <!-- Total -->
                    <div class="flex justify-between py-3 text-lg font-bold border-t mt-2">
                        <span>Total:</span>
                        <span id="order-total">
                            @if(isset($selectedShipping['id']))
                                R$ {{ number_format($recalculatedSubtotal + $selectedShipping['price'], 2, ',', '.') }}
                            @else
                                R$ {{ number_format($recalculatedSubtotal, 2, ',', '.') }}
                            @endif
                        </span>
                    </div>

                    @if($userHasRequiredData && $userHasAddress && isset($selectedShipping['id']))
                <div class="mt-6">
                    <form action="{{ route('site.checkout.create-order') }}" method="POST">
                        @csrf
                        <button type="submit" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center font-bold py-3 px-4 rounded-md">
                            Ir para Pagamento
                        </button>
                    </form>
                </div>
                @endif

                    <div class="flex justify-between mt-4">
                        <a href="{{ route('site.cart.index') }}" class="text-purple-600 hover:text-purple-800 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Voltar ao carrinho
                        </a>
                    </div>

                </div>
            </div>

    </div>
  
    </div>
    
 
</x-app-layout>