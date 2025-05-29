@props(['id' => 'login-modal'])

<div x-data="{ open: false }" class="relative" @keydown.escape.window="open = false">
    <!-- Toggle Button -->
    <button @click="open = !open; $root.openDropdown = open" class="flex flex-col items-center text-white hover:text-yellow-400 transition-colors focus:outline-none" title="Usuário">
        <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        @auth
        <span class="text-xs">{{ auth()->user()->name }}</span>
        @else
        <span class="text-xs">Entrar</span>
        @endauth
    </button>
    
    <!-- Modal Content -->
    <div x-show="open" @click.away="open = false; $root.openDropdown = false" class="absolute right-0 mt-2 w-64 bg-white border border-slate-700 rounded-lg shadow-lg z-50 p-4" x-cloak>
    @auth    
    <h3 class="text-black text-lg font-semibold mb-2">Minha conta:</h3>
        
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Admin Dashboard
                        </a>
                    @else
                        <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
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
        <!-- Login Form -->
        <h3 class="text-black text-lg font-semibold mb-2">Bem-vindo!</h3>
        <form method="POST" action="{{ route('login.standard') }}" class="space-y-3">
            @csrf
            
            <!-- Email Address -->
            <input type="email" id="email" name="email" value="{{ old('email') }}" 
                   placeholder="E-mail" required autofocus
                   class="w-full px-3 py-2 rounded bg-slate-800 text-white border border-slate-700 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
            
            <!-- Password -->
            <input type="password" id="password" name="password" required
                   placeholder="Senha"
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
        </a>@endauth
    </div>
    
</div>
