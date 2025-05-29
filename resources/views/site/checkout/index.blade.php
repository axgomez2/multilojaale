<x-app-layout>
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <h1 class="text-3xl font-bold mb-8 text-slate-800 ">Checkout</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Coluna principal de checkout (ocupa 2/3 em desktop) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Indicador de progresso com design moderno -->
                <div x-data="{ currentStep: '{{ $currentStep }}' }" class="mb-8">
                    <ol class="flex items-center w-full">
                        <li class="flex items-center" :class="['address', 'shipping', 'payment', 'confirmation'].includes(currentStep) ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400'">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full shrink-0" 
                                  :class="['address', 'shipping', 'payment', 'confirmation'].includes(currentStep) ? 'bg-blue-100 dark:bg-blue-800' : 'bg-gray-100 dark:bg-gray-700'">
                                <svg class="w-5 h-5" :class="['address', 'shipping', 'payment', 'confirmation'].includes(currentStep) ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400'" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" />
                                    <path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75v4.5a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198c.031-.028.062-.056.091-.086L12 5.43z" />
                                </svg>
                            </span>
                            <span class="ms-2 text-sm font-medium">Endereço</span>
                            <span class="hidden sm:flex w-12 h-1 ms-3 bg-gray-200 dark:bg-gray-700" :class="['shipping', 'payment', 'confirmation'].includes(currentStep) ? '!bg-blue-600 dark:!bg-blue-500' : ''"></span>
                        </li>
                        <li class="flex items-center" :class="['shipping', 'payment', 'confirmation'].includes(currentStep) ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400'">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full shrink-0" 
                                  :class="['shipping', 'payment', 'confirmation'].includes(currentStep) ? 'bg-blue-100 dark:bg-blue-800' : 'bg-gray-100 dark:bg-gray-700'">
                                <svg class="w-5 h-5" :class="['shipping', 'payment', 'confirmation'].includes(currentStep) ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400'" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 004.25 22.5h15.5a1.875 1.875 0 001.865-2.071l-1.263-12a1.875 1.875 0 00-1.865-1.679H16.5V6a4.5 4.5 0 10-9 0zM12 3a3 3 0 00-3 3v.75h6V6a3 3 0 00-3-3zm-3 8.25a3 3 0 106 0v-.75a.75.75 0 011.5 0v.75a4.5 4.5 0 11-9 0v-.75a.75.75 0 011.5 0v.75z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span class="ms-2 text-sm font-medium">Frete</span>
                            <span class="hidden sm:flex w-12 h-1 ms-3 bg-gray-200 dark:bg-gray-700" :class="['payment', 'confirmation'].includes(currentStep) ? '!bg-blue-600 dark:!bg-blue-500' : ''"></span>
                        </li>
                        <li class="flex items-center" :class="['payment', 'confirmation'].includes(currentStep) ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400'">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full shrink-0" 
                                  :class="['payment', 'confirmation'].includes(currentStep) ? 'bg-blue-100 dark:bg-blue-800' : 'bg-gray-100 dark:bg-gray-700'">
                                <svg class="w-5 h-5" :class="['payment', 'confirmation'].includes(currentStep) ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400'" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M4.5 3.75a3 3 0 00-3 3v.75h21v-.75a3 3 0 00-3-3h-15z" />
                                    <path fill-rule="evenodd" d="M22.5 9.75h-21v7.5a3 3 0 003 3h15a3 3 0 003-3v-7.5zm-18 3.75a.75.75 0 01.75-.75h6a.75.75 0 010 1.5h-6a.75.75 0 01-.75-.75zm.75 2.25a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span class="ms-2 text-sm font-medium">Pagamento</span>
                            <span class="hidden sm:flex w-12 h-1 ms-3 bg-gray-200 dark:bg-gray-700" :class="['confirmation'].includes(currentStep) ? '!bg-blue-600 dark:!bg-blue-500' : ''"></span>
                        </li>
                        <li class="flex items-center" :class="['confirmation'].includes(currentStep) ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400'">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full shrink-0" 
                                  :class="['confirmation'].includes(currentStep) ? 'bg-blue-100 dark:bg-blue-800' : 'bg-gray-100 dark:bg-gray-700'">
                                <svg class="w-5 h-5" :class="['confirmation'].includes(currentStep) ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400'" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span class="ms-2 text-sm font-medium">Confirmação</span>
                        </li>
                    </ol>
                </div>
                
                <!-- Conteúdo da etapa atual com design moderno -->
                <div class="bg-white  rounded-xl shadow-lg p-6 mb-6 border border-slate-200 dark:border-slate-700 transition-all duration-300 hover:shadow-xl">
                    <div x-data="{ loading: false }" class="relative">
                        <!-- Overlay de carregamento -->
                        <div x-show="loading" class="absolute inset-0 bg-white/80 dark:bg-slate-800/80 flex items-center justify-center rounded-xl z-10">
                            <div class="flex items-center space-x-2">
                                <div class="animate-spin h-8 w-8 border-4 border-blue-500 rounded-full border-t-transparent"></div>
                                <span class="text-slate-700 dark:text-slate-300 font-medium">Processando...</span>
                            </div>
                        </div>
                        
                        <!-- Conteúdo dinâmico -->
                        @if ($currentStep == 'address')
                            @include('site.checkout.partials.address')
                        @elseif ($currentStep == 'shipping')
                            @include('site.checkout.partials.shipping')
                        @elseif ($currentStep == 'payment')
                            @include('site.checkout.partials.payment')
                        @elseif ($currentStep == 'confirmation')
                            @include('site.checkout.partials.confirmation')
                        @endif
                    </div>
                </div>
                
                <!-- Botões de navegação com design moderno -->
                <div class="flex flex-wrap sm:flex-nowrap justify-between gap-4">
                    @if ($currentStep != 'address')
                        <form action="{{ route('site.checkout.previous-step') }}" method="POST" x-data="{ submitting: false }" novalidate>
                            @csrf
                            <input type="hidden" name="_prevent_query" value="1">
                            <button type="submit" 
                                    @click="submitting = true"
                                    :disabled="submitting"
                                    class="w-full sm:w-auto px-6 py-3 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg text-slate-700  font-medium transition-all duration-300 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                                <span>Voltar</span>
                                <span x-show="submitting" class="ml-2">
                                    <svg class="animate-spin h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('site.cart.index') }}" 
                           class="w-full sm:w-auto px-6 py-3 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg text-slate-700  font-medium transition-all duration-300 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                            </svg>
                            <span>Voltar para o carrinho</span>
                        </a>
                    @endif
                    
                    @if ($currentStep != 'confirmation')
                        <form action="{{ route('site.checkout.next-step') }}" method="POST" id="next-step-form" x-data="{ submitting: false }" novalidate>
                            @csrf
                            <input type="hidden" name="_prevent_query" value="1">
                            <button type="submit" 
                                    @click="submitting = true"
                                    :disabled="submitting"
                                    class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-lg text-white font-medium transition-all duration-300 flex items-center justify-center">
                                <span>
                                    @if ($currentStep == 'payment')
                                        Finalizar Pedido
                                    @else
                                        Continuar
                                    @endif
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                <span x-show="submitting" class="ml-2">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            
            <!-- Resumo do pedido com design moderno -->
            <div class="lg:col-span-1">
                <div x-data="{ expanded: false }" class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-6 sticky top-6 border border-slate-200 dark:border-slate-700 transition-all duration-300 hover:shadow-xl">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-slate-800 ">Resumo do Pedido</h2>
                        <button @click="expanded = !expanded" class="lg:hidden text-blue-600 dark:text-blue-400 flex items-center">
                            <span x-text="expanded ? 'Ocultar' : 'Mostrar'">Mostrar</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transition-transform" :class="expanded ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    
                    <div x-show="expanded || window.innerWidth >= 1024" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
                        <div class="space-y-4 divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach ($cart->items()->where('saved_for_later', false)->with('vinylMaster.vinylSec', 'vinylMaster.artists')->get() as $item)
                                <div class="py-4 flex items-start group">
                                    <div class="w-16 h-16 flex-shrink-0 overflow-hidden rounded-lg relative group-hover:scale-105 transition-transform duration-300">
                                        @if ($item->vinylMaster->cover_image)
                                            <img src="{{ asset('storage/' . $item->vinylMaster->cover_image) }}" alt="{{ $item->vinylMaster->title }}" class="w-full h-full object-cover rounded-lg">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-600 rounded-lg flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <h3 class="text-sm font-medium text-slate-800  group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">{{ $item->vinylMaster->title }}</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $item->vinylMaster->artists->pluck('name')->join(', ') }}</p>
                                        <div class="flex justify-between mt-2">
                                            <span class="text-sm text-slate-600 dark:text-slate-300">{{ $item->quantity }} x R$ {{ number_format($item->vinylMaster->vinylSec->price, 2, ',', '.') }}</span>
                                            <span class="text-sm font-medium text-slate-800 ">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="border-t border-slate-200 dark:border-slate-700 mt-4 pt-4 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600 dark:text-slate-300">Subtotal</span>
                                <span class="text-slate-800  font-medium">R$ {{ number_format($cart->subtotal, 2, ',', '.') }}</span>
                            </div>
                            
                            @if (isset($shippingQuote) && $shippingQuote->selected_price)
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600 dark:text-slate-300">Frete</span>
                                    <span class="checkout-shipping-price text-slate-800  font-medium">R$ {{ number_format($shippingQuote->selected_price, 2, ',', '.') }}</span>
                                </div>
                            @endif
                            
                            <div class="flex justify-between items-center pt-3 border-t border-slate-200 dark:border-slate-700">
                                <span class="text-lg font-bold text-slate-800 ">Total</span>
                                <span class="checkout-total-price text-lg font-bold text-blue-600 dark:text-blue-400">R$ {{ number_format($cart->subtotal + ($shippingQuote->selected_price ?? 0), 2, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <!-- Informações de segurança -->
                        <div class="mt-6 bg-slate-50 dark:bg-slate-900 rounded-lg p-3 flex items-start space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <div class="text-xs text-slate-600 dark:text-slate-400">
                                <p>Pagamento 100% seguro</p>
                                <p class="mt-1">Seus dados estão protegidos e a transação é criptografada.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            // Detectar mudanças de tamanho de tela para o resumo do pedido
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    document.querySelectorAll('[x-data="{ expanded: false }"]').forEach(el => {
                        if (el.__x) {
                            el.__x.$data.expanded = true;
                        }
                    });
                }
            });
            
            // Scripts específicos de cada etapa serão carregados dinamicamente
            Alpine.data('checkoutForm', () => ({
                loading: false,
                
                submitForm(formId) {
                    this.loading = true;
                    document.getElementById(formId).submit();
                }
            }));
        });
    </script>
    @endpush

    <x-address-modal route="site.checkout.addresses.store" />
</x-app-layout>
