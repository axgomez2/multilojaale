<div class="summary-step">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Resumo do Pedido</h2>
    
    @if(!session('checkout_payment_method'))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Por favor, selecione um método de pagamento antes de continuar.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="flex justify-center mt-6">
            <a href="{{ route('site.newcheckout.index', ['step' => 'payment'], false) }}" 
               class="px-5 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-lg text-white font-medium transition-all duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Voltar para selecionar pagamento
            </a>
        </div>
    @else
        <div class="space-y-8">
            <!-- Produtos -->
            <div>
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white">
                    Produtos ({{ $cart->items->where('saved_for_later', false)->count() }})
                </h3>
                
                <div class="space-y-4">
                    @foreach($cart->items->where('saved_for_later', false) as $item)
                        <div class="flex items-start">
                            <div class="w-16 h-16 rounded overflow-hidden flex-shrink-0 bg-gray-100">
                                @if($item->vinylMaster && $item->vinylMaster->cover_image)
                                <img src="{{ asset('storage/' . $item->vinylMaster->cover_image) }}" 
                                     alt="{{ $item->vinylMaster->title }}" 
                                     class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <h4 class="text-base font-medium text-gray-800 dark:text-white">
                                    {{ $item->vinylMaster->title ?? 'Produto' }}
                                </h4>
                                
                                @if($item->vinylMaster && $item->vinylMaster->artists->isNotEmpty())
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ $item->vinylMaster->artists->pluck('name')->join(', ') }}
                                </p>
                                @endif
                                
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">
                                        Qtd: {{ $item->quantity }} x {{ 'R$ ' . number_format($item->vinylMaster->vinylSec->price ?? $item->price, 2, ',', '.') }}
                                    </span>
                                    <span class="text-base font-medium text-gray-800 dark:text-white">
                                        {{ 'R$ ' . number_format(($item->vinylMaster->vinylSec->price ?? $item->price) * $item->quantity, 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Endereço de entrega -->
            @php
                $addressId = session('checkout_address_id');
                $address = null;
                if (auth()->check() && $addressId) {
                    $address = auth()->user()->addresses()->find($addressId);
                }
            @endphp
            
            @if($address)
                <div>
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white">
                        Endereço de Entrega
                    </h3>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <p class="text-base font-medium text-gray-800 dark:text-white">
                            {{ $address->name }}
                        </p>
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
                    
                    <div class="mt-2 text-right">
                        <a href="{{ route('site.newcheckout.index', ['step' => 'address'], false) }}"
                           class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                            Alterar endereço
                        </a>
                    </div>
                </div>
            @endif
            
            <!-- Método de entrega -->
            @if(session('selected_shipping'))
                <div>
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white">
                        Método de Entrega
                    </h3>
                    
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                @php
                                    $shippingQuote = null;
                                    $quoteToken = session('shipping_quote_token');
                                    if ($quoteToken) {
                                        $shippingQuote = App\Models\ShippingQuote::where('quote_token', $quoteToken)->first();
                                    }
                                    
                                    $serviceName = '';
                                    $companyName = '';
                                    $companyPicture = '';
                                    if ($shippingQuote && $shippingQuote->options) {
                                        $services = $shippingQuote->options;
                                        foreach ($services as $service) {
                                            if ($service['id'] == session('selected_shipping.id')) {
                                                $serviceName = $service['name'];
                                                $companyName = $service['company_name'] ?? '';
                                                $companyPicture = $service['company_picture'] ?? '';
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                
                                <div class="flex items-center mb-2">
                                    @if($companyPicture)
                                        <img src="{{ $companyPicture }}" alt="{{ $companyName }}" class="h-6 mr-2 rounded">
                                    @endif
                                    <span class="text-base font-medium text-gray-800 dark:text-white">
                                        {{ $serviceName ?: 'Serviço de entrega' }}
                                    </span>
                                </div>
                                
                                @if(session('selected_shipping.delivery_time'))
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-blue-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Entrega em {{ session('selected_shipping.delivery_time') }} {{ session('selected_shipping.delivery_time') == 1 ? 'dia útil' : 'dias úteis' }}
                                    </div>
                                @endif
                            </div>
                            
                            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                {{ 'R$ ' . number_format(session('selected_shipping.price'), 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-2 text-right">
                        <a href="{{ route('site.newcheckout.index', ['step' => 'shipping'], false) }}"
                           class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                            Alterar frete
                        </a>
                    </div>
                </div>
            @endif
            
            <!-- Método de pagamento -->
            @if(session('checkout_payment_method'))
                <div>
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white">
                        Método de Pagamento
                    </h3>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        @php
                            $paymentMethod = session('checkout_payment_method');
                            $methodName = '';
                            $methodIcon = '';
                            $methodInfo = '';
                            
                            if ($paymentMethod == 'credit_card') {
                                $methodName = 'Cartão de Crédito';
                                $methodIcon = '<div class="flex space-x-2"><img src="https://cdn-icons-png.flaticon.com/128/196/196578.png" alt="Visa" class="h-6 w-auto"><img src="https://cdn-icons-png.flaticon.com/128/196/196561.png" alt="Mastercard" class="h-6 w-auto"></div>';
                                $installments = session('checkout_payment_installments', 1);
                                $installmentValue = ($cart->total + (session('selected_shipping.price') ?? 0)) / $installments;
                                $methodInfo = $installments . 'x de R$ ' . number_format($installmentValue, 2, ',', '.');
                                if ($installments >= 7) {
                                    $methodInfo .= ' (com juros)';
                                } else {
                                    $methodInfo .= ' (sem juros)';
                                }
                            } elseif ($paymentMethod == 'pix') {
                                $methodName = 'Pix';
                                $methodIcon = '<img src="https://logopng.com.br/logos/pix-106.png" alt="Pix" class="h-7 w-auto">';
                                $methodInfo = 'Pagamento instantâneo com 5% de desconto';
                            } elseif ($paymentMethod == 'boleto') {
                                $methodName = 'Boleto Bancário';
                                $methodIcon = '<svg class="h-7 w-7 text-gray-500" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/></svg>';
                                $methodInfo = 'Vencimento em 3 dias úteis com 3% de desconto';
                            }
                        @endphp
                        
                        <div class="flex justify-between items-center">
                            <p class="text-base font-medium text-gray-800 dark:text-white">
                                {{ $methodName }}
                            </p>
                            
                            {!! $methodIcon !!}
                        </div>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                            {{ $methodInfo }}
                        </p>
                    </div>
                    
                    <div class="mt-2 text-right">
                        <a href="{{ route('site.newcheckout.index', ['step' => 'payment'], false) }}"
                           class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                            Alterar forma de pagamento
                        </a>
                    </div>
                </div>
            @endif
            
            <!-- Totais -->
            <div>
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white">
                    Valor Total
                </h3>
                
                <div class="space-y-2">
                    <div class="flex justify-between text-gray-600 dark:text-gray-300">
                        <span>Subtotal</span>
                        <span>{{ 'R$ ' . number_format($cart->subtotal, 2, ',', '.') }}</span>
                    </div>
                    
                    @if(session('selected_shipping'))
                    <div class="flex justify-between text-gray-600 dark:text-gray-300">
                        <span>Frete</span>
                        <span>{{ 'R$ ' . number_format(session('selected_shipping.price'), 2, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    @php
                        $total = $cart->total + (session('selected_shipping.price') ?? 0);
                        $discount = 0;
                        $paymentMethod = session('checkout_payment_method');
                        
                        if ($paymentMethod == 'pix') {
                            $discount = $total * 0.05; // 5% de desconto
                        } elseif ($paymentMethod == 'boleto') {
                            $discount = $total * 0.03; // 3% de desconto
                        }
                        
                        $finalTotal = $total - $discount;
                    @endphp
                    
                    @if($discount > 0)
                    <div class="flex justify-between text-green-600 dark:text-green-400">
                        <span>Desconto</span>
                        <span>-{{ 'R$ ' . number_format($discount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between font-bold text-gray-800 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700 text-xl">
                        <span>Total</span>
                        <span>{{ 'R$ ' . number_format($finalTotal, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Botões de navegação -->
            <div class="flex justify-between mt-8">
                <a href="{{ route('site.newcheckout.index', ['step' => 'payment'], false) }}" 
                   class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg text-gray-800 dark:text-white font-medium transition-all duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Voltar para pagamento
                </a>
                
                <form action="{{ route('site.newcheckout.finalize') }}" method="POST" x-data="{ submitting: false }">
                    @csrf
                    <button type="submit" 
                            @click="submitting = true"
                            :disabled="submitting"
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 rounded-lg text-white font-medium transition-all duration-300 flex items-center">
                        <span x-show="!submitting">Finalizar Pedido</span>
                        <span x-show="submitting" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processando...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
