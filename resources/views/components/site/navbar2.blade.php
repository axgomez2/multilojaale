@props(['store' => null])

<nav class="bg-black border-b border-slate-800 w-full">
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
                        <img class="h-8 w-auto m-2" src="{{ asset('storage/' . $store->logo_path) }}" alt="{{ $store->name }}">
                    @else
                        <span class="text-lg font-bold">{{ isset($store) ? $store->name : 'Loja Online' }}</span>
                    @endif
                </a>
            </div>
            <!-- Logo desktop -->
            <div class="items-center space-x-2 min-w-max hidden md:flex">
               <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                    @if(isset($store) && $store->logo_path)
                        <img class="h-12 w-auto m-2" src="{{ asset('storage/' . $store->logo_path) }}" alt="{{ $store->name }}">
                    @else
                        <span class="text-lg font-bold">{{ isset($store) ? $store->name : 'Loja Online' }}</span>
                    @endif
                </a>
            </div>
            <!-- Campo de busca centralizado (desktop) -->
            <div class="hidden md:flex flex-1 justify-center">
                <div class="flex w-full max-w-2xl">
                    <input type="text" placeholder="Buscar discos, artistas, gêneros..." class="flex-1 pl-4 pr-2 py-3 rounded-l-full border border-slate-800 bg-slate-100 text-slate-900 text-lg font-semibold placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400" style="font-family: 'Montserrat', 'Inter', 'Arial', sans-serif;" />
                    <button class="bg-yellow-400 text-slate-900 px-4 rounded-r-full flex items-center justify-center hover:bg-yellow-500 hover:text-black transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                        </svg>
                    </button>
                </div>
            </div>
            <!-- Ícone de busca (mobile) -->
            <div class="md:hidden flex items-center">
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
                                <input type="text" placeholder="Buscar..." class="w-full pl-12 pr-12 py-4 rounded-full border border-slate-700 bg-slate-100 text-slate-900 font-semibold placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 shadow-lg transition-all duration-200 text-xl" style="font-family: 'Montserrat', 'Inter', 'Arial', sans-serif;" autofocus />
                                <span class="absolute left-6 top-1/2 transform -translate-y-1/2 text-yellow-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                                    </svg>
                                </span>
                                <button @click="open = false" class="absolute right-6 top-1/2 -translate-y-1/2 md:top-1/2 md:-translate-y-1/2 -translate-y-[60%] md:-translate-y-1/2 text-slate-700 hover:text-yellow-400 text-2xl font-bold">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <!-- Ações à direita (desktop) -->
            <div class="hidden md:flex items-center space-x-6 min-w-max">
                <!-- Overlay para dropdowns -->
                <template x-if="openDropdown">
                    <div class="fixed inset-0 z-40 bg-black bg-opacity-40 transition-opacity"></div>
                </template>
                <!-- Entrar (com dropdown) -->
                <x-site.login-modal />
                <!-- Favoritos -->
                <button class="flex flex-col items-center text-white hover:text-yellow-400 transition-colors" title="Wishlist">
                    <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span class="text-xs">Favoritos</span>
                </button>
                <!-- Carrinho (com dropdown) -->
                <div x-data="{ open: false }" class="relative" @keydown.escape.window="open = false">
                    <button @click="open = !open; $root.openDropdown = open" class="flex flex-col items-center text-white hover:text-yellow-400 transition-colors relative focus:outline-none" title="Carrinho">
                        <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="text-xs">Carrinho</span>
                        <span class="absolute -top-2 -right-2 bg-yellow-400 text-slate-900 text-xs rounded-full px-1.5 py-0.5">2</span>
                    </button>
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
                            <a href="#" class="w-full text-center border border-yellow-400 text-yellow-400 font-bold py-2 rounded hover:bg-yellow-400 hover:text-slate-900 transition-colors">Ver Carrinho Completo</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Divider desktop -->
        <div class="hidden md:block max-w-7xl mx-auto">
            <div class="border-t border-slate-800 opacity-60"></div>
        </div>
        <!-- Menu de categorias -->
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
            <a href="#" class="text-white hover:text-yellow-400 transition-colors text-md font-medium ml-4">Sobre</a>
            <a href="#" class="text-white hover:text-yellow-400 transition-colors text-md font-medium ml-4">Outros</a>
            <a href="#" class="text-white hover:text-yellow-400 transition-colors text-md font-medium ml-4">Playlists</a>
            <a href="#" class="text-white hover:text-yellow-400 transition-colors text-md font-medium ml-4">Area de Membros</a>
           
            <span class="mx-2 text-slate-700">|</span>
            <a href="#" class="text-yellow-400 font-semibold text-md ml-4">OFERTAS</a>
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
        <ul class="flex flex-col gap-2 mt-8">
            <li><a href="#" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors text-center">A partir de R$ 11,69</a></li>
            <li><a href="#" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors text-center">Do Brasil</a></li>
            <li><a href="#" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors text-center">Choice</a></li>
            <li><a href="#" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors text-center">Super cliente</a></li>
            <li><a href="#" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors text-center">Plus</a></li>
            <li><a href="#" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors text-center">SuperOfertas</a></li>
            <li><a href="#" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors text-center">Mais</a></li>
            <li><a href="#" class="block py-2 px-4 rounded hover:bg-yellow-400 hover:text-black transition-colors text-center">Vender</a></li>
        </ul>
    </div>
    <!-- Navbar inferior mobile -->
    <div x-data="{ modalLogin: false }" class="fixed bottom-0 left-0 right-0 z-50 bg-slate-900 border-t border-slate-800 flex justify-around items-center py-2 md:hidden">
        <button @click="modalLogin = true" class="flex flex-col items-center text-white hover:text-yellow-400 transition-colors" title="Entrar">
            <svg class="w-7 h-7 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="text-xs">Entrar</span>
        </button>
        <button class="flex flex-col items-center text-white hover:text-yellow-400 transition-colors" title="Favoritos">
            <svg class="w-7 h-7 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <span class="text-xs">Favoritos</span>
        </button>
        <button class="flex flex-col items-center text-white hover:text-yellow-400 transition-colors" title="Carrinho">
            <svg class="w-7 h-7 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="text-xs">Carrinho</span>
        </button>
        <!-- Modal de login mobile -->
        <template x-if="modalLogin">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
                <div class="relative w-full max-w-xs mx-auto bg-slate-900 border border-slate-700 rounded-lg shadow-lg p-6">
                    <button @click="modalLogin = false" class="absolute top-2 right-2 text-white hover:text-yellow-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    
                    <!-- Using the login-modal component content -->
                    <div class="pt-2">
                        <x-site.login-modal />
                    </div>
                </div>
            </div>
        </template>
    </div>

