@props(['store' => null])

<nav class="bg-stone-800 border-b border-slate-800 w-full">
    <div class="max-w-7xl mx-auto flex items-center justify-between py-3 px-4 md:py-3 md:px-4">
        <!-- Botão menu mobile -->
        <div class="flex items-center md:hidden">
            <button data-drawer-target="drawer-navigation" data-drawer-show="drawer-navigation" aria-controls="drawer-navigation" class="p-2 text-white hover:text-yellow-400 transition-colors focus:outline-none mr-2">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                @if(isset($store) && $store->logo_path)
                    <img class="h-16 w-auto m-2" src="{{ asset('storage/' . $store->logo_path) }}" alt="{{ $store->name }}">
                @else
                    <span class="text-lg font-bold">{{ isset($store) ? $store->name : 'Loja Online' }}</span>
                @endif
            </a>
        </div>
        <!-- Logo desktop -->
        <div class="items-center space-x-2 min-w-max hidden md:flex">
            <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                @if(isset($store) && $store->logo_path)
                    <img class="sm:h-12 xl:h-16 xl:w-auto sm:w-auto m-2" src="{{ asset('storage/' . $store->logo_path) }}" alt="{{ $store->name }}">
                @else
                    <span class="text-lg font-bold">{{ isset($store) ? $store->name : 'Loja Online' }}</span>
                @endif
            </a>
        </div>
        <!-- Campo de busca centralizado (desktop) -->
        <div class="hidden md:flex flex-1 justify-center">
            <form action="{{ route('site.search') }}" method="GET" class="flex w-full max-w-2xl">
                <input type="text" name="q" placeholder="Buscar discos, artistas, gêneros..." class="flex-1 pl-4 pr-2 py-3 rounded-l-full border border-slate-800 bg-slate-100 text-stone-900 text-lg font-semibold placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400" style="font-family: 'Montserrat', 'Inter', 'Arial', sans-serif;" />
                <button type="submit" class="bg-yellow-400 text-stone-900 px-4 rounded-r-full flex items-center justify-center hover:bg-yellow-500 hover:text-black transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                    </svg>
                </button>
            </form>
        </div>
        <!-- Ícones mobile (busca, usuário, favoritos, carrinho) -->
        <div class="md:hidden flex items-center space-x-2">
            <!-- Ícone de busca -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="p-2 text-white hover:text-yellow-400 transition-colors focus:outline-none" title="Buscar">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                    </svg>
                </button>
                <!-- Overlay e campo de busca centralizado -->
                <template x-if="open">
                    <div class="fixed inset-0 z-50 flex items-start justify-center bg-black bg-opacity-60">
                        <div class="relative w-full max-w-md mx-auto px-4 mt-[50px]">
                            <form action="{{ route('site.search') }}" method="GET" class="relative">
                                <input type="text" name="q" placeholder="Buscar discos, artistas, gêneros..." class="w-full pl-12 pr-12 py-4 rounded-full border border-slate-700 bg-slate-100 text-stone-900 font-semibold placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 shadow-lg transition-all duration-200 text-xl" style="font-family: 'Montserrat', 'Inter', 'Arial', sans-serif;" autofocus />
                                <button type="submit" class="absolute left-6 top-1/2 transform -translate-y-1/2 text-yellow-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                                    </svg>
                                </button>
                            </form>
                            <button @click="open = false" class="absolute right-6 top-1/2 -translate-y-1/2 md:top-1/2 md:-translate-y-1/2 -translate-y-[60%] md:-translate-y-1/2 text-slate-700 hover:text-yellow-400 text-2xl font-bold">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
            
            <!-- Ícone de usuário/login -->
            <div x-data="{ modalLogin: false }">
                @auth
                <!-- Usuário logado: link para perfil -->
                <a href="{{ route('site.profile.index') }}" class="p-2 text-white hover:text-yellow-400 transition-colors focus:outline-none" title="Minha Conta">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </a>
                
                <!-- Ícone de favoritos (apenas para usuários logados) -->
                <a href="{{ route('site.wishlist.index') }}" class="p-2 text-white hover:text-yellow-400 transition-colors focus:outline-none" title="Lista de Desejos">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </a>
                
                <!-- Ícone de carrinho (apenas para usuários logados) -->
                <a href="{{ route('site.cart.index') }}" class="p-2 text-white hover:text-yellow-400 transition-colors focus:outline-none" title="Carrinho">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </a>
                @else
                <!-- Usuário não logado: botão para modal de login com ícone de sign in -->
                <button id="mobile-login-button" class="p-2 text-white hover:text-yellow-400 transition-colors focus:outline-none" title="Entrar">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </button>
                @endauth
                
                <!-- Modal de login mobile - Implementação sem Alpine.js template -->
                <div id="mobile-login-modal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden overflow-y-auto overflow-x-hidden">
                    <!-- Overlay com opacidade -->
                    <div class="fixed inset-0 bg-black opacity-50"></div>
                    
                    <!-- Container do modal -->
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                        <div class="relative w-full max-w-xs mx-auto bg-slate-900 border border-slate-700 rounded-lg shadow-xl overflow-y-auto max-h-[90vh] z-[60]">
                            <button onclick="document.getElementById('mobile-login-modal').classList.add('hidden')" class="absolute top-2 right-2 text-white hover:text-yellow-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            
                            <!-- Usando o novo componente de login mobile completo -->
                            <x-site.mobile-login-modal />
                        </div>
                    </div>
                </div>
                
                <script>
                    // Script para controlar o modal de login mobile
                    document.addEventListener('DOMContentLoaded', function() {
                        // Quando o botão de login é clicado
                        document.getElementById('mobile-login-button').addEventListener('click', function() {
                            document.getElementById('mobile-login-modal').classList.remove('hidden');
                        });
                    });
                </script>
            </div>
        </div>
        <!-- Ações à direita (desktop) -->
        <div class="hidden md:flex items-center space-x-6 min-w-max">
            <!-- Overlay para dropdowns -->
            <template x-if="openDropdown">
                <div class="fixed inset-0 z-40 bg-black bg-opacity-40 transition-opacity"></div>
            </template>
            
            <!-- Usuário (com dropdown/modal) -->
            <div x-data="{ open: false }" class="relative" @keydown.escape.window="open = false">
                @auth
                <!-- Usuário logado: Dropdown com opções -->
                <button @click="open = !open; $root.openDropdown = open" class="flex flex-col items-center text-white hover:text-yellow-400 transition-colors focus:outline-none" title="Minha Conta">
                    <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
         
                    <span class="text-xs">{{ auth()->user()->name }}</span>
                </button>

                <!-- Dropdown para usuário logado -->
                <div x-show="open" @click.away="open = false; $root.openDropdown = false" 
                     class="absolute right-0 mt-2 w-64 bg-white border border-slate-700 rounded-lg shadow-lg z-50" x-cloak>
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-black text-lg font-semibold">Olá, {{ auth()->user()->name }}!</h3>
                        <p class="text-gray-500 text-sm">{{ auth()->user()->email }}</p>
                    </div>
                    
                    <ul class="py-2">
                        <!-- Link para perfil -->
                        <li>
                            <a href="{{ route('site.profile.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Meu Perfil
                            </a>
                        </li>
                        
                        <!-- Link para wishlist -->
                        <li>
                            <a href="{{ route('site.wishlist.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                Lista de Desejos
                            </a>
                        </li>
                        
                        <!-- Link para wantlist -->
                        <li>
                            <a href="{{ route('site.wantlist.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                Lista de Interesse
                            </a>
                        </li>
                        
                        <!-- Link para meus pedidos -->
                        <li>
                            <a href="{{ route('site.profile.orders.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                Meus Pedidos
                            </a>
                        </li>
                        
                        @if(auth()->user()->isAdmin())
                        <!-- Link para dashboard admin (se for admin) -->
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Admin Dashboard
                            </a>
                        </li>
                        @endif
                    </ul>
                    
                    <!-- Botão de logout -->
                    <div class="p-3 border-t border-gray-200">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center px-4 py-2 text-red-600 hover:bg-red-50 rounded transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <!-- Usuário não logado: Botão para abrir modal de login -->
                <button @click="open = !open; $root.openDropdown = open" class="flex flex-col items-center text-white hover:text-yellow-400 transition-colors focus:outline-none" title="Entrar">
                    <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-xs">Entrar</span>
                </button>

                <!-- Modal de Login para usuário não logado -->
                <div x-show="open" @click.away="open = false; $root.openDropdown = false" 
                     class="absolute right-0 mt-2 w-64 bg-white border border-slate-700 rounded-lg shadow-lg z-50 p-4" x-cloak>
                    <h3 class="text-black text-lg font-semibold mb-2">Bem-vindo!</h3>
                    <form method="POST" action="{{ route('login.standard') }}" class="space-y-3">
                        @csrf
                        
                        <!-- Email Address -->
                        <input type="email" id="email" name="email" value="{{ old('email') }}" 
                               placeholder="E-mail" required autofocus
                               class="w-full px-3 py-2 rounded bg-slate-800 text-white border border-slate-700 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                        
                        <!-- Password -->
                        <input type="password" id="password" name="password" required
                               placeholder="Senha" autocomplete="current-password"
                               class="w-full px-3 py-2 rounded bg-slate-800 text-white border border-slate-700 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                        
                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <input id="remember_me" type="checkbox" name="remember" 
                                   class="rounded bg-slate-800 border-slate-700 text-yellow-400 focus:ring-yellow-400">
                            <span class="ml-2 text-sm text-slate-300">Lembrar-me</span>
                        </div>
                        
                        <!-- Login Button -->
                        <button type="submit" class="w-full bg-yellow-400 text-slate-900 font-bold py-2 rounded hover:bg-yellow-500 transition-colors">
                            Entrar
                        </button>
                    </form>
                    
                    <!-- Links -->
                    <div class="mt-3 flex justify-between text-sm">
                        <a href="{{ route('register') }}" class="text-yellow-400 hover:underline">Criar conta</a>
                        <a href="{{ route('password.request') }}" class="text-yellow-400 hover:underline">Esqueci a senha</a>
                    </div>
                    
                    <!-- Google Login -->
                    <div class="relative flex items-center justify-center my-3">
                        <div class="flex-grow border-t border-slate-700"></div>
                        <span class="flex-shrink mx-4 text-slate-400">ou</span>
                        <div class="flex-grow border-t border-slate-700"></div>
                    </div>
                    
                    <a href="{{ route('login.google') }}" class="flex items-center justify-center w-full px-3 py-2 bg-slate-800 text-white rounded border border-slate-700 hover:bg-slate-700 transition-colors">
                        <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                        </svg>
                        <span>Login com Google</span>
                    </a>
                </div>
                @endauth
                @guest
                
                @endguest
            </div>

            <!-- Favoritos -->
            @auth
            <a href="{{ route('site.wishlist.index') }}" class="flex flex-col items-center text-white hover:text-yellow-400 transition-colors" title="Lista de Desejos">
                <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span class="text-xs">Favoritos</span>
            </a>
            @else
            <!-- Usuário não logado: Exibir toast ao clicar -->
            <button id="show-wishlist-toast" class="flex flex-col items-center text-white hover:text-yellow-400 transition-colors" title="Favoritos">
                <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span class="text-xs">Favoritos</span>
            </button>
            @endauth
            
            <!-- Carrinho (com dropdown) -->
            <div x-data="{ open: false }" class="relative" @keydown.escape.window="open = false">
                <a href="{{ route('site.cart.index') }}" class="flex flex-col items-center text-white hover:text-yellow-400 transition-colors relative focus:outline-none" title="Carrinho">
                    <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-xs">Carrinho</span>
                    <!-- Contador de itens no carrinho removido para evitar erro de função -->
                </a>
                <div x-show="open" @click.away="open = false; $root.openDropdown = false" class="absolute right-0 mt-2 w-80 bg-slate-900 border border-slate-700 rounded-lg shadow-lg z-50 p-4" x-cloak>
                    <h3 class="text-white text-lg font-semibold mb-2">Resumo do Carrinho</h3>
                    <ul class="divide-y divide-slate-800 max-h-60 overflow-y-auto">
                        <li class="flex items-center py-2">
                            <img src="https://via.placeholder.com/40" alt="Produto 1" class="w-10 h-10 rounded mr-3">
                            <div class="flex-1">
                                <span class="block text-white font-medium">Disco 1</span>
                                <span class="block text-yellow-400 text-xs">R$ 99,90</span>
                            </div>
                            <span class="text-white text-xs">x1</span>
                        </li>
                        <li class="flex items-center py-2">
                            <img src="https://via.placeholder.com/40" alt="Produto 2" class="w-10 h-10 rounded mr-3">
                            <div class="flex-1">
                                <span class="block text-white font-medium">Disco 2</span>
                                <span class="block text-yellow-400 text-xs">R$ 79,90</span>
                            </div>
                            <span class="text-white text-xs">x1</span>
                        </li>
                        <!-- Adicione até 5 itens -->
                    </ul>
                    <div class="mt-4 flex flex-col gap-2">
                        <a href="#" class="w-full text-center bg-yellow-400 text-slate-900 font-bold py-2 rounded hover:bg-yellow-500 transition-colors">Finalizar Compra</a>
                        <a href="{{ route('site.cart.index') }}" class="w-full text-center border border-yellow-400 text-yellow-400 font-bold py-2 rounded hover:bg-yellow-400 hover:text-slate-900 transition-colors">Ver Carrinho Completo</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="hidden md:block max-w-7xl mx-auto">
            <div class="border-t border-slate-800 opacity-60"></div>
        </div>
        </div>
        <div class="max-w-7xl mx-auto hidden md:flex items-center gap-2 py-2 px-4 justify-center relative" x-data="{ openMegaMenu: false }">
            <button id="mega-menu-vinil-button" type="button" class="flex items-center bg-white px-4 py-2 rounded-full text-black font-medium hover:bg-yellow-400 hover:text-slate-900 transition-colors focus:outline-none" aria-expanded="false" aria-haspopup="true" @click="openMegaMenu = !openMegaMenu">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Discos de Vinil
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <span class="text-yellow-400 font-semibold text-md ml-4">Inicio</span>
            <a href="{{route('about')}}" class="text-white hover:text-yellow-400 transition-colors text-md font-medium ml-4">Sobre</a>
            <a href="{{route('deejays')}}" class="text-white hover:text-yellow-400 transition-colors text-md font-medium ml-4">Vinyls 4 DJ's</a>
            <a href="{{route('playlist')}}" class="text-white hover:text-yellow-400 transition-colors text-md font-medium ml-4">Playlists</a>
            <a href="{{route('member.index')}}" class="text-white hover:text-yellow-400 transition-colors text-md font-medium ml-4">Area de Membros</a>
           
            <span class="mx-2 text-slate-700">|</span>
            <a href="{{route('offer.index')}}" class="text-yellow-400 font-semibold text-md ml-4">OFERTAS</a>
            <!-- Mega menu full width, alinhado ao container -->
            <template x-if="openMegaMenu">
                <div>
                    <!-- Overlay -->
                    <div class="fixed inset-0 bg-black bg-opacity-40 z-40" @click="openMegaMenu = false"></div>
                    <!-- Mega menu -->
                    <div class="absolute left-0 right-0 z-50 bg-white border-t border-b border-slate-700 shadow-lg w-full px-8 py-8" style="top: calc(100% + 8px);">
                        <div class="grid grid-cols-4 gap-8">
                            <!-- 3 colunas de categorias -->
                            @php
                                $chunks = $categories->chunk(ceil($categories->count() / 3));
                            @endphp
                            <div class="col-span-3 grid grid-cols-3 gap-8 border-r border-slate-300 pr-8">
                                @foreach ($chunks as $chunk)
                                    <ul class="space-y-3">
                                        @foreach ($chunk as $category)
                                            <li>
                                                <a href="{{ route('site.category', $category->slug) }}" class="block py-2 px-2 rounded hover:bg-yellow-400 hover:text-slate-900 transition-colors text-black font-medium">{{ $category->nome }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            </div>
                            <!-- Card 'Todos os discos' -->
                            <div class="flex flex-col justify-between bg-gray-500 bg-center bg-no-repeat bg-cover rounded-lg bg-blend-multiply hover:bg-blend-soft-light p-6" style="background-image: url('/docs/images/dashboard-overview.png')">
                                <div>
                                    <p class="mb-4 font-extrabold leading-tight tracking-tight text-white text-lg">Confira todos os discos disponíveis!</p>
                                </div>
                                <a href="{{ route('site.products') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-center text-white border border-white rounded-lg hover:bg-white hover:text-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-700 transition-colors">
                                    Ver todos os discos
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 14 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </nav>
    <!-- Drawer Menu Mobile -->
    <div id="drawer-navigation" class="fixed top-0 left-0 z-40 w-64 h-screen p-4 overflow-y-auto transition-transform -translate-x-full bg-white text-gray-900 md:hidden" tabindex="-1" aria-labelledby="drawer-navigation-label">
        <h5 id="drawer-navigation-label" class="text-base font-semibold text-gray-900 uppercase mb-4">Menu</h5>
        <button type="button" data-drawer-hide="drawer-navigation" aria-controls="drawer-navigation" class="text-gray-400 bg-transparent hover:bg-yellow-400 hover:text-black rounded-lg text-sm p-1.5 absolute top-2.5 right-2.5 inline-flex items-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        
        <!-- Links principais do menu mobile -->
        <ul class="flex flex-col gap-2 mt-8">
            <li>
                <a href="{{ route('home') }}" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors font-semibold">Início</a>
            </li>
            
            <!-- Dropdown para Discos de Vinil -->
            <li x-data="{open: false}" class="relative">
                <button @click="open = !open" class="flex items-center justify-between w-full py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors">
                    <span>Discos de Vinil</span>
                    <svg class="w-4 h-4" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <div x-show="open" x-transition class="pl-4 mt-2 space-y-2">
                    <!-- Link para todos os discos -->
                    <a href="{{ route('site.products') }}" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors text-sm font-medium border-l-2 border-yellow-400">
                        Ver todos os discos
                    </a>
                    
                    <!-- Categorias -->
                    @foreach($categories as $category)
                        <a href="{{ route('site.category', $category->slug) }}" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors text-sm border-l-2 border-gray-200">
                            {{ $category->nome }}
                        </a>
                    @endforeach
                </div>
            </li>
            
            <li>
                <a href="{{route('about')}}" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors">Sobre</a>
            </li>
            
            <li>
                <a href="{{route('deejays')}}" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors">Vinyls 4 DJ's</a>
            </li>
            
            <li>
                <a href="{{route('playlist')}}" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors">Playlists</a>
            </li>
            
            <li>
                <a href="{{route('member.index')}}" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors">Area de Membros</a>
            </li>
            
            <li>
                <a href="{{route('offer.index')}}" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors font-semibold text-yellow-500">OFERTAS</a>
            </li>
            
            <!-- Área do usuário para mobile -->
            @auth
            <li class="mt-4 pt-4 border-t border-gray-200">
                <span class="block px-4 text-sm text-gray-500">Olá, {{ auth()->user()->name }}</span>
                
                <a href="{{ route('site.profile.index') }}" class="flex items-center py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors mt-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Meu Perfil
                </a>
                
                <a href="{{ route('site.wishlist.index') }}" class="flex items-center py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    Lista de Desejos
                </a>
                
                <a href="{{ route('site.wantlist.index') }}" class="flex items-center py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Lista de Interesse
                </a>
                
                <a href="{{ route('site.profile.orders.index') }}" class="flex items-center py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Meus Pedidos
                </a>
                
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="flex items-center py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Admin Dashboard
                </a>
                @endif
                
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="flex w-full items-center py-2 px-4 rounded hover:bg-red-100 text-red-600 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Sair
                    </button>
                </form>
            </li>
            @endauth
        </ul>
    </div>
    <!-- A navbar inferior mobile foi removida para evitar sobreposição com o player -->

