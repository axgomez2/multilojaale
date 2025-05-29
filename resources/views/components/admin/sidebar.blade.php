<div class="h-screen w-64 bg-zinc-900 text-white p-4 fixed left-0 top-0 z-10 overflow-y-auto">
    <div class="flex items-center justify-center mb-8 pt-4">
        <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">Admin Panel</a>
    </div>
    
    <nav class="space-y-2">
        <!-- Menu Principal -->
        <div x-data="{ open: false }" class="mb-6">
            <button @click="open = !open" class="w-full flex items-center justify-between text-xs uppercase text-zinc-500 mb-2 px-4 focus:outline-none">
                <span>Menu Principal</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-transition>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-zinc-800 text-emerald-400' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard
                </a>
            </div>
        </div>

        <!-- Produtos -->
        <div x-data="{ open: false }" class="mb-6">
            <button @click="open = !open" class="w-full flex items-center justify-between text-xs uppercase text-zinc-500 mb-2 px-4 focus:outline-none">
                <span>Produtos</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-transition>
                <a href="{{ route('admin.vinyls.index') }}" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-zinc-800 text-emerald-400' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Discos
                </a>
            </div>
        </div>

        <!-- Cadastros -->
        <div x-data="{ open: false }" class="mb-6">
            <button @click="open = !open" class="w-full flex items-center justify-between text-xs uppercase text-zinc-500 mb-2 px-4 focus:outline-none">
                <span>Cadastros</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-transition>
                <a href="{{ route('admin.cat-style-shop.index') }}" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md {{ request()->routeIs('admin.cat-style-shop.*') ? 'bg-zinc-800 text-emerald-400' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.585l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Categorias de Discos
                </a>
                <a href="{{ route('admin.midia-status.index') }}" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md mt-1 {{ request()->routeIs('admin.midia-status.*') ? 'bg-zinc-800 text-emerald-400' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                    Status de Mídia
                </a>
                <a href="{{ route('admin.cover-status.index') }}" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md mt-1 {{ request()->routeIs('admin.cover-status.*') ? 'bg-zinc-800 text-emerald-400' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Status de Capa
                </a>
                <a href="{{ route('admin.suppliers.index') }}" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md mt-1 {{ request()->routeIs('admin.suppliers.*') ? 'bg-zinc-800 text-emerald-400' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Fornecedores
                </a>
            </div>
        </div>

        <!-- Pedidos Online -->
        <div x-data="{ open: {{ request()->routeIs('orders.*') ? 'true' : 'false' }} }" class="mb-6">
            <button @click="open = !open" class="w-full flex items-center justify-between text-xs uppercase text-zinc-500 mb-2 px-4 focus:outline-none">
                <span>Pedidos Online</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-transition>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md {{ request()->routeIs('orders.index') ? 'bg-zinc-800 text-emerald-400' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Lista de Pedidos
                </a>
            </div>
        </div>
        
        <!-- Ponto de Venda -->
        <div x-data="{ open: false }" class="mb-6">
            <button @click="open = !open" class="w-full flex items-center justify-between text-xs uppercase text-zinc-500 mb-2 px-4 focus:outline-none">
                <span>Ponto de Venda</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-transition>
                <a href="{{ route('admin.pos.index') }}" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md {{ request()->routeIs('admin.pos.*') ? 'bg-zinc-800 text-emerald-400' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    PDV
                </a>
                <a href="{{ route('admin.pos.create') }}" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md mt-1 {{ request()->routeIs('admin.pos.create') ? 'bg-zinc-800 text-emerald-400' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Nova Venda
                </a>
                <a href="{{ route('admin.pos.list') }}" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md mt-1 {{ request()->routeIs('admin.pos.list') ? 'bg-zinc-800 text-emerald-400' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Histórico
                </a>
            </div>
        </div>

        <!-- Relatórios -->
        <div x-data="{ open: false }" class="mb-6">
            <button @click="open = !open" class="w-full flex items-center justify-between text-xs uppercase text-zinc-500 mb-2 px-4 focus:outline-none">
                <span>Relatórios</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-transition>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md {{ request()->routeIs('admin.reports.index') ? 'bg-zinc-800 text-emerald-400' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.reports.vinyl') }}" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md mt-1 {{ request()->routeIs('admin.reports.vinyl') ? 'bg-zinc-800 text-emerald-400' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Inventário de Discos
                </a>
            </div>
        </div>

        <!-- Gerenciamento -->
        <div x-data="{ open: false }" class="mb-6">
            <button @click="open = !open" class="w-full flex items-center justify-between text-xs uppercase text-zinc-500 mb-2 px-4 focus:outline-none">
                <span>Gerenciamento</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-transition>
                <a href="#" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Gerenciar Usuários
                </a>
                <a href="#" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Configurações
                </a>
                <a href="{{ route('admin.payment.index') }}" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md mt-1 {{ request()->routeIs('admin.payment.*') ? 'bg-zinc-800 text-emerald-400' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Gateways de Pagamento
                </a>
            </div>
        </div>

        <!-- Relatórios (outros) -->
        <div x-data="{ open: false }" class="mb-6">
            <button @click="open = !open" class="w-full flex items-center justify-between text-xs uppercase text-zinc-500 mb-2 px-4 focus:outline-none">
                <span>Relatórios</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-transition>
                <a href="#" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Logs e Relatórios
                </a>
            </div>
        </div>

        @if(auth()->user()->isDeveloper())
        <div x-data="{ open: false }" class="mb-6">
            <button @click="open = !open" class="w-full flex items-center justify-between text-xs uppercase text-zinc-500 mb-2 px-4 focus:outline-none">
                <span>Área do Desenvolvedor</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-transition>
                <a href="{{ route('admin.developer.branding') }}" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md {{ request()->routeIs('admin.developer.branding') ? 'bg-zinc-800 text-purple-400' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Identidade Visual
                </a>
                <a href="{{ route('admin.developer.store') }}" class="flex items-center px-4 py-2 text-zinc-100 hover:bg-zinc-800 rounded-md mt-1 {{ request()->routeIs('admin.developer.store') ? 'bg-zinc-800 text-purple-400' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Informações da Loja
                </a>
            </div>
        </div>
        @endif
    </nav>
    
    <div class="absolute bottom-0 left-0 w-full p-4">
        <div class="border-t border-zinc-800 pt-4">
            <div class="flex items-center text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ auth()->user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="flex items-center text-sm text-zinc-400 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Sair
                </button>
            </form>
        </div>
    </div>
</div>
