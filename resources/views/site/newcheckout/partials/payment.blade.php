<div class="payment-step">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Método de Pagamento</h2>
    
    @if(!session('selected_shipping'))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Por favor, selecione uma opção de frete antes de continuar.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="flex justify-center mt-6">
            <a href="{{ route('site.newcheckout.index', ['step' => 'shipping'], false) }}" 
               class="px-5 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-lg text-white font-medium transition-all duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Voltar para selecionar frete
            </a>
        </div>
    @else
        <form id="payment-form" action="{{ route('site.newcheckout.process-payment') }}" method="POST" x-data="{ paymentMethod: '{{ session('checkout_payment_method', '') }}', showInstallments: false }">
            @csrf
            
            <div class="space-y-6">
                <!-- Cartão de Crédito -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden transition-all duration-200"
                     :class="{ 'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-800': paymentMethod === 'credit_card' }">
                    <label class="block cursor-pointer">
                        <div class="flex items-start p-4">
                            <input type="radio" name="payment_method" value="credit_card" 
                                   class="form-radio h-5 w-5 text-blue-600 mt-1"
                                   x-model="paymentMethod"
                                   @change="showInstallments = (paymentMethod === 'credit_card')"
                                   required>
                            <div class="ml-3 w-full">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-800 dark:text-white">Cartão de Crédito</span>
                                    <div class="flex space-x-2">
                                        <img src="https://cdn-icons-png.flaticon.com/128/196/196578.png" alt="Visa" class="h-6 w-auto">
                                        <img src="https://cdn-icons-png.flaticon.com/128/196/196561.png" alt="Mastercard" class="h-6 w-auto">
                                        <img src="https://cdn-icons-png.flaticon.com/128/196/196539.png" alt="American Express" class="h-6 w-auto">
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    Pague com seu cartão de crédito em até 12x
                                </p>
                            </div>
                        </div>
                        
                        <!-- Opções de parcelamento (aparecem apenas quando o cartão de crédito é selecionado) -->
                        <div x-show="paymentMethod === 'credit_card'" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             class="px-4 pb-4 pt-2 border-t border-gray-200 dark:border-gray-700">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Número de parcelas:
                            </label>
                            <select name="installments" 
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-800 dark:text-white">
                                @php
                                    $orderTotal = $cart->total + (session('selected_shipping')['price'] ?? 0);
                                    $maxInstallments = min(12, max(1, floor($orderTotal / 10))); // Mínimo de R$10 por parcela
                                @endphp
                                
                                @for ($i = 1; $i <= $maxInstallments; $i++)
                                    @php
                                        $installmentValue = $orderTotal / $i;
                                        // Aplica juros a partir da 7ª parcela (exemplo)
                                        if ($i >= 7) {
                                            $interestRate = 0.0199; // 1.99% ao mês
                                            $installmentValue = $orderTotal * (($interestRate * pow(1 + $interestRate, $i)) / (pow(1 + $interestRate, $i) - 1));
                                        }
                                    @endphp
                                    <option value="{{ $i }}" {{ session('checkout_payment_installments', 1) == $i ? 'selected' : '' }}>
                                        {{ $i }}x de R$ {{ number_format($installmentValue, 2, ',', '.') }}
                                        @if ($i >= 7)
                                            (com juros)
                                        @else
                                            (sem juros)
                                        @endif
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </label>
                </div>
                
                <!-- Pix -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden transition-all duration-200"
                     :class="{ 'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-800': paymentMethod === 'pix' }">
                    <label class="flex items-start p-4 cursor-pointer">
                        <input type="radio" name="payment_method" value="pix" 
                               class="form-radio h-5 w-5 text-blue-600 mt-1"
                               x-model="paymentMethod"
                               @change="showInstallments = false"
                               required>
                        <div class="ml-3 w-full">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-800 dark:text-white">Pix</span>
                                <img src="https://logopng.com.br/logos/pix-106.png" alt="Pix" class="h-7 w-auto">
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                Pagamento instantâneo - Aprovação imediata
                            </p>
                            <p class="text-sm font-medium text-green-600 dark:text-green-400 mt-1">
                                5% de desconto
                            </p>
                        </div>
                    </label>
                </div>
                
                <!-- Boleto -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden transition-all duration-200"
                     :class="{ 'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-800': paymentMethod === 'boleto' }">
                    <label class="flex items-start p-4 cursor-pointer">
                        <input type="radio" name="payment_method" value="boleto" 
                               class="form-radio h-5 w-5 text-blue-600 mt-1"
                               x-model="paymentMethod"
                               @change="showInstallments = false"
                               required>
                        <div class="ml-3 w-full">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-800 dark:text-white">Boleto Bancário</span>
                                <svg class="h-7 w-7 text-gray-500" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                Vencimento em 3 dias úteis
                            </p>
                            <p class="text-sm font-medium text-green-600 dark:text-green-400 mt-1">
                                3% de desconto
                            </p>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Informação de segurança -->
            <div class="mt-8 flex items-start">
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <p class="ml-2 text-sm text-gray-600 dark:text-gray-300">
                    Todas as transações são seguras e criptografadas. Seus dados pessoais nunca são compartilhados.
                </p>
            </div>
            
            <!-- Botões de navegação -->
            <div class="flex justify-between mt-8">
                <a href="{{ route('site.newcheckout.index', ['step' => 'shipping'], false) }}" 
                   class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg text-gray-800 dark:text-white font-medium transition-all duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Voltar para entrega
                </a>
                
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-lg text-white font-medium transition-all duration-300 flex items-center"
                        :disabled="!paymentMethod"
                        :class="{ 'opacity-50 cursor-not-allowed': !paymentMethod }">
                    Continuar para revisão
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </form>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Para garantir que o botão de continuar seja habilitado/desabilitado corretamente
        const paymentForm = document.getElementById('payment-form');
        if (paymentForm) {
            const continueButton = paymentForm.querySelector('button[type="submit"]');
            
            function checkSelectedPayment() {
                const selectedPayment = paymentForm.querySelector('input[name="payment_method"]:checked');
                if (continueButton) {
                    continueButton.disabled = !selectedPayment;
                    continueButton.classList.toggle('opacity-50', !selectedPayment);
                    continueButton.classList.toggle('cursor-not-allowed', !selectedPayment);
                }
            }
            
            // Verificar no carregamento inicial
            checkSelectedPayment();
            
            // Verificar quando uma opção é selecionada
            const paymentRadios = paymentForm.querySelectorAll('input[name="payment_method"]');
            paymentRadios.forEach(radio => {
                radio.addEventListener('change', checkSelectedPayment);
            });
        }
    });
</script>
@endpush
