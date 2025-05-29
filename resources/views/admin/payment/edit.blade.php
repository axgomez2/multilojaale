<x-admin-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Configurar {{ $gateway->name }}</h1>
        
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Ops! Algo deu errado.</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('admin.payment.update', $gateway->id) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Gateway</label>
                <input type="text" name="name" value="{{ old('name', $gateway->name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
            </div>
            
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="active" value="1" {{ $gateway->active ? 'checked' : '' }} class="h-4 w-4 text-primary border-gray-300 focus:ring-primary">
                    <span class="ml-2 text-sm text-gray-700">Ativar este gateway</span>
                </label>
                <p class="text-xs text-gray-500 mt-1">Ao ativar este gateway, todos os outros serão automaticamente desativados.</p>
            </div>
            
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="sandbox_mode" value="1" {{ $gateway->sandbox_mode ? 'checked' : '' }} class="h-4 w-4 text-primary border-gray-300 focus:ring-primary">
                    <span class="ml-2 text-sm text-gray-700">Modo Sandbox (ambiente de testes)</span>
                </label>
                <p class="text-xs text-gray-500 mt-1">Ative este modo para realizar testes sem processar pagamentos reais.</p>
            </div>
            
            <div class="mb-6">
                <h3 class="text-lg font-medium mb-4">Credenciais</h3>
                
                @if($gateway->code == 'mercadopago')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Public Key</label>
                            <input type="text" name="mp_public_key" value="{{ old('mp_public_key', $gateway->credentials['public_key'] ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Access Token</label>
                            <input type="password" name="mp_access_token" value="{{ old('mp_access_token', $gateway->credentials['access_token'] ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                    </div>
                @elseif($gateway->code == 'pagseguro')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="ps_email" value="{{ old('ps_email', $gateway->credentials['email'] ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Token</label>
                            <input type="password" name="ps_token" value="{{ old('ps_token', $gateway->credentials['token'] ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                    </div>
                @elseif($gateway->code == 'rede')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">PV (Ponto de Venda)</label>
                            <input type="text" name="rede_pv" value="{{ old('rede_pv', $gateway->credentials['pv'] ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Token</label>
                            <input type="password" name="rede_token" value="{{ old('rede_token', $gateway->credentials['token'] ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="mb-6">
                <h3 class="text-lg font-medium mb-4">Configurações Avançadas</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="settings[sandbox]" value="1" {{ isset($gateway->settings['sandbox']) && $gateway->settings['sandbox'] ? 'checked' : '' }} class="h-4 w-4 text-primary border-gray-300 focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-700">Modo Sandbox (ambiente de testes)</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div>
                <button type="submit" class="px-6 py-3 bg-primary text-white rounded-md font-medium hover:bg-primary-dark transition-colors">Salvar Configurações</button>
                <a href="{{ route('admin.payment.index') }}" class="ml-4 px-6 py-3 bg-gray-200 text-gray-700 rounded-md font-medium hover:bg-gray-300 transition-colors">Cancelar</a>
            </div>
        </form>
    </div>
</x-admin-layout>
