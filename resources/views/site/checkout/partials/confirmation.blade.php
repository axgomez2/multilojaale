<h2 class="text-2xl font-bold mb-6">Confirmar Pedido</h2>

<div class="space-y-6">
    <!-- Resumo dos dados -->
    <div class="space-y-4">
        <!-- Endereço de entrega -->
        <div>
            <h3 class="text-lg font-semibold mb-2">Endereço de Entrega</h3>
            
            @if(session('checkout_address_id') && $address = \App\Models\Address::find(session('checkout_address_id')))
                <div class="bg-gray-50 p-4 rounded-md">
                    <div class="font-medium">{{ $address->name }}</div>
                    <div class="text-sm text-gray-600 mt-1">
                        {{ $address->street }}, {{ $address->number }}
                        @if ($address->complement)
                            - {{ $address->complement }}
                        @endif
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ $address->neighborhood }}
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ $address->city }} - {{ $address->state }}
                    </div>
                    <div class="text-sm text-gray-600">
                        CEP: {{ $address->zipcode }}
                    </div>
                </div>
                
                <div class="mt-2 text-sm">
                    <a href="{{ route('site.checkout.index') }}?step=address" class="text-primary hover:text-primary-dark">
                        Alterar endereço
                    </a>
                </div>
            @else
                <div class="bg-red-50 text-red-700 p-4 rounded-md">
                    Nenhum endereço selecionado. Por favor, <a href="{{ route('site.checkout.index') }}?step=address" class="text-red-700 underline">volte à etapa de endereço</a>.
                </div>
            @endif
        </div>
        
        <!-- Método de envio -->
        <div>
            <h3 class="text-lg font-semibold mb-2">Método de Envio</h3>
            
            @if(isset($shippingQuote) && $shippingQuote->selected_service_id)
                @php
                    $options = json_decode($shippingQuote->options, true);
                    $selectedOption = collect($options)->firstWhere('id', $shippingQuote->selected_service_id);
                @endphp
                
                @if($selectedOption)
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="flex justify-between">
                            <span class="font-medium">{{ $selectedOption['name'] }}</span>
                            <span class="font-medium">R$ {{ number_format($shippingQuote->selected_price, 2, ',', '.') }}</span>
                        </div>
                        <div class="text-sm text-gray-600 mt-1">
                            Entrega em {{ $selectedOption['delivery_time'] }} {{ $selectedOption['delivery_time'] == 1 ? 'dia útil' : 'dias úteis' }}
                        </div>
                        @if(isset($selectedOption['company']))
                            <div class="text-sm text-gray-600">
                                Transportadora: {{ $selectedOption['company']['name'] }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-2 text-sm">
                        <a href="{{ route('site.checkout.index') }}?step=shipping" class="text-primary hover:text-primary-dark">
                            Alterar método de envio
                        </a>
                    </div>
                @else
                    <div class="bg-red-50 text-red-700 p-4 rounded-md">
                        Opção de envio inválida. Por favor, <a href="{{ route('site.checkout.index') }}?step=shipping" class="text-red-700 underline">volte à etapa de frete</a>.
                    </div>
                @endif
            @else
                <div class="bg-red-50 text-red-700 p-4 rounded-md">
                    Nenhum método de envio selecionado. Por favor, <a href="{{ route('site.checkout.index') }}?step=shipping" class="text-red-700 underline">volte à etapa de frete</a>.
                </div>
            @endif
        </div>
        
        <!-- Forma de pagamento -->
        <div>
            <h3 class="text-lg font-semibold mb-2">Forma de Pagamento</h3>
            
            @if(session('checkout_payment_method'))
                <div class="bg-gray-50 p-4 rounded-md">
                    @if(session('checkout_payment_method') == 'credit_card')
                        <div class="font-medium">Cartão de Crédito</div>
                        <div class="text-sm text-gray-600 mt-1">
                            @if(session('checkout_payment_token'))
                                Pagamento processado com cartão
                            @endif
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ session('checkout_payment_installments', 1) }}x de R$ {{ number_format(($cart->subtotal + ($shippingQuote->selected_price ?? 0)) / session('checkout_payment_installments', 1), 2, ',', '.') }}
                        </div>
                    @elseif(session('checkout_payment_method') == 'pix')
                        <div class="font-medium">PIX</div>
                        <div class="text-sm text-gray-600 mt-1">
                            Valor com desconto: R$ {{ number_format(($cart->subtotal + ($shippingQuote->selected_price ?? 0)) * 0.95, 2, ',', '.') }}
                        </div>
                        <div class="text-sm text-gray-600">
                            Você receberá o QR Code para pagamento após finalizar o pedido.
                        </div>
                    @elseif(session('checkout_payment_method') == 'boleto')
                        <div class="font-medium">Boleto Bancário</div>
                        <div class="text-sm text-gray-600 mt-1">
                            Valor: R$ {{ number_format($cart->subtotal + ($shippingQuote->selected_price ?? 0), 2, ',', '.') }}
                        </div>
                        <div class="text-sm text-gray-600">
                            Você receberá o boleto para impressão ou pagamento online após finalizar o pedido.
                        </div>
                    @endif
                </div>
                
                <div class="mt-2 text-sm">
                    <a href="{{ route('site.checkout.index') }}?step=payment" class="text-primary hover:text-primary-dark">
                        Alterar forma de pagamento
                    </a>
                </div>
            @else
                <div class="bg-red-50 text-red-700 p-4 rounded-md">
                    Nenhuma forma de pagamento selecionada. Por favor, <a href="{{ route('site.checkout.index') }}?step=payment" class="text-red-700 underline">volte à etapa de pagamento</a>. Selecione uma forma de pagamento para prosseguir com o pedido.
                </div>
            @endif
        </div>
    </div>

    <!-- Resumo dos itens do pedido -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Itens do Pedido</h3>
        
        <div class="bg-gray-50 p-4 rounded-md">
            <!-- Produtos -->
            <div class="divide-y divide-gray-200">
                @foreach ($cart->items as $item)
                    <div class="py-4 flex space-x-4">
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded overflow-hidden">
                            @if ($item->vinylMaster && $item->vinylMaster->cover_image)
                                <img src="{{ asset('storage/' . $item->vinylMaster->cover_image) }}" alt="{{ $item->vinylMaster->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <div class="font-medium">{{ $item->vinylMaster->title ?? 'Produto' }}</div>
                            <div class="text-sm text-gray-600">{{ $item->vinylMaster->artists->pluck('name')->join(', ') }}</div>
                            <div class="text-sm text-gray-500 mt-1">Quantidade: {{ $item->quantity }}</div>
                        </div>
                        
                        <div class="text-right">
                            @php
                                $itemPrice = $item->vinylMaster->vinylSec->price ?? $item->price;
                                $itemTotal = $itemPrice * $item->quantity;
                            @endphp
                            <div class="font-medium">R$ {{ number_format($itemTotal, 2, ',', '.') }}</div>
                            <div class="text-sm text-gray-500">R$ {{ number_format($itemPrice, 2, ',', '.') }} cada</div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Totais -->
            <div class="border-t border-gray-200 mt-4 pt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal</span>
                    <span>R$ {{ number_format($cart->subtotal, 2, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Frete</span>
                    <span>R$ {{ number_format($shippingQuote->selected_price ?? 0, 2, ',', '.') }}</span>
                </div>
                
                @if(session('checkout_payment_method') == 'pix')
                    <div class="flex justify-between text-sm text-green-600">
                        <span>Desconto PIX (5%)</span>
                        <span>-R$ {{ number_format(($cart->subtotal + ($shippingQuote->selected_price ?? 0)) * 0.05, 2, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between font-bold border-t border-gray-200 pt-2 mt-2">
                        <span>Total</span>
                        <span>R$ {{ number_format(($cart->subtotal + ($shippingQuote->selected_price ?? 0)) * 0.95, 2, ',', '.') }}</span>
                    </div>
                @else
                    <div class="flex justify-between font-bold border-t border-gray-200 pt-2 mt-2">
                        <span>Total</span>
                        <span>R$ {{ number_format($cart->subtotal + ($shippingQuote->selected_price ?? 0), 2, ',', '.') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Finalizar pedido -->
    <div class="mt-8">
        <form action="{{ route('site.orders.store') }}" method="POST" id="create-order-form">
            @csrf
            <input type="hidden" name="address_id" value="{{ session('checkout_address_id') }}">
            <input type="hidden" name="shipping_quote_token" value="{{ $shippingQuote->quote_token ?? '' }}">
            
            <div class="space-y-4">
                <div class="text-sm text-gray-500">
                    <p>Ao finalizar o pedido, você concorda com os <a href="#" class="text-primary underline">Termos e Condições</a> e <a href="#" class="text-primary underline">Política de Privacidade</a>.</p>
                </div>
                
                <button type="submit" class="w-full py-3 px-4 bg-primary text-white rounded-md hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Finalizar Pedido
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const createOrderForm = document.getElementById('create-order-form');
        
        createOrderForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Verificar se todos os dados necessários estão presentes
            const addressId = this.elements.address_id.value;
            const shippingQuoteToken = this.elements.shipping_quote_token.value;
            
            if (!addressId || !shippingQuoteToken) {
                alert('Por favor, complete todas as etapas do checkout antes de finalizar o pedido.');
                return;
            }
            
            // Desabilitar o botão de envio para evitar múltiplos cliques
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = 'Processando...';
            
            // Enviar o formulário via AJAX
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    address_id: addressId,
                    shipping_quote_token: shippingQuoteToken
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirecionar para a página de pagamento
                    window.location.href = data.redirect;
                } else {
                    // Mostrar mensagem de erro
                    alert(data.message || 'Erro ao criar o pedido. Por favor, tente novamente.');
                    
                    // Reabilitar o botão
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Finalizar Pedido';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao criar o pedido. Por favor, tente novamente.');
                
                // Reabilitar o botão
                submitButton.disabled = false;
                submitButton.innerHTML = 'Finalizar Pedido';
            });
        });
    });
</script>
@endpush
