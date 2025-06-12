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
        
        <form action="{{ route('site.shipping.save-address') }}" method="POST" class="space-y-4">
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
                        <label for="zipcode" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                        <input type="text" name="zipcode" id="zipcode" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                            placeholder="00000-000" maxlength="9" required>
                        <div id="zipcode-error" class="text-red-500 text-sm mt-1 hidden">O CEP deve ter 9 caracteres no formato 00000-000</div>
                    </div>
                    <div class="md:col-span-2">
                        <label for="street" class="block text-sm font-medium text-gray-700 mb-1">Rua/Avenida</label>
                        <input type="text" name="street" id="street" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                            required>
                    </div>
                    <div>
                        <label for="number" class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                        <input type="text" name="number" id="number" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                            required>
                    </div>
                    <div class="md:col-span-2">
                        <label for="complement" class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                        <input type="text" name="complement" id="complement" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label for="district" class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                        <input type="text" name="district" id="district" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                            required>
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                        <input type="text" name="city" id="city" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                            required>
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="state" id="state" 
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
            
            <div class="mt-4">
                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md">
                    Salvar Endereço
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
            
            <div class="mt-4">
                <button type="button" id="btn-new-address" class="w-full text-purple-600 bg-white border border-purple-500 hover:bg-purple-50 font-medium py-2 px-4 rounded-md">
                    Adicionar novo endereço
                </button>
            </div>
        @endif
    @endif
</div>
