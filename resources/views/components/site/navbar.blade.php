@props(['store' => null])

<nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                    @if(isset($store) && $store->logo_path)
                        <img class="h-8 w-auto" src="{{ asset('storage/' . $store->logo_path) }}" alt="{{ $store->name }}">
                    @else
                        <span class="text-lg font-bold">{{ isset($store) ? $store->name : 'Loja Online' }}</span>
                    @endif
                </a>
            </div>
            
            <div class="flex items-center">
                <!-- Login / Dashboard -->
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Admin Dashboard
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Minha Conta
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Sair
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Entrar
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium ml-4">
                            Cadastre-se
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>
