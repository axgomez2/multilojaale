<!-- Modal de Cadastro de Endereço - Versão Simplificada -->
<div id="address-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-screen overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Adicionar Endereço</h2>
                <button type="button" onclick="document.getElementById('address-modal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form id="address-form" action="{{ route('site.checkout.addresses.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Nome do endereço -->
                <div>
                    <label for="address_name" class="block text-sm font-medium text-gray-700">Nome do endereço</label>
                    <input type="text" name="name" id="address_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Ex: Minha casa, Trabalho" required>
                </div>
                
                <!-- Informações do destinatário -->
                <div>
                    <label for="recipient_name" class="block text-sm font-medium text-gray-700">Nome do destinatário</label>
                    <input type="text" name="recipient_name" id="recipient_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Nome completo da pessoa que vai receber" required>
                </div>
                
                <!-- Telefone -->
                <div>
                    <label for="recipient_phone" class="block text-sm font-medium text-gray-700">Telefone</label>
                    <input type="text" name="recipient_phone" id="recipient_phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="(00) 00000-0000" required>
                </div>
                
                <!-- CEP -->
                <div>
                    <label for="address_zipcode" class="block text-sm font-medium text-gray-700">CEP</label>
                    <input type="text" name="zipcode" id="address_zipcode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="00000-000" maxlength="9" required>
                    <div id="zipcode-error" class="text-red-500 text-sm mt-1 hidden">O CEP deve ter 9 caracteres no formato 00000-000</div>
                </div>
                
                <!-- Rua -->
                <div>
                    <label for="address_street" class="block text-sm font-medium text-gray-700">Rua</label>
                    <input type="text" name="street" id="address_street" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                </div>
                
                <!-- Número e Complemento -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="address_number" class="block text-sm font-medium text-gray-700">Número</label>
                        <input type="text" name="number" id="address_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                    </div>
                    
                    <div>
                        <label for="address_complement" class="block text-sm font-medium text-gray-700">Complemento</label>
                        <input type="text" name="complement" id="address_complement" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Apto, Bloco, etc.">
                    </div>
                </div>
                
                <!-- Bairro -->
                <div>
                    <label for="address_district" class="block text-sm font-medium text-gray-700">Bairro</label>
                    <input type="text" name="district" id="address_district" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                </div>
                
                <!-- Cidade e Estado -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="address_city" class="block text-sm font-medium text-gray-700">Cidade</label>
                        <input type="text" name="city" id="address_city" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                    </div>
                    
                    <div>
                        <label for="address_state" class="block text-sm font-medium text-gray-700">Estado</label>
                        <select name="state" id="address_state" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                            <option value="">Selecione</option>
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
                
                <!-- Campos ocultos necessários -->
                <input type="hidden" name="type" value="shipping">
                <input type="hidden" name="is_default_shipping" value="1">
                <input type="hidden" name="is_default_billing" value="0">
                <input type="hidden" name="recipient_document" value="">
                <input type="hidden" name="recipient_email" value="">
                
                <div class="flex space-x-4 mt-6">
                    <button type="button" onclick="document.getElementById('address-modal').classList.add('hidden')" class="flex-1 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Salvar endereço
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Função para abrir o modal
    function openAddressModal() {
        document.getElementById('address-modal').classList.remove('hidden');
    }
    
    // Aplicar máscara ao campo de CEP
    document.addEventListener('DOMContentLoaded', function() {
        const zipcodeInput = document.getElementById('address_zipcode');
        
        zipcodeInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            
            e.target.value = value;
        });
        
        // Validar e enviar o formulário via AJAX
        const addressForm = document.getElementById('address-form');
        addressForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Impedir o envio tradicional do formulário
            
            const zipcode = zipcodeInput.value;
            const zipError = document.getElementById('zipcode-error');
            
            // Verificar se o CEP está no formato correto (00000-000)
            if (zipcode.length !== 9 || zipcode.charAt(5) !== '-') {
                zipError.classList.remove('hidden');
                return false;
            } else {
                zipError.classList.add('hidden');
            }
            
            // Mostrar indicador de carregamento
            const submitBtn = addressForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Salvando...';
            
            // Enviar o formulário via fetch API
            fetch(addressForm.action, {
                method: 'POST',
                body: new FormData(addressForm),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Esconder o modal
                    document.getElementById('address-modal').classList.add('hidden');
                    
                    // Mostrar mensagem de sucesso temporariamente
                    const successMessage = document.createElement('div');
                    successMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg';
                    successMessage.innerHTML = data.message;
                    document.body.appendChild(successMessage);
                    
                    // Redirecionar para a página atualizada
                    setTimeout(() => {
                        // Limpar a URL se terminar com uma interrogação solitária
                        let redirectUrl = data.redirect;
                        if (redirectUrl && redirectUrl.endsWith('?')) {
                            redirectUrl = redirectUrl.slice(0, -1);
                        }
                        window.location.href = redirectUrl;
                    }, 500);
                } else {
                    // Mostrar mensagem de erro
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const errorElement = document.getElementById(`${field}-error`);
                            if (errorElement) {
                                errorElement.textContent = data.errors[field][0];
                                errorElement.classList.remove('hidden');
                            }
                        });
                    } else if (data.message) {
                        alert(data.message);
                    } else {
                        alert('Erro ao salvar o endereço');
                    }
                    
                    // Restaurar o botão
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            })
            .catch(error => {
                console.error('Erro ao enviar o formulário:', error);
                alert('Ocorreu um erro ao processar sua solicitação. Tente novamente.');
                
                // Restaurar o botão
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    });
</script>
