<x-app-layout>
    <div class="container mx-auto px-4 py-8 max-w-3xl">
        <!-- Banner de falha -->
        <div class="bg-red-50 border-l-4 border-red-500 p-6 mb-8 rounded-r shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-red-800 text-2xl font-bold">Falha no Pagamento</h2>
                    <p class="text-red-700 mt-1">
                        Infelizmente houve um problema com o pagamento do seu pedido <span class="font-semibold">{{ $order->order_number }}</span>.
                    </p>
                </div>
            </div>
        </div>

        <!-- Status do pedido -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h3 class="text-xl font-semibold mb-4 border-b pb-2">Detalhes do Pedido</h3>
            
            <div class="flex flex-wrap">
                <div class="w-full md:w-1/2 mb-4">
                    <span class="block text-sm text-gray-500">Número do Pedido</span>
                    <span class="block font-medium text-gray-800">{{ $order->order_number }}</span>
                </div>
                <div class="w-full md:w-1/2 mb-4">
                    <span class="block text-sm text-gray-500">Data do Pedido</span>
                    <span class="block font-medium text-gray-800">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="w-full md:w-1/2 mb-4">
                    <span class="block text-sm text-gray-500">Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        Pagamento Recusado
                    </span>
                </div>
                <div class="w-full md:w-1/2 mb-4">
                    <span class="block text-sm text-gray-500">Valor Total</span>
                    <span class="block font-medium text-gray-800">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Opções para o usuário -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h3 class="text-xl font-semibold mb-4">O que fazer agora?</h3>
            
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <svg class="h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-gray-700">Você pode tentar realizar o pagamento novamente.</p>
                        <a href="{{ route('site.checkout.payment', $order->id) }}" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            Tentar novamente
                        </a>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <svg class="h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-gray-700">Se continuar tendo problemas, entre em contato com nosso suporte.</p>
                        <a href="/contato" class="mt-2 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            Contatar suporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Botão para voltar à loja -->
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar à Loja
            </a>
        </div>
    </div>
</x-app-layout>
