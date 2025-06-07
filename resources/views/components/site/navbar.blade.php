@props(['store' => null])

<nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 100">
  <rect width="300" height="100" fill="#000"/>
  <!-- Disco de vinil -->
  <circle cx="50" cy="50" r="40" fill="#111"/>
  <circle cx="50" cy="50" r="12" fill="#fff"/>
  <circle cx="50" cy="50" r="40" fill="none" stroke="#ffcc00" stroke-width="2"/>

  <!-- Texto -->
  <text x="100" y="45" font-family="Arial Black, sans-serif" font-size="32" fill="#fff">RDV</text>
  <text x="100" y="80" font-family="Arial, sans-serif" font-size="20" fill="#ccc">DISCOS</text>
</svg>
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
