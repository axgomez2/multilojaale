<div class="address-step">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Endereço de Entrega</h2>
    
    @if($addresses->isEmpty())
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Você ainda não tem endereços cadastrados. Por favor, adicione um endereço para continuar.
                    </p>
                </div>
            </div>
        </div>
    @endif
    
    <form id="address-form" action="{{ route('site.newcheckout.process-address') }}" method="POST" x-data="{ selectedAddressId: '{{ session('checkout_address_id', '') }}', formSubmitting: false }">
        @csrf
        
        <div class="grid grid-cols-1 gap-6 mb-8">
            <!-- Endereços existentes -->
            @if($addresses->isNotEmpty())
                <div class="space-y-4">
                    @foreach($addresses as $address)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden transition-all duration-200"
                             :class="{ 'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-800': selectedAddressId === '{{ $address->id }}' }">
                            <label class="flex items-start p-4 cursor-pointer">
                                <input type="radio" name="address_id" value="{{ $address->id }}" 
                                       class="form-radio h-5 w-5 text-blue-600 mt-1"
                                       x-model="selectedAddressId"
                                       required>
                                <div class="ml-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-lg font-semibold text-gray-800 dark:text-white">{{ $address->name }}</span>
                                        @if($address->is_default)
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">Padrão</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                        {{ $address->recipient_name }} • {{ $address->recipient_phone }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                        {{ $address->street }}, {{ $address->number }}
                                        @if($address->complement) - {{ $address->complement }} @endif
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        {{ $address->district }}, {{ $address->city }} - {{ $address->state }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        CEP: {{ $address->zipcode }}
                                    </p>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif
            
            <!-- Adicionar novo endereço -->
            <div class="mt-4">
                <button type="button" 
                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center"
                        data-modal-target="address-modal" 
                        data-modal-toggle="address-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Adicionar novo endereço
                </button>
            </div>
        </div>
        
        <!-- Botão de continuar -->
        <div class="flex justify-end mt-8">
            <button type="submit" 
                    @click="if(selectedAddressId) { formSubmitting = true; $event.target.closest('form').submit(); }"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-lg text-white font-medium transition-all duration-300 flex items-center"
                    :disabled="!selectedAddressId || formSubmitting"
                    :class="{ 'opacity-50 cursor-not-allowed': !selectedAddressId || formSubmitting }">
                <span x-show="!formSubmitting">Continuar para entrega</span>
                <span x-show="formSubmitting" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processando...
                </span>
                <svg x-show="!formSubmitting" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </form>
    
    <!-- Incluir o modal de endereço -->
    @include('components.address-modal', ['redirectTo' => route('site.newcheckout.index', ['step' => 'address'], false)])
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Adicionar interatividade ao modal de endereço
        const addressModal = document.getElementById('address-modal');
        if (addressModal) {
            // Garantir que o modal tem o redirecionamento correto
            const redirectInput = addressModal.querySelector('input[name="redirect_to"]');
            if (redirectInput) {
                redirectInput.value = '{{ route("site.newcheckout.index", ["step" => "address"], false) }}';
            }
        }
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        // Para garantir que o botão de continuar funcione corretamente
        const addressForm = document.getElementById('address-form');
        if (addressForm) {
            addressForm.addEventListener('submit', function(e) {
                // Verificar se um endereço foi selecionado
                const selectedAddress = addressForm.querySelector('input[name="address_id"]:checked');
                if (!selectedAddress) {
                    e.preventDefault();
                    alert('Por favor, selecione um endereço de entrega para continuar.');
                    return false;
                }
                
                // Se tudo estiver correto, permitir o envio e mostrar o indicador de carregamento
                if (typeof Alpine !== 'undefined') {
                    const addressFormComponent = Alpine.getRoot(addressForm);
                    if (addressFormComponent) {
                        addressFormComponent.$data.formSubmitting = true;
                    }
                }
                
                return true;
            });
        }
    });
</script>
@endpush
