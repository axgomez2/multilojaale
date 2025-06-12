<x-app-layout>
    <div class="container mx-auto px-4 py-8 max-w-3xl">
        <!-- Banner de pagamento pendente -->
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 mb-8 rounded-r shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-yellow-800 text-2xl font-bold">Pagamento em Processamento</h2>
                    <p class="text-yellow-700 mt-1">
                        O pagamento do seu pedido <span class="font-semibold">{{ $order->order_number }}</span> está sendo processado.
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
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Pagamento Pendente
                    </span>
                </div>
                <div class="w-full md:w-1/2 mb-4">
                    <span class="block text-sm text-gray-500">Valor Total</span>
                    <span class="block font-medium text-gray-800">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Informações sobre o pagamento pendente -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h3 class="text-xl font-semibold mb-4">O que acontece agora?</h3>
            
            <div class="space-y-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-full p-1">
                        <svg class="h-5 w-5 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-gray-700">Seu pagamento está sendo processado pela instituição financeira.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-full p-1">
                        <svg class="h-5 w-5 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-gray-700">Você receberá um e-mail assim que o pagamento for confirmado.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-full p-1">
                        <svg class="h-5 w-5 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-gray-700">Não se preocupe, seu pedido está reservado e será processado assim que o pagamento for confirmado.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Botões de ação -->
        <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('site.account.order', $order->id) }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Acompanhar Pedido
            </a>
            
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar à Loja
            </a>
        </div>
    </div>
</x-app-layout>
