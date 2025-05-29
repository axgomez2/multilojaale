<x-admin-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Configurações de Pagamento</h1>
        
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-4">
                <h2 class="text-xl font-semibold mb-2">Gateways de Pagamento Disponíveis</h2>
                <p class="text-gray-600 mb-4">Selecione e configure o gateway de pagamento que deseja utilizar. Apenas um gateway pode estar ativo por vez.</p>
            </div>
            
            <div class="space-y-4">
                @foreach($gateways as $gateway)
                <div class="border rounded-lg p-4 {{ $gateway->active ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium">{{ $gateway->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $gateway->active ? 'Ativo' : 'Inativo' }}</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.payment.edit', $gateway->id) }}" class="px-4 py-2 bg-primary text-white rounded-md">Configurar</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-admin-layout>
