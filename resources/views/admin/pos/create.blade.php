<x-admin-layout title="Nova Venda - PDV">
    <div class="p-4">
        @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="p-6 bg-white rounded-lg shadow-md mb-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">Nova Venda</h1>
                <a href="{{ route('admin.pos.index') }}" class="px-4 py-2 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar
                </a>
            </div>

            <!-- Formulário de Venda -->
            <form id="posForm" action="{{ route('admin.pos.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Dados do Cliente -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Dados do Cliente</h3>
                        
                        <div class="mb-4">
                            <div class="flex items-center mb-2">
                                <input type="radio" id="clienteAnon" name="client_type" value="anonymous" checked class="mr-2">
                                <label for="clienteAnon" class="text-sm font-medium text-gray-700">Cliente de Balcão</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="clienteReg" name="client_type" value="registered" class="mr-2">
                                <label for="clienteReg" class="text-sm font-medium text-gray-700">Cliente Cadastrado</label>
                            </div>
                        </div>
                        
                        <div id="anonClientContainer" class="mb-4">
                            <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Cliente (opcional)</label>
                            <input type="text" id="customer_name" name="customer_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                        
                        <div id="regClientContainer" class="mb-4 hidden">
                            <label for="user_search" class="block text-sm font-medium text-gray-700 mb-1">Buscar Cliente Cadastrado</label>
                            <div class="relative">
                                <input type="text" id="user_search" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="Digite nome ou email">
                                <input type="hidden" id="user_id" name="user_id">
                                <div id="usersResult" class="absolute z-10 w-full mt-1 bg-white shadow-lg rounded-md border border-gray-200 hidden"></div>
                            </div>
                            <div id="selectedUserInfo" class="mt-2 p-2 bg-blue-50 rounded-md hidden">
                                <span id="selectedUserName" class="font-medium"></span>
                                <span id="selectedUserEmail" class="text-sm text-gray-600 block"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informações de Pagamento -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Pagamento</h3>
                        
                        <div class="mb-4">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Forma de Pagamento</label>
                            <select id="payment_method" name="payment_method" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <option value="money">Dinheiro</option>
                                <option value="credit">Cartão de Crédito</option>
                                <option value="debit">Cartão de Débito</option>
                                <option value="pix">PIX</option>
                                <option value="transfer">Transferência Bancária</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="shipping" class="block text-sm font-medium text-gray-700 mb-1">Frete (R$)</label>
                            <input type="number" id="shipping" name="shipping" value="0" min="0" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                        
                        <div class="mb-4">
                            <label for="discount" class="block text-sm font-medium text-gray-700 mb-1">Desconto (R$)</label>
                            <input type="number" id="discount" name="discount" value="0" min="0" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                        
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                            <textarea id="notes" name="notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Busca de Produtos -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Adicionar Discos</h3>
                    
                    <div class="flex space-x-4 mb-4">
                        <div class="flex-grow">
                            <label for="vinyl_search" class="block text-sm font-medium text-gray-700 mb-1">Buscar Disco</label>
                            <div class="relative">
                                <input type="text" id="vinyl_search" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="Digite título, artista ou código do catálogo">
                                <div id="vinylsResult" class="absolute z-10 w-full mt-1 bg-white shadow-lg rounded-md border border-gray-200 hidden"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Itens do Carrinho -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-800">Itens da Venda</h3>
                        <span id="itemCount" class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded">0 itens</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disco</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Desconto</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="cartItems" class="bg-white divide-y divide-gray-200">
                                <tr id="emptyCart">
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Nenhum item adicionado à venda.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Subtotal:</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-bold">R$ <span id="subtotal">0,00</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Desconto:</td>
                                    <td class="px-6 py-4 text-sm text-red-600 font-bold">- R$ <span id="discountValue">0,00</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Frete:</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-bold">+ R$ <span id="shippingValue">0,00</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Total:</td>
                                    <td class="px-6 py-4 text-lg text-green-600 font-bold">R$ <span id="totalValue">0,00</span></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <!-- Items JSON -->
                <div id="itemsContainer"></div>
                
                <!-- Botões de Ação -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.pos.index') }}" class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </a>
                    <button type="submit" id="submitSale" class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50" disabled>
                        Finalizar Venda
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts específicos para a página -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cart = [];
            let subtotal = 0;
            
            // Toggle de tipo de cliente
            const clienteAnon = document.getElementById('clienteAnon');
            const clienteReg = document.getElementById('clienteReg');
            const anonClientContainer = document.getElementById('anonClientContainer');
            const regClientContainer = document.getElementById('regClientContainer');
            
            clienteAnon.addEventListener('change', function() {
                if (this.checked) {
                    anonClientContainer.classList.remove('hidden');
                    regClientContainer.classList.add('hidden');
                    document.getElementById('user_id').value = '';
                }
            });
            
            clienteReg.addEventListener('change', function() {
                if (this.checked) {
                    anonClientContainer.classList.add('hidden');
                    regClientContainer.classList.remove('hidden');
                }
            });
            
            // Busca de usuários
            const userSearch = document.getElementById('user_search');
            const usersResult = document.getElementById('usersResult');
            let typingTimer;
            
            userSearch.addEventListener('input', function() {
                clearTimeout(typingTimer);
                
                if (userSearch.value) {
                    typingTimer = setTimeout(function() {
                        searchUsers(userSearch.value);
                    }, 500);
                } else {
                    usersResult.innerHTML = '';
                    usersResult.classList.add('hidden');
                }
            });
            
            function searchUsers(query) {
                fetch(`{{ route('admin.pos.search-users') }}?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        usersResult.innerHTML = '';
                        
                        if (data.length > 0) {
                            data.forEach(user => {
                                const userEl = document.createElement('div');
                                userEl.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                                userEl.innerHTML = `
                                    <div class="font-medium">${user.name}</div>
                                    <div class="text-sm text-gray-600">${user.email}</div>
                                `;
                                userEl.addEventListener('click', function() {
                                    selectUser(user);
                                });
                                usersResult.appendChild(userEl);
                            });
                            usersResult.classList.remove('hidden');
                        } else {
                            usersResult.innerHTML = '<div class="p-2 text-gray-500">Nenhum usuário encontrado</div>';
                            usersResult.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao buscar usuários:', error);
                    });
            }
            
            function selectUser(user) {
                document.getElementById('user_id').value = user.id;
                document.getElementById('selectedUserName').textContent = user.name;
                document.getElementById('selectedUserEmail').textContent = user.email;
                document.getElementById('selectedUserInfo').classList.remove('hidden');
                userSearch.value = '';
                usersResult.classList.add('hidden');
            }
            
            // Busca de discos
            const vinylSearch = document.getElementById('vinyl_search');
            const vinylsResult = document.getElementById('vinylsResult');
            let vinylTypingTimer;
            
            vinylSearch.addEventListener('input', function() {
                clearTimeout(vinylTypingTimer);
                
                if (vinylSearch.value) {
                    vinylTypingTimer = setTimeout(function() {
                        searchVinyls(vinylSearch.value);
                    }, 500);
                } else {
                    vinylsResult.innerHTML = '';
                    vinylsResult.classList.add('hidden');
                }
            });
            
            function searchVinyls(query) {
                fetch(`{{ route('admin.pos.search-vinyls') }}?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        vinylsResult.innerHTML = '';
                        
                        if (data.length > 0) {
                            data.forEach(vinyl => {
                                const vinylEl = document.createElement('div');
                                vinylEl.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                                vinylEl.innerHTML = `
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" src="${vinyl.cover_image ? '{{ asset("') + vinyl.cover_image + '") }}' : '{{ asset("images/placeholder.jpg") }}'}" alt="${vinyl.title}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium">${vinyl.title}</div>
                                            <div class="text-sm text-gray-600">${vinyl.artist}</div>
                                            <div class="text-xs text-gray-500">${vinyl.catalog_number} - R$ ${formatCurrency(vinyl.price)}</div>
                                        </div>
                                    </div>
                                `;
                                vinylEl.addEventListener('click', function() {
                                    addToCart(vinyl);
                                });
                                vinylsResult.appendChild(vinylEl);
                            });
                            vinylsResult.classList.remove('hidden');
                        } else {
                            vinylsResult.innerHTML = '<div class="p-2 text-gray-500">Nenhum disco encontrado</div>';
                            vinylsResult.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao buscar discos:', error);
                    });
            }
            
            function addToCart(vinyl) {
                // Verifica se o disco já está no carrinho
                if (cart.some(item => item.id === vinyl.id)) {
                    alert('Este disco já foi adicionado ao carrinho!');
                    vinylSearch.value = '';
                    vinylsResult.classList.add('hidden');
                    return;
                }
                
                // Adiciona ao carrinho
                cart.push({
                    id: vinyl.id,
                    title: vinyl.title,
                    artist: vinyl.artist,
                    catalog_number: vinyl.catalog_number,
                    price: vinyl.price,
                    discount: 0,
                    total: vinyl.price
                });
                
                updateCartDisplay();
                vinylSearch.value = '';
                vinylsResult.classList.add('hidden');
            }
            
            function removeFromCart(index) {
                cart.splice(index, 1);
                updateCartDisplay();
            }
            
            function updateCartDisplay() {
                const cartItems = document.getElementById('cartItems');
                const emptyCart = document.getElementById('emptyCart');
                const itemCount = document.getElementById('itemCount');
                const itemsContainer = document.getElementById('itemsContainer');
                const subtotalEl = document.getElementById('subtotal');
                const totalValueEl = document.getElementById('totalValue');
                const submitSale = document.getElementById('submitSale');
                
                // Limpa o carrinho atual
                cartItems.innerHTML = '';
                itemsContainer.innerHTML = '';
                
                if (cart.length === 0) {
                    cartItems.appendChild(emptyCart);
                    itemCount.textContent = '0 itens';
                    subtotalEl.textContent = '0,00';
                    totalValueEl.textContent = '0,00';
                    submitSale.disabled = true;
                    return;
                }
                
                // Atualiza o contador
                itemCount.textContent = cart.length + (cart.length === 1 ? ' item' : ' itens');
                
                // Recalcula o subtotal
                subtotal = 0;
                
                cart.forEach((item, index) => {
                    // Cria a linha na tabela
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${item.title}</div>
                                    <div class="text-sm text-gray-500">${item.artist}</div>
                                    <div class="text-xs text-gray-500">${item.catalog_number}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            R$ ${formatCurrency(item.price)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="number" min="0" max="${item.price}" step="0.01" value="${item.discount}" 
                                class="item-discount w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                                onchange="updateItemDiscount(${index}, this.value)">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            R$ ${formatCurrency(item.price - item.discount)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button type="button" class="text-red-600 hover:text-red-900" onclick="removeFromCart(${index})">
                                Remover
                            </button>
                        </td>
                    `;
                    cartItems.appendChild(row);
                    
                    // Cria os inputs hidden
                    const itemInput = document.createElement('div');
                    itemInput.innerHTML = `
                        <input type="hidden" name="items[${index}][vinyl_sec_id]" value="${item.id}">
                        <input type="hidden" name="items[${index}][price]" value="${item.price}">
                        <input type="hidden" name="items[${index}][quantity]" value="1">
                        <input type="hidden" name="items[${index}][item_discount]" value="${item.discount}" class="item-discount-input-${index}">
                    `;
                    itemsContainer.appendChild(itemInput);
                    
                    // Atualiza o subtotal
                    subtotal += (item.price - item.discount);
                });
                
                // Atualiza o subtotal e total
                subtotalEl.textContent = formatCurrency(subtotal);
                updateTotal();
                
                // Habilita o botão de finalizar
                submitSale.disabled = false;
            }
            
            // Atualiza o desconto de um item
            window.updateItemDiscount = function(index, value) {
                const discount = parseFloat(value) || 0;
                cart[index].discount = discount;
                document.querySelector(`.item-discount-input-${index}`).value = discount;
                
                // Recalcula o total do item
                cart[index].total = cart[index].price - cart[index].discount;
                
                // Atualiza o display
                updateCartDisplay();
            };
            
            // Atualiza o total
            function updateTotal() {
                const discountEl = document.getElementById('discount');
                const shippingEl = document.getElementById('shipping');
                const discountValueEl = document.getElementById('discountValue');
                const shippingValueEl = document.getElementById('shippingValue');
                const totalValueEl = document.getElementById('totalValue');
                
                const discount = parseFloat(discountEl.value) || 0;
                const shipping = parseFloat(shippingEl.value) || 0;
                
                discountValueEl.textContent = formatCurrency(discount);
                shippingValueEl.textContent = formatCurrency(shipping);
                
                const total = subtotal - discount + shipping;
                totalValueEl.textContent = formatCurrency(total);
            }
            
            // Formatação de valores monetários
            function formatCurrency(value) {
                return parseFloat(value).toFixed(2).replace('.', ',');
            }
            
            // Event listeners para atualizar o total
            document.getElementById('discount').addEventListener('input', updateTotal);
            document.getElementById('shipping').addEventListener('input', updateTotal);
            
            // Evita clicks fora dos resultados
            document.addEventListener('click', function(e) {
                if (!usersResult.contains(e.target) && e.target !== userSearch) {
                    usersResult.classList.add('hidden');
                }
                
                if (!vinylsResult.contains(e.target) && e.target !== vinylSearch) {
                    vinylsResult.classList.add('hidden');
                }
            });
            
            // Define as funções no escopo global
            window.removeFromCart = removeFromCart;
        });
    </script>
</x-admin-layout>
