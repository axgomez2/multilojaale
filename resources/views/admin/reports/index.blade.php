<x-admin-layout title="Relatórios">
<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700">
        Relatórios
    </h2>
    
    <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-3">
        <!-- Card Relatório de Discos -->
        <div class="p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <div class="flex items-center">
                <div class="p-3 rounded-full text-blue-500 bg-blue-100 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                    </svg>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">
                        Relatório de Discos
                    </p>
                    <p class="text-lg font-semibold text-gray-700">
                        Inventário e Valores
                    </p>
                </div>
            </div>
            <p class="text-gray-600 mt-4">
                Visualize dados completos sobre discos em estoque, valores de compra, venda e lucro potencial.
            </p>
            <div class="mt-6">
                <a href="{{ route('admin.reports.vinyl') }}" 
                   class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue">
                    Ver Relatório
                </a>
            </div>
        </div>
        
        <!-- Espaço para futuros relatórios -->
        <div class="p-4 bg-white rounded-lg shadow-md dark:bg-gray-800 opacity-50">
            <div class="flex items-center">
                <div class="p-3 rounded-full text-gray-500 bg-gray-100 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">
                        Relatório de Vendas
                    </p>
                    <p class="text-lg font-semibold text-gray-700">
                        Em breve
                    </p>
                </div>
            </div>
            <p class="text-gray-600 mt-4">
                Este relatório estará disponível em uma atualização futura.
            </p>
            <div class="mt-6">
                <button disabled
                   class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-gray-400 border border-transparent rounded-lg cursor-not-allowed">
                    Em Desenvolvimento
                </button>
            </div>
        </div>
    </div>
</div>
</x-admin-layout>
