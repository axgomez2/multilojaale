<x-admin-layout title="Dashboard Administrativo">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4 text-zinc-900 dark:text-zinc-100">Dashboard Administrativo</h1>
            <p class="mb-4 text-zinc-700 dark:text-zinc-300">Bem-vindo, {{ $user->name }}!</p>
            <p class="text-zinc-700 dark:text-zinc-300">Você está conectado como <span class="font-semibold text-emerald-500">Administrador</span> (Role: {{ $user->role }}).</p>
            
            @if($user->isDeveloper())
            <div class="mt-3 inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
                Desenvolvedor
            </div>
            @endif
            
            <!-- Dados da Loja -->
            <div class="mt-4 p-4 bg-gray-50 dark:bg-zinc-700 rounded">
                <h3 class="font-medium text-lg mb-2 text-zinc-800 dark:text-zinc-200">Informações da Loja</h3>
                <p class="text-zinc-700 dark:text-zinc-300 text-sm"><strong>Nome:</strong> {{ $store->name }}</p>
                @if($store->email)
                <p class="text-zinc-700 dark:text-zinc-300 text-sm"><strong>Email:</strong> {{ $store->email }}</p>
                @endif
                @if($store->document)
                <p class="text-zinc-700 dark:text-zinc-300 text-sm"><strong>{{ $store->document_type == 'cpf' ? 'CPF' : 'CNPJ' }}:</strong> {{ $store->document }}</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Estatísticas Rápidas -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-zinc-800 p-4 rounded-lg shadow">
            <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Usuários Totais</h3>
            <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $userCounts['total'] }}</p>
        </div>
        
        <div class="bg-white dark:bg-zinc-800 p-4 rounded-lg shadow">
            <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Administradores</h3>
            <p class="mt-2 text-3xl font-semibold text-emerald-600">{{ $userCounts['admin'] }}</p>
        </div>
        
        <div class="bg-white dark:bg-zinc-800 p-4 rounded-lg shadow">
            <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Desenvolvedores</h3>
            <p class="mt-2 text-3xl font-semibold text-purple-600">{{ $userCounts['developer'] }}</p>
        </div>
        
        <div class="bg-white dark:bg-zinc-800 p-4 rounded-lg shadow">
            <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Usuários Comuns</h3>
            <p class="mt-2 text-3xl font-semibold text-blue-600">{{ $userCounts['user'] }}</p>
        </div>
    </div>
    
    <!-- Informações do Sistema -->
    <div class="mt-6 bg-white dark:bg-zinc-800 rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Informações do Sistema</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-zinc-700 dark:text-zinc-300"><span class="font-medium">PHP:</span> {{ $systemInfo['php_version'] }}</p>
                <p class="text-zinc-700 dark:text-zinc-300"><span class="font-medium">Laravel:</span> {{ $systemInfo['laravel_version'] }}</p>
            </div>
            <div>
                <p class="text-zinc-700 dark:text-zinc-300"><span class="font-medium">Ambiente:</span> {{ $systemInfo['environment'] }}</p>
                <p class="text-zinc-700 dark:text-zinc-300"><span class="font-medium">Servidor:</span> {{ $systemInfo['server'] }}</p>
            </div>
        </div>
    </div>
    
    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-4 text-zinc-900 dark:text-zinc-100">Funções Administrativas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Usuários -->
            <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-emerald-500 text-white mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2 text-zinc-900 dark:text-zinc-100">Gerenciar Usuários</h3>
                <p class="text-zinc-600 dark:text-zinc-400 text-sm mb-4">Adicionar, editar e remover usuários do sistema.</p>
                <a href="#" class="text-emerald-600 hover:text-emerald-500 text-sm font-medium">Acessar &rarr;</a>
            </div>
            
            <!-- Produtos -->
            <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2 text-zinc-900 dark:text-zinc-100">Produtos</h3>
                <p class="text-zinc-600 dark:text-zinc-400 text-sm mb-4">Gerenciar catálogo de produtos da loja.</p>
                <a href="#" class="text-blue-600 hover:text-blue-500 text-sm font-medium">Acessar &rarr;</a>
            </div>
            
            <!-- Pedidos -->
            <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-amber-500 text-white mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2 text-zinc-900 dark:text-zinc-100">Pedidos</h3>
                <p class="text-zinc-600 dark:text-zinc-400 text-sm mb-4">Acompanhar e gerenciar pedidos dos clientes.</p>
                <a href="#" class="text-amber-600 hover:text-amber-500 text-sm font-medium">Acessar &rarr;</a>
            </div>
            
            <!-- Configurações -->
            <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-500 text-white mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2 text-zinc-900 dark:text-zinc-100">Configurações</h3>
                <p class="text-zinc-600 dark:text-zinc-400 text-sm mb-4">Configurações gerais e integrações.</p>
                <a href="#" class="text-purple-600 hover:text-purple-500 text-sm font-medium">Acessar &rarr;</a>
            </div>
            
            <!-- Relatórios -->
            <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2 text-zinc-900 dark:text-zinc-100">Relatórios</h3>
                <p class="text-zinc-600 dark:text-zinc-400 text-sm mb-4">Visualizar relatórios de vendas e desempenho.</p>
                <a href="#" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">Acessar &rarr;</a>
            </div>
            
            <!-- Área do Desenvolvedor (apenas visível para desenvolvedores) -->
            @if($user->isDeveloper())
            <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-pink-500 text-white mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2 text-zinc-900 dark:text-zinc-100">Área do Desenvolvedor</h3>
                <p class="text-zinc-600 dark:text-zinc-400 text-sm mb-4">Configurar aspectos técnicos da loja.</p>
                <a href="{{ route('admin.developer.branding') }}" class="text-pink-600 hover:text-pink-500 text-sm font-medium">Acessar &rarr;</a>
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>
