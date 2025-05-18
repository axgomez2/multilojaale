<x-layouts.app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-zinc-100">
                    <h1 class="text-2xl font-bold mb-4">Dashboard Administrativo</h1>
                    <p class="mb-4">Bem-vindo, {{ auth()->user()->name }}!</p>
                    <p>Você está conectado como <span class="font-semibold text-emerald-400">Administrador</span> (Role: {{ auth()->user()->role }}).</p>
                    
                    <div class="mt-6 border-t border-zinc-700 pt-4">
                        <h2 class="text-xl font-semibold mb-4">Funções Administrativas</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="bg-zinc-700 p-4 rounded-lg">
                                <h3 class="font-bold text-lg mb-2">Gerenciar Usuários</h3>
                                <p class="text-zinc-300 text-sm">Adicionar, editar e remover usuários do sistema.</p>
                            </div>
                            <div class="bg-zinc-700 p-4 rounded-lg">
                                <h3 class="font-bold text-lg mb-2">Configurações do Sistema</h3>
                                <p class="text-zinc-300 text-sm">Alterar configurações globais da aplicação.</p>
                            </div>
                            <div class="bg-zinc-700 p-4 rounded-lg">
                                <h3 class="font-bold text-lg mb-2">Logs e Relatórios</h3>
                                <p class="text-zinc-300 text-sm">Visualizar logs do sistema e gerar relatórios.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
