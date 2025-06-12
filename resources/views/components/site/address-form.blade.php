<form action="{{ route('site.shipping.save-address') }}" method="POST" class="space-y-4" x-data="{
    cep: '',
    street: '',
    number: '',
    complement: '',
    district: '',
    city: '',
    state: '',
    isLoading: false,
    cepError: '',
    
    async searchCep() {
        const cleanCep = this.cep.replace(/\D/g, '');
        
        if (cleanCep.length !== 8) {
            this.cepError = 'CEP deve ter 8 dígitos';
            return;
        }
        
        this.isLoading = true;
        this.cepError = '';
        
        try {
            const response = await fetch(`https://viacep.com.br/ws/${cleanCep}/json/`);
            const data = await response.json();
            
            if (data.erro) {
                this.cepError = 'CEP não encontrado';
                this.clearAddressFields();
            } else {
                this.street = data.logradouro || '';
                this.district = data.bairro || '';
                this.city = data.localidade || '';
                this.state = data.uf || '';
                
                // Foca no campo número após o preenchimento
                this.$nextTick(() => {
                    const numberField = this.$refs.numberField;
                    if (numberField) numberField.focus();
                });
            }
        } catch (error) {
            console.error('Erro ao buscar CEP:', error);
            this.cepError = 'Erro ao consultar CEP. Tente novamente.';
            this.clearAddressFields();
        } finally {
            this.isLoading = false;
        }
    },
    
    clearAddressFields() {
        this.street = '';
        this.district = '';
        this.city = '';
        this.state = '';
    },
    
    formatCep() {
        // Remove tudo que não for número
        let value = this.cep.replace(/\D/g, '');
        
        // Adiciona o hífen após 5 dígitos
        if (value.length > 5) {
            value = value.replace(/^(\d{5})(\d{1,3})?/, '$1-$2');
        }
        
        this.cep = value;
    },
    
    handleCepBlur() {
        const cleanCep = this.cep.replace(/\D/g, '');
        if (cleanCep.length === 8) {
            this.searchCep();
        }
    }
}">
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
                <label for="form2_zipcode" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                <div class="relative">
                    <input type="text" 
                           x-model="cep" 
                           @input="formatCep()" 
                           @blur="handleCepBlur()"
                           :class="{'border-red-500': cepError, 'border-gray-300': !cepError}"
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                           placeholder="00000-000" 
                           maxlength="9" 
                           required>
                    <div x-show="isLoading" class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="animate-spin h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p x-show="cepError" x-text="cepError" class="mt-1 text-sm text-red-600"></p>
                </div>
                <div id="form2_zipcode-error" class="text-red-500 text-sm mt-1 hidden">O CEP deve ter 8 dígitos no formato 00000-000</div>
            </div>
            <div class="md:col-span-2">
                <label for="form2_street" class="block text-sm font-medium text-gray-700 mb-1">Rua/Avenida</label>
                <input type="text" 
                       name="street" 
                       x-model="street" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                       required>
            </div>
            <div>
                <label for="form2_number" class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                <input type="text" 
                       name="number" 
                       x-ref="numberField" 
                       x-model="number" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purpen-500" 
                       required>
            </div>
            <div class="md:col-span-2">
                <label for="form2_complement" class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                <input type="text" 
                       name="complement" 
                       x-model="complement" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label for="form2_district" class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                <input type="text" 
                       name="district" 
                       x-model="district" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                       required>
            </div>
            <div>
                <label for="form2_city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                <input type="text" 
                       name="city" 
                       x-model="city" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                       required>
            </div>
            <div>
                <label for="form2_state" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="state" 
                        x-model="state" 
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
                        </form>