<x-app-layout>
    <div class="max-w-6xl mx-auto p-4">
        <h1 class="text-xl font-semibold text-slate-900 mb-6">Finalizar Compra</h1>
        
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Coluna principal com informações do usuário e endereço -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informações do usuário -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-medium mb-4">Informações Pessoais</h2>
                    
                    <form id="user-info-form" class="space-y-4">
                        @php
                            $user = auth()->user();
                        @endphp
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo *</label>
                            <input type="text" id="full_name" name="full_name" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50"
                                   value="{{ $user->name ?? '' }}">
                            <p class="text-xs text-gray-500 mt-1">Nome e sobrenome são obrigatórios</p>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" id="email" name="email" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50"
                                   value="{{ $user->email ?? '' }}" {{ $user ? 'readonly' : '' }}>
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone *</label>
                            <input type="tel" id="phone" name="phone" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50"
                                   value="{{ $user->profile->phone ?? '' }}"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, ''); this.value = maskPhone(this.value);"
                                   maxlength="15" placeholder="(00) 00000-0000">
                        </div>
                        
                        <div>
                            <label for="cpf" class="block text-sm font-medium text-gray-700 mb-1">CPF *</label>
                            <input type="text" id="cpf" name="cpf" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50"
                                   value="{{ $user->profile->cpf ?? '' }}"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, ''); this.value = maskCPF(this.value);"
                                   maxlength="14" placeholder="000.000.000-00">
                        </div>
                        
                        <div class="bg-purple-50 p-3 rounded-md border border-purple-100">
                            <p class="text-sm text-purple-800"><strong>Nota:</strong> Estes dados são necessários para emissão da nota fiscal e envio do seu pedido.</p>
                        </div>
                    </form>
                </div>
                
                <!-- Endereço de entrega -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-medium">Endereço de Entrega</h2>
                        <button type="button" onclick="openAddressModal()" 
                                class="text-purple-600 hover:text-purple-800 text-sm font-medium flex items-center">
                            <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4V20M4 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Adicionar novo endereço
                        </button>
                    </div>
                    
                    @php
                        $userAddresses = auth()->check() ? auth()->user()->addresses : collect([]);
                    @endphp
                    
                    @if($userAddresses->count() > 0)
                        <div class="space-y-3">
                            @foreach($userAddresses as $address)
                                <div class="border rounded-md p-4 address-item hover:border-purple-500 cursor-pointer transition-all"
                                     onclick="selectAddress(this, '{{ $address->id }}')">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3">
                                            <div class="mt-1">
                                                <div class="w-5 h-5 border border-gray-300 rounded-full address-radio flex items-center justify-center transition-all">
                                                    <div class="w-3 h-3 bg-purple-600 rounded-full hidden address-radio-dot"></div>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="font-medium">{{ $address->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $address->street }}, {{ $address->number }}</p>
                                                @if($address->complement)
                                                    <p class="text-sm text-gray-600">{{ $address->complement }}</p>
                                                @endif
                                                <p class="text-sm text-gray-600">{{ $address->neighborhood }} - {{ $address->city }}/{{ $address->state }}</p>
                                                <p class="text-sm text-gray-600">CEP: {{ $address->zipcode }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="selected_address_id" id="selected_address_id" value="">
                    @else
                        <div class="bg-gray-50 rounded-md p-4 text-center">
                            <p class="text-gray-600 mb-3">Você ainda não possui endereços cadastrados.</p>
                            <button type="button" onclick="openAddressModal()" 
                                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Adicionar endereço agora
                            </button>
                        </div>
                    @endif
                </div>
                
                <!-- Opções de frete -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-medium mb-4">Opções de Frete</h2>
                    
                    <!-- Formulário para calcular frete -->
                    <form action="{{ route('site.shipping.calculate') }}" method="POST" class="mb-4" id="shipping-calculator-form">
                      @csrf
                      <div class="flex mb-2">
                        <input 
                          type="text" 
                          name="zip_code" 
                          id="zip_code"
                          value="{{ $zipCode ?? '' }}" 
                          placeholder="00000-000" 
                          class="flex-1 px-3 py-2 border rounded-l text-sm" 
                          maxlength="9"
                          required
                          oninput="this.value = maskCEP(this.value)"
                        >
                        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-r text-sm font-medium">
                          Calcular Frete
                        </button>
                      </div>
                    </form>
                    
                    <!-- Exibição das opções de frete -->
                    <div id="shipping-options" class="space-y-3">
                        @if(isset($shippingOptions) && count($shippingOptions) > 0)
                            @foreach($shippingOptions as $option)
                                @php
                                    $isSelected = isset($selectedShipping) && $selectedShipping['id'] == $option['id'];
                                @endphp
                                <div class="border rounded-md p-3 shipping-option {{ $isSelected ? 'border-purple-500 bg-purple-50' : '' }}"
                                     onclick="selectShipping(this, '{{ $option['id'] }}')">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-start space-x-3">
                                            <div class="mt-1">
                                                <div class="w-5 h-5 border border-gray-300 rounded-full shipping-radio flex items-center justify-center transition-all {{ $isSelected ? 'border-purple-600' : '' }}">
                                                    <div class="w-3 h-3 bg-purple-600 rounded-full {{ $isSelected ? '' : 'hidden' }} shipping-radio-dot"></div>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="font-medium">{{ $option['name'] ?? $option['title'] ?? 'Opção de frete' }}</p>
                                                <p class="text-xs text-gray-500">{{ $option['delivery_estimate'] ?? ($option['delivery_time'] ? ($option['delivery_time'] . ' ' . ($option['delivery_time'] == 1 ? 'dia útil' : 'dias úteis')) : 'Prazo a calcular') }}</p>
                                            </div>
                                        </div>
                                        <span class="font-medium">{{ $option['formatted_price'] ?? ('R$ ' . number_format($option['price'] ?? 0, 2, ',', '.')) }}</span>
                                    </div>
                                </div>
                            @endforeach
                            <input type="hidden" name="selected_shipping_id" id="selected_shipping_id" value="{{ $selectedShipping['id'] ?? '' }}">
                        @elseif(isset($shippingCalculated) && $shippingCalculated)
                            <div class="bg-gray-50 rounded-md p-4 text-center">
                                <p class="text-gray-600">Nenhuma opção de frete disponível para o CEP informado.</p>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-md p-4 text-center">
                                <p class="text-gray-600">Informe seu CEP para calcular o frete.</p>
                            </div>
                        @endif
                        
                        @if(session('shipping_error'))
                            <div class="bg-red-50 text-red-700 p-3 rounded-md text-sm">
                                {{ session('shipping_error') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Resumo do pedido -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 h-max">
                <h2 class="text-lg font-medium border-b pb-4 mb-4">Resumo do Pedido</h2>
                
                <!-- Lista de itens resumida -->
                <div class="space-y-4 mb-6">
                    @if(isset($cartItems) && $cartItems->count() > 0)
                        <div class="text-sm text-gray-500 mb-2">{{ $cartItems->count() }} {{ $cartItems->count() == 1 ? 'item' : 'itens' }} ({{ $cartItems->sum('quantity') }} {{ $cartItems->sum('quantity') == 1 ? 'unidade' : 'unidades' }})</div>
                        @foreach($cartItems as $item)
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 flex-shrink-0">
                                    <img src="{{ $item->vinylMaster->cover_image }}" alt="{{ $item->vinylMaster->title }}" class="w-full h-full object-cover rounded">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ $item->vinylMaster->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->vinylMaster->artists->first()->name }}</p>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-xs text-gray-600">Qtd: {{ $item->quantity }}</span>
                                        <span class="text-sm font-medium">R$ {{ number_format($item->vinylMaster->vinylSec->price * $item->quantity, 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-gray-500 text-center py-4">Carrinho vazio</div>
                    @endif
                </div>
                
                <!-- Resumo de valores -->
                <div class="space-y-2 py-4 border-t border-gray-100">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span>R$ {{ number_format($cartTotal ?? 0, 2, ',', '.') }}</span>
                    </div>
                    
                    @if(isset($discount) && $discount > 0)
                    <div class="flex justify-between text-red-600">
                        <span>Desconto:</span>
                        <span>-R$ {{ number_format($discount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Frete:</span>
                        <span>
                            @if(isset($selectedShipping) && $selectedShipping)
                                R$ {{ number_format($selectedShipping['price'] ?? 0, 2, ',', '.') }}
                            @else
                                Calcular
                            @endif
                        </span>
                    </div>
                    
                    <div class="flex justify-between font-bold text-lg pt-2 border-t">
                        <span>Total:</span>
                        <span>
                            @php
                                $totalValue = ($cartTotal ?? 0);
                                if (isset($discount) && $discount > 0) {
                                    $totalValue -= $discount;
                                }
                                if (isset($selectedShipping) && $selectedShipping) {
                                    $totalValue += $selectedShipping['price'] ?? 0;
                                }
                            @endphp
                            R$ {{ number_format($totalValue, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
                
                <!-- Botões de ação -->
                <div class="pt-4 space-y-3">
                    <form action="{{ route('site.shipping.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_data" id="user_data_input">
                        <button type="submit" 
                                onclick="return prepareCheckoutData()"
                                class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-md font-medium transition-colors">
                            Continuar para Pagamento
                        </button>
                    </form>
                    
                    <a href="{{ route('site.cart.index') }}" class="block text-center w-full border border-gray-300 hover:bg-gray-50 text-gray-700 py-3 rounded-md font-medium transition-colors">
                        Voltar ao Carrinho
                    </a>
                </div>
                
                <!-- Métodos de pagamento aceitos -->
                <div class="mt-6 flex flex-wrap justify-center gap-4 border-t pt-4">
                    <img src='https://readymadeui.com/images/master.webp' alt="MasterCard" class="w-10 object-contain" />
                    <img src='https://readymadeui.com/images/visa.webp' alt="Visa" class="w-10 object-contain" />
                    <img src='https://readymadeui.com/images/american-express.webp' alt="American Express" class="w-10 object-contain" />
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de endereço -->
    <x-address-modal route="site.profile.address-modal.store" />
    
    <!-- Scripts -->
    <script src="{{ asset('js/cart.js') }}"></script>
    
    <script>
        // Máscaras para formatação de campos
        function maskCEP(value) {
            return value
                .replace(/\D/g, '')
                .replace(/(\d{5})(\d)/, '$1-$2')
                .replace(/(-\d{3})\d+?$/, '$1');
        }
        
        function maskPhone(value) {
            return value
                .replace(/\D/g, '')
                .replace(/(\d{2})(\d)/, '($1) $2')
                .replace(/(\d{5})(\d)/, '$1-$2')
                .replace(/(-\d{4})\d+?$/, '$1');
        }
        
        function maskCPF(value) {
            return value
                .replace(/\D/g, '')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d{1,2})$/, '$1-$2')
                .replace(/(-\d{2})\d+?$/, '$1');
        }
        
        // Função para abrir o modal de endereço
        function openAddressModal() {
            // Assumindo que você tem uma função AddressModal.open() definida no seu sistema
            if (typeof AddressModal !== 'undefined' && AddressModal.open) {
                AddressModal.open();
            } else {
                // Alternativa caso não exista a função
                const modalElement = document.querySelector('.address-modal');
                if (modalElement) modalElement.classList.remove('hidden');
            }
        }
        
        // Seleção de endereço
        function selectAddress(element, addressId) {
            // Remove a seleção de todos os itens
            document.querySelectorAll('.address-item').forEach(item => {
                item.classList.remove('border-purple-500', 'bg-purple-50');
                item.querySelector('.address-radio-dot').classList.add('hidden');
                item.querySelector('.address-radio').classList.remove('border-purple-600');
            });
            
            // Adiciona a seleção ao item clicado
            element.classList.add('border-purple-500', 'bg-purple-50');
            element.querySelector('.address-radio-dot').classList.remove('hidden');
            element.querySelector('.address-radio').classList.add('border-purple-600');
            
            // Atualiza o valor do campo oculto
            document.getElementById('selected_address_id').value = addressId;
            
            // Se o CEP estiver disponível, preencha o campo de CEP
            const zipcode = element.dataset.zipcode;
            if (zipcode) {
                const zipField = document.getElementById('zip_code');
                if (zipField) zipField.value = zipcode;
            }
        }
        
        // Seleção de frete
        function selectShipping(element, shippingId) {
            // Remove a seleção de todos os itens
            document.querySelectorAll('.shipping-option').forEach(item => {
                item.classList.remove('border-purple-500', 'bg-purple-50');
                item.querySelector('.shipping-radio-dot').classList.add('hidden');
                item.querySelector('.shipping-radio').classList.remove('border-purple-600');
            });
            
            // Adiciona a seleção ao item clicado
            element.classList.add('border-purple-500', 'bg-purple-50');
            element.querySelector('.shipping-radio-dot').classList.remove('hidden');
            element.querySelector('.shipping-radio').classList.add('border-purple-600');
            
            // Atualiza o valor do campo oculto
            document.getElementById('selected_shipping_id').value = shippingId;
            
            // Enviar a seleção para o servidor via AJAX
            const form = new FormData();
            form.append('shipping_option', shippingId);
            form.append('_token', '{{ csrf_token() }}');
            
            fetch('{{ route("site.shipping.select") }}', {
                method: 'POST',
                body: form
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar o resumo do pedido sem recarregar a página
                    // Isso pode requerer uma função adicional para atualizar os valores exibidos
                    updateOrderSummary(data);
                }
            })
            .catch(error => console.error('Erro ao selecionar frete:', error));
        }
        
        // Atualiza o resumo do pedido com os dados do servidor
        function updateOrderSummary(data) {
            // Esta função receberia dados do servidor após selecionar um frete
            // e atualizaria os valores no resumo do pedido
            if (data.totalValue) {
                document.querySelector('.font-bold.text-lg span:last-child').textContent = 
                    `R$ ${data.totalValue.toFixed(2).replace('.', ',')}`;
            }
        }
        
        // Prepara os dados para enviar ao checkout
        function prepareCheckoutData() {
            // Validar se todos os campos obrigatórios estão preenchidos
            const fullName = document.getElementById('full_name');
            const email = document.getElementById('email');
            const phone = document.getElementById('phone');
            const cpf = document.getElementById('cpf');
            const selectedAddressId = document.getElementById('selected_address_id');
            const selectedShippingId = document.getElementById('selected_shipping_id');
            
            let isValid = true;
            let errorMessage = '';
            
            // Validar campos de dados pessoais
            if (!fullName.value.trim()) {
                fullName.classList.add('border-red-500');
                isValid = false;
                errorMessage += 'Nome completo é obrigatório. ';
            } else {
                fullName.classList.remove('border-red-500');
            }
            
            if (!email.value.trim() || !email.value.includes('@')) {
                email.classList.add('border-red-500');
                isValid = false;
                errorMessage += 'Email válido é obrigatório. ';
            } else {
                email.classList.remove('border-red-500');
            }
            
            if (!phone.value.trim() || phone.value.replace(/\D/g, '').length < 10) {
                phone.classList.add('border-red-500');
                isValid = false;
                errorMessage += 'Telefone válido é obrigatório. ';
            } else {
                phone.classList.remove('border-red-500');
            }
            
            if (!cpf.value.trim() || cpf.value.replace(/\D/g, '').length !== 11) {
                cpf.classList.add('border-red-500');
                isValid = false;
                errorMessage += 'CPF válido é obrigatório. ';
            } else {
                cpf.classList.remove('border-red-500');
            }
            
            // Validar seleção de endereço
            if (!selectedAddressId || !selectedAddressId.value) {
                isValid = false;
                errorMessage += 'Selecione um endereço de entrega. ';
                document.querySelector('.bg-white.rounded-lg:nth-child(2)').scrollIntoView({ behavior: 'smooth' });
            }
            
            // Validar seleção de frete
            if (!selectedShippingId || !selectedShippingId.value) {
                isValid = false;
                errorMessage += 'Selecione uma opção de frete. ';
                document.querySelector('.bg-white.rounded-lg:nth-child(3)').scrollIntoView({ behavior: 'smooth' });
            }
            
            if (!isValid) {
                alert('Por favor, corrija os seguintes erros:\n' + errorMessage);
                return false;
            }
            
            // Se tudo estiver válido, adiciona os dados ao campo oculto
            const userData = {
                full_name: fullName.value,
                email: email.value,
                phone: phone.value,
                cpf: cpf.value,
                address_id: selectedAddressId.value,
                shipping_id: selectedShippingId.value
            };
            
            document.getElementById('user_data_input').value = JSON.stringify(userData);
            return true;
        }
    </script>
</x-app-layout>
