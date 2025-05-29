@php
    $selectedAddressId = session('checkout_address_id');
    $selectedAddress = null;
    
    if ($selectedAddressId) {
        $selectedAddress = auth()->user()->addresses()->find($selectedAddressId);
    }
    
    $shippingOptions = $shippingQuote->options ?? [];
    $selectedShipping = session('selected_shipping');
@endphp

<div class="space-y-6">
    <!-- Cabeçalho da seção -->
    <div class="border-b border-gray-200 pb-4">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Opções de Entrega</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Escolha a melhor opção de entrega para o seu pedido</p>
    </div>
    
    <!-- Endereço selecionado -->
    @if($selectedAddress)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-medium text-blue-800">Endereço de entrega</h3>
                    <p class="text-sm text-gray-700 mt-1">
                        {{ $selectedAddress->street }}, {{ $selectedAddress->number }}
                        @if($selectedAddress->complement)
                            - {{ $selectedAddress->complement }}
                        @endif
                        <br>
                        {{ $selectedAddress->neighborhood }} - {{ $selectedAddress->city }}/{{ $selectedAddress->state }}
                        <br>
                        CEP: {{ substr($selectedAddress->zipcode, 0, 5) }}-{{ substr($selectedAddress->zipcode, 5) }}
                    </p>
                </div>
                <a href="{{ route('site.newcheckout.index', ['step' => 'address']) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Alterar
                </a>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Atenção!</strong>
            <span class="block sm:inline">Nenhum endereço selecionado. Por favor, selecione um endereço de entrega.</span>
        </div>
    @endif
    
    <!-- Opções de frete -->
    @if(!empty($shippingOptions))
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-800 dark:text-white">Escolha o frete</h3>
            
            <form id="shipping-form" action="{{ route('site.newcheckout.shipping.process') }}" method="POST" class="space-y-4">
                @csrf
                
                @foreach($shippingOptions as $option)
                    @php
                        $optionId = $option['id'] ?? null;
                        $optionName = $option['name'] ?? 'Opção de frete';
                        $optionPrice = $option['price'] ?? 0;
                        $deliveryTime = $option['delivery_time'] ?? 0;
                        $deliveryEstimate = $deliveryTime > 0 ? 
                            ($deliveryTime == 1 ? '1 dia útil' : "$deliveryTime dias úteis") : 'A combinar';
                        $isSelected = $selectedShipping && ($selectedShipping['id'] == $optionId);
                    @endphp
                    
                    <div class="relative">
                        <input 
                            class="peer hidden" 
                            id="shipping-option-{{ $optionId }}" 
                            name="shipping_option" 
                            type="radio" 
                            value="{{ $optionId }}"
                            data-price="{{ $optionPrice }}"
                            data-delivery-time="{{ $deliveryTime }}"
                            {{ $isSelected ? 'checked' : '' }}
                            required>
                        <label 
                            for="shipping-option-{{ $optionId }}" 
                            class="block cursor-pointer rounded-lg border border-gray-200 bg-white p-4 hover:border-blue-500 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:ring-1 peer-checked:ring-blue-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $optionName }}</p>
                                    <p class="text-sm text-gray-600">
                                        Entrega em até {{ $deliveryEstimate }}
                                    </p>
                                </div>
                                <p class="text-lg font-semibold text-blue-600">
                                    R$ {{ number_format($optionPrice, 2, ',', '.') }}
                                </p>
                            </div>
                        </label>
                    </div>
                @endforeach
                
                <!-- Campos ocultos para envio do formulário -->
                <input type="hidden" name="shipping_service_id" id="shipping-service-id">
                <input type="hidden" name="shipping_price" id="shipping-price">
                <input type="hidden" name="delivery_time" id="delivery-time">
                
                <!-- Botão de continuar -->
                <div class="pt-4 border-t border-gray-200 mt-6">
                    <div class="flex justify-between items-center">
                        <a 
                            href="{{ route('site.newcheckout.index', ['step' => 'address']) }}" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Voltar para Endereço
                        </a>
                        <button 
                            type="submit" 
                            id="continue-to-payment"
                            class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ empty($selectedShipping) ? 'disabled' : '' }}>
                            Continuar para Pagamento
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Atenção!</strong>
            <span class="block sm:inline">Não foi possível carregar as opções de frete. Por favor, verifique o endereço de entrega.</span>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const shippingForm = document.getElementById('shipping-form');
        const continueButton = document.getElementById('continue-to-payment');
        
        if (shippingForm) {
            // Preencher campos ocultos quando uma opção for selecionada
            const shippingOptions = shippingForm.querySelectorAll('input[type="radio"]');
            
            shippingOptions.forEach(option => {
                option.addEventListener('change', function() {
                    if (this.checked) {
                        document.getElementById('shipping-service-id').value = this.value;
                        document.getElementById('shipping-price').value = this.dataset.price;
                        document.getElementById('delivery-time').value = this.dataset.deliveryTime || 0;
                        
                        // Habilitar o botão de continuar
                        if (continueButton) {
                            continueButton.disabled = false;
                        }
                    }
                });
            });
            
            // Se já houver uma opção selecionada, garantir que os campos ocultos estejam preenchidos
            const selectedOption = shippingForm.querySelector('input[type="radio"]:checked');
            if (selectedOption) {
                document.getElementById('shipping-service-id').value = selectedOption.value;
                document.getElementById('shipping-price').value = selectedOption.dataset.price;
                document.getElementById('delivery-time').value = selectedOption.dataset.deliveryTime || 0;
                
                if (continueButton) {
                    continueButton.disabled = false;
                }
            }
        }
    });
</script>
@endpush