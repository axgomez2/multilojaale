<x-layouts.admin title="Dashboard Administrativo">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4 text-zinc-900 dark:text-zinc-100">Dashboard Administrativo</h1>
            <p class="mb-4 text-zinc-700 dark:text-zinc-300">Bem-vindo, {{ auth()->user()->name }}!</p>
            <p class="text-zinc-700 dark:text-zinc-300">Você está conectado como <span class="font-semibold text-emerald-500">Administrador</span> (Role: {{ auth()->user()->role }}).</p>
            
            @if(auth()->user()->isDeveloper())
            <div class="mt-3 inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
                Desenvolvedor
            </div>
            @endif
        </div>
    </div>
    
    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-4 text-zinc-900 dark:text-zinc-100">Funções Administrativas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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
            
            <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2 text-zinc-900 dark:text-zinc-100">Configurações do Sistema</h3>
                <p class="text-zinc-600 dark:text-zinc-400 text-sm mb-4">Alterar configurações globais da aplicação.</p>
                <a href="#" class="text-blue-600 hover:text-blue-500 text-sm font-medium">Acessar &rarr;</a>
            </div>
            
            <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-amber-500 text-white mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2 text-zinc-900 dark:text-zinc-100">Logs e Relatórios</h3>
                <p class="text-zinc-600 dark:text-zinc-400 text-sm mb-4">Visualizar logs do sistema e gerar relatórios.</p>
                <a href="#" class="text-amber-600 hover:text-amber-500 text-sm font-medium">Acessar &rarr;</a>
            </div>
        </div>
    </div>
</x-layouts.admin>
