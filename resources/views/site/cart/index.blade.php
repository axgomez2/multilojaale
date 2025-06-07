<x-app-layout>


    <!-- cabeçalho do carrinho -->
<div class="max-w-6xl max-lg:max-w-2xl mx-auto p-4">
  <h1 class="text-xl font-semibold text-slate-900">Meu Carrinho:  
    <span class="text-gray-600 text-sm mt-1">{{ $cartItems->count() }} {{ $cartItems->count() == 1 ? 'item' : 'itens' }} ({{ $cartItems->sum('quantity') }} {{ $cartItems->sum('quantity') == 1 ? 'unidade' : 'unidades' }})</span>
  </h1>
    
@if(count($cartItems) > 0)
    
  <form action="{{ route('site.cart.clear') }}" method="POST" class="inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center">
      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
      </svg>
      Limpar carrinho
    </button>
  </form>

@endif


<div class="max-w-7xl max-lg:max-w-2xl mx-auto p-4">
         
          <div class="grid lg:grid-cols-3 lg:gap-x-8 gap-x-6 gap-y-8 mt-6">
              <div class="lg:col-span-2 space-y-6">
                <!-- Script para wishlist e controle de quantidade -->
                  @if(count($cartItems) > 0 || count($savedItems) > 0)
                      <div class="lg:flex lg:space-x-6">
                      @if(count($cartItems) > 0)
                  
                  <div class="space-y-6">
                  @foreach($cartItems as $item)
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:p-6">
                      <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                        <a href="{{ route('site.vinyl.show', [$item->vinylMaster->artists->first()->slug, $item->vinylMaster->slug]) }}" class="shrink-0 md:order-1">
                          <img class="h-32 w-32  dark:hidden" src="{{ $item->vinylMaster->cover_image }}" alt="vinyl image" />
                          <img class="hidden h-32 w-32 dark:block" src="{{ $item->vinylMaster->cover_image }}" alt="vinyl image" />
                        </a>
  
                        <label for="counter-input" class="sr-only">Choose quantity:</label>
                        <div class="flex items-center justify-between md:order-3 md:justify-end">
                          <form action="{{ route('site.cart.update', $item->id) }}" method="POST" class="flex items-center" id="update-form-{{ $item->id }}">
                            @csrf
                            @method('PUT')
                            <button type="button" onclick="decrementQuantity('{{ $item->id }}')" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                              <svg class="h-2.5 w-2.5 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                              </svg>
                            </button>
                            <input type="number" name="quantity" id="quantity-{{ $item->id }}" class="w-10 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0" value="{{ $item->quantity }}" min="1" max="{{ min(10, $item->vinylMaster->vinylSec->stock) }}" required readonly />
                            <button type="button" onclick="incrementQuantity('{{ $item->id }}')" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                              <svg class="h-2.5 w-2.5 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                              </svg>
                            </button>
                          </form>
                          <div class="text-end md:order-4 md:w-32">
                            <p class="text-base font-bold text-gray-900">R$ {{ number_format($item->vinylMaster->vinylSec->price, 2, ',', '.') }}</p>
                            @if($item->vinylMaster->vinylSec->original_price > $item->vinylMaster->vinylSec->price)
                              <p class="text-xs text-gray-500 line-through">R$ {{ number_format($item->vinylMaster->vinylSec->original_price, 2, ',', '.') }}</p>
                            @endif
                          </div>
                        </div>
  
                        <div class="w-full min-w-0 flex-1 space-y-4 md:order-2 md:max-w-md">
                          <a href="#" class="text-base font-medium text-gray-900 hover:underline ">{{ $item->vinylMaster->title }}</a>
                          <p>{{ $item->vinylMaster->artists->first()->name }}</p>
                          <div class="flex items-center gap-4">
                            <button type="button" onclick="toggleWishlist('{{ $item->vinylMaster->id }}', this)" data-vinyl-id="{{ $item->vinylMaster->id }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 hover:underline">
                              <svg class="me-1.5 h-5 w-5 wishlist-icon {{ in_array($item->vinylMaster->id, $wishlistItems ?? []) ? 'text-red-600' : '' }}" 
                                  aria-hidden="true" 
                                  xmlns="http://www.w3.org/2000/svg" 
                                  width="24" 
                                  height="24" 
                                  fill="{{ in_array($item->vinylMaster->id, $wishlistItems ?? []) ? 'currentColor' : 'none' }}" 
                                  viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.01 6.001C6.5 1 1 8 5.782 13.001L12.011 20l6.23-7C23 8 17.5 1 12.01 6.002Z" />
                              </svg>
                              {{ in_array($item->vinylMaster->id, $wishlistItems ?? []) ? 'Remover dos favoritos' : 'Adicionar aos favoritos' }}
                            </button>
  
                            <form action="{{ route('site.cart.save-for-later', $item->id) }}" method="POST" class="inline">
                              @csrf
                              @method('PUT')
                              <button type="submit" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 hover:underline">
                                <svg class="me-1.5 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v13m0-13 4-4m-4 4-4-4M6 10h12"/>
                                </svg>
                                Salvar para depois
                              </button>
                            </form>
   
                            <form action="{{ route('site.cart.remove', $item->id) }}" method="POST" class="inline">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="inline-flex items-center text-sm font-medium text-red-600 hover:underline">
                                <svg class="me-1.5 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                </svg>
                                Remover
                              </button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                  </div>
                @endif  
                      </div>
                  @else
                      <!-- Mensagem de carrinho vazio -->
                      <div class="bg-white rounded-lg shadow-md p-8 text-center mb-8">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                          </svg>
                          <h2 class="text-2xl font-bold mb-2">Seu carrinho está vazio</h2>
                          <p class="text-gray-600 mb-6">Parece que você ainda não adicionou nenhum item ao seu carrinho.</p>
                          
                          <a href="{{ route('home') }}" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition-colors inline-block">Voltar para o site</a>
                      </div>
                  @endif

                  <!-- Verificar se há itens salvos para depois - independente do carrinho estar vazio ou não -->
                  @if(count($savedItems) > 0)
                      <div class="bg-white rounded-lg shadow-md p-6 mt-8">
                          <div class="flex justify-between items-center border-b pb-4 mb-4">
                              <h2 class="text-xl font-semibold">Itens Salvos Para Depois ({{ count($savedItems) }})</h2>
                              <div class="text-sm text-gray-600">
                                  <span class="italic">Itens salvados não são incluídos no total do carrinho</span>
                              </div>
                          </div>
                          
                          @foreach($savedItems as $item)
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                  <div class="flex flex-col sm:flex-row items-start gap-4">
                    <a href="{{ route('site.vinyl.show', [$item->vinylMaster->artists->first()->slug, $item->vinylMaster->slug]) }}" class="shrink-0">
                      <img src="{{ $item->vinylMaster->cover_image }}" alt="{{ $item->vinylMaster->title }}" class="w-24 h-24 object-cover rounded">
                    </a>
                    <div class="flex-1">
                      <a href="{{ route('site.vinyl.show', [$item->vinylMaster->artists->first()->slug, $item->vinylMaster->slug]) }}" class="font-semibold hover:text-purple-600">
                        {{ $item->vinylMaster->title }}
                      </a>
                      <p class="text-gray-600 text-sm">{{ $item->vinylMaster->artists->first()->name }}</p>
                      <p class="mt-2">
                        <span class="{{ $item->vinylMaster->isAvailable() ? 'text-green-600' : 'text-red-600' }} text-sm">
                          {{ $item->vinylMaster->isAvailable() ? 'Disponível' : 'Indisponível' }}
                        </span>
                      </p>
                      <p class="font-semibold mt-2">R$ {{ number_format($item->vinylMaster->vinylSec->price, 2, ',', '.') }}</p>
                      <div class="flex flex-wrap gap-2 mt-3">
                        @if($item->vinylMaster->isAvailable())
                          <form action="{{ route('site.cart.move-to-cart', $item->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="text-white bg-purple-600 hover:bg-purple-700 px-3 py-1 rounded text-sm">
                              Mover ao carrinho
                            </button>
                          </form>
                        @endif
                        <form action="{{ route('site.cart.remove', $item->id) }}" method="POST" class="inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="text-red-600 hover:text-red-800 border border-red-600 hover:border-red-800 px-3 py-1 rounded text-sm">
                            Remover
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
                      </div>
                  @endif
              </div>

              <div class="bg-white rounded-md px-4 py-6 h-max shadow-sm border border-gray-200">
                  <ul class="text-slate-500 font-medium space-y-4">
                  <li class="flex flex-wrap gap-4 text-sm">
                  <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 sm:p-6">
          <form class="space-y-4" action="{{ route('site.cart.apply-coupon') }}" method="POST">
            @csrf
            <div>
              <label for="coupon_code" class="mb-2 block text-sm font-medium text-gray-900">Você possui um cupom de desconto?</label>
              <input 
                type="text" 
                id="coupon_code" 
                name="coupon_code"
                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-purple-500 focus:ring-purple-500" 
                placeholder="Digite seu código aqui" 
                required 
              />
            </div>
            <button 
              type="submit" 
              class="flex w-full items-center justify-center rounded-lg bg-purple-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-4 focus:ring-purple-300"
            >
              Aplicar Cupom
            </button>
          </form>
        </div>

                    </li>

                  
                    
                      <li class="flex flex-wrap gap-4 text-sm">Subtotal <span class="ml-auto font-semibold text-slate-900">R$ {{ number_format($cartTotal, 2, ',', '.') }}</span></li>
                      
                      @if(isset($discount) && $discount > 0)
                      <li class="flex flex-wrap gap-4 text-sm">Desconto: <span class="ml-auto font-semibold text-red-600">-R$ {{ number_format($discount, 2, ',', '.') }}</span></li>
                      @endif

                      <li class="flex flex-wrap gap-4 text-sm">
                        <h3 class="font-medium mb-3">Calcular Frete</h3>
                  
                        <form action="{{ route('site.cart.calculate-shipping') }}" method="POST" class="mb-4">
                          @csrf
                          <div class="flex">
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
                            @if(isset($selectedShipping) && $selectedShipping)
                              <button type="button" 
                                onclick="ShippingModal.open()" 
                                class="bg-white text-purple-600 border border-purple-600 hover:bg-purple-50 px-4 py-2 rounded-r text-sm font-medium transition-colors">
                                Alterar
                              </button>
                            @else
                              <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-r text-sm font-medium">
                                Calcular
                              </button>
                            @endif
                          </div>
                        </form>
                      </li>

                      <li class="flex flex-wrap gap-4 text-sm">
                      @if(auth()->check())
                        @php
                          $userAddresses = auth()->user()->addresses ?? collect([]);
                        @endphp
                        
                        @if($userAddresses->count() > 0)
                          <div class="mt-3">
                            <h4 class="text-sm font-medium mb-2">Seus endereços:</h4>
                            <div class="space-y-2">
                              @foreach($userAddresses as $address)
                                <div class="border rounded p-2 text-sm address-item {{ isset($zipCode) && $zipCode == $address->zipcode ? 'border-purple-500 bg-purple-50' : '' }}" data-zipcode="{{ preg_replace('/\D/', '', $address->zipcode) }}">
                                  <div class="flex justify-between items-start">
                                    <div>
                                      <p class="font-medium">{{ $address->name }}</p>
                                      <p class="text-gray-600">{{ $address->street }}, {{ $address->number }}</p>
                                      <p class="text-gray-600">{{ $address->neighborhood }} - {{ $address->city }}/{{ $address->state }}</p>
                                      <p class="text-gray-600">CEP: {{ $address->zipcode }}</p>
                                    </div>
                                    <button type="button" onclick="useAddress('{{ $address->zipcode }}')" class="text-sm text-purple-600 hover:text-purple-800">
                                      Usar
                                    </button>
                                  </div>
                                </div>
                              @endforeach
                            </div>
                          </div>
                        @else
                          <div class="mt-3 p-3 bg-gray-50 rounded text-sm">
                            <p>Você ainda não possui endereços cadastrados.</p>
                            <button type="button" onclick="openAddressModal()" class="mt-2 text-purple-600 hover:text-purple-800 font-medium">
                              + Adicionar endereço
                            </button>
                          </div>
                        @endif
                      @endif

                      </li>
                      <!-- Seção de cálculo de frete -->
                      <div class="mb-3 mt-4" id="shipping-section">
                                        @if(empty($shippingOptions))
                                          <!-- Formulário de cálculo quando não há opções -->
                                          
                                          
                                          @if(session('shipping_calculation') && !session('shipping_calculation.success'))
                                            <div class="text-red-600 text-sm my-2">
                                              {{ session('shipping_calculation.message') }}
                                            </div>
                                          @endif
                                          
                                        @else
                                          <!-- Exibir opções de frete quando disponíveis -->
                                          <div class="flex justify-between items-center mb-3">
                                            <h4 class="text-sm font-medium">Opções de frete para {{ $zipCode ? substr_replace($zipCode, '-', 5, 0) : 'seu CEP' }}:</h4>
                                          </div>
                                          <div class="space-y-2">
                                            @foreach($shippingOptions as $option)
                                              @php
                                                $isSelected = isset($selectedShipping) && $selectedShipping['id'] == $option['id'];
                                              @endphp
                                              <form action="{{ route('site.cart.select-shipping') }}" method="POST" class="mb-0">
                                                @csrf
                                                <input type="hidden" name="shipping_option" value="{{ $option['id'] ?? '' }}">
                                                <button type="submit" class="w-full text-left cursor-pointer transition-colors duration-200 {{ $isSelected ? 'bg-green-50 border-green-200' : 'bg-white hover:bg-gray-50 border-gray-200' }} p-3 rounded border block">
                                                  <div class="flex items-center justify-between">
                                                    <div>
                                                      <p class="font-medium">{{ $option['name'] ?? $option['title'] ?? 'Opção de frete' }}</p>
                                                      <p class="text-xs text-gray-500">{{ $option['delivery_estimate'] ?? ($option['delivery_time'] ? ($option['delivery_time'] . ' ' . ($option['delivery_time'] == 1 ? 'dia útil' : 'dias úteis')) : 'Prazo a calcular') }}</p>
                                                    </div>
                                                    <div class="flex items-center">
                                                      <span class="font-medium">{{ $option['formatted_price'] ?? ('R$ ' . number_format($option['price'] ?? 0, 2, ',', '.')) }}</span>
                                                      @if($isSelected)
                                                        <svg class="w-5 h-5 ml-2 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                      @endif
                                                    </div>
                                                  </div>
                                                </button>
                                              </form>
                                            @endforeach
                                          </div>
                                          
                                          @if(!count($shippingOptions))
                                            <div class="text-center p-4 bg-gray-50 rounded">
                                              <p class="text-gray-500">Nenhuma opção de frete disponível. Por favor, verifique o CEP informado.</p>
                                            </div>
                                          @endif
                                        @endif
                                      <!-- Mensagem de erro fora das condições principais, será exibida apenas quando necessário -->
                                      @if(session('shipping_calculation') && !session('shipping_calculation.success') && empty($shippingOptions))
                                        <div class="text-red-600 text-sm my-2">
                                          {{ session('shipping_calculation.message') }}
                                        </div>
                                      @endif
                                      </div>

                                      
                      <li class="flex flex-wrap gap-4 text-sm">
                         <!-- Frete selecionado -->
                        <dl class="flex items-center justify-between gap-4">
                          <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Frete</dt>
                          <dd class="text-base font-normal text-gray-900 flex justify-between items-center" id="shipping-cost">
                            @if(isset($selectedShipping))
                              <span>{{ $selectedShipping['name'] ?? 'Frete selecionado' }} - {{ $selectedShipping['delivery_estimate'] ?? 'Prazo a calcular' }}</span>
                              <span>R$ {{ number_format($selectedShipping['price'] ?? 0, 2, ',', '.') }}</span>
                            @else
                              <span>Calculado no checkout</span>
                            @endif
                          </dd>
                        </dl>


                      </li>


                      <li class="flex flex-wrap gap-4 text-sm">
                        
                      

                        </li>


                        <li class="flex flex-wrap gap-4 text-sm">
                        


                        </li>

                        <li class="flex flex-wrap gap-4 text-sm">
                        


                        </li>
                      <hr class="border-slate-300" />
                      <li class="flex flex-wrap gap-4 text-sm font-semibold text-slate-900">Total <span class="ml-auto">@php
                  $totalValue = $cartTotal;
                  if (isset($selectedShipping)) {
                    $totalValue += $selectedShipping['price'] ?? 0;
                  }
                  if (isset($discount) && $discount > 0) {
                    $totalValue -= $discount;
                  }
                @endphp
                R$ {{ number_format($totalValue, 2, ',', '.') }}</span>
                  </li>
              </ul>
                  <div class="mt-8 space-y-4">
                  <form action="{{ route('site.checkout.index') }}" method="GET" id="checkout-form">
                    <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-purple-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-4 focus:ring-purple-300 {{ isset($selectedShipping) ? '' : 'opacity-50 cursor-not-allowed' }}" {{ isset($selectedShipping) ? '' : 'disabled' }}>
                      Finalizar Compra
                    </button>
                  </form>


                  <span class="text-sm font-normal text-gray-500 dark:text-gray-400">ou</span>
                    <a href="{{ route('home') }}" title="" class="inline-flex items-center gap-2 text-sm font-medium text-purple-600 underline hover:no-underline">
                      Continuar Comprando
                      <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
                      </svg>
                    </a>
                      <button type="button" class="text-sm px-4 py-2.5 w-full font-medium tracking-wide bg-slate-800 hover:bg-slate-900 text-white rounded-md cursor-pointer">Buy Now</button>
                      <button type="button" class="text-sm px-4 py-2.5 w-full font-medium tracking-wide bg-slate-50 hover:bg-slate-100 text-slate-900 border border-gray-300 rounded-md cursor-pointer">Continue Shopping</button>
                  </div>
                  <div class="mt-5 flex flex-wrap justify-center gap-4">
                      <img src='https://readymadeui.com/images/master.webp' alt="card1" class="w-10 object-contain" />
                      <img src='https://readymadeui.com/images/visa.webp' alt="card2" class="w-10 object-contain" />
                      <img src='https://readymadeui.com/images/american-express.webp' alt="card3" class="w-10 object-contain" />
                  </div>
              </div>
          </div>
      </div>






 

      



          





   
        <x-address-modal route="site.profile.address-modal.store" />
    
    <!-- Os scripts para manipulação de endereços foram movidos para o final da página para evitar conflitos -->
    
    <!-- Carregando os scripts do carrinho -->
    <script src="{{ asset('js/cart.js') }}"></script>
</x-app-layout>
