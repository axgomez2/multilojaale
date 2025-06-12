{{-- Mobile Login Modal Component --}}

<div class="w-full bg-slate-900 rounded-lg shadow-lg p-6">
    <!-- Logo e título -->
    <div class="text-center mb-6">
        <h1 class="text-xl font-bold text-white mt-2">Entrar na Conta</h1>
        <p class="text-sm text-gray-400">Use seus dados de acesso ou entre com Google</p>
    </div>

    <!-- Formulário de login -->
    <form method="POST" action="{{ route('login.standard') }}" class="space-y-4">
        @csrf

        <!-- Email -->
        <div>
            <div class="mb-2 block">
                <label for="mobile_email" class="text-sm font-medium text-gray-300">E-mail</label>
            </div>
            <input type="email" name="email" id="mobile_email" value="{{ old('email') }}" required
                class="bg-slate-800 border border-slate-700 text-white text-sm rounded-lg focus:ring-yellow-400 focus:border-yellow-400 block w-full p-2.5"
                placeholder="email@exemplo.com" />
        </div>

        <!-- Senha -->
        <div>
            <div class="mb-2 block">
                <label for="mobile_password" class="text-sm font-medium text-gray-300">Senha</label>
            </div>
            <input type="password" name="password" id="mobile_password" required
                class="bg-slate-800 border border-slate-700 text-white text-sm rounded-lg focus:ring-yellow-400 focus:border-yellow-400 block w-full p-2.5"
                placeholder="••••••••" autocomplete="current-password" />
        </div>

        <!-- Lembrar de mim -->
        <div class="flex items-center gap-2">
            <input id="mobile_remember_me" type="checkbox" name="remember"
                class="w-4 h-4 text-yellow-400 bg-slate-800 border-slate-700 rounded focus:ring-yellow-400 focus:ring-2">
            <label for="mobile_remember_me" class="text-sm text-gray-300">Lembrar de mim</label>
        </div>

        <!-- Botão de login -->
        <button type="submit"
            class="w-full text-slate-900 bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
            Entrar
        </button>
    </form>

    <!-- Links -->
    <div class="mt-4 flex justify-between text-sm">
        <a href="{{ route('register') }}" class="text-yellow-400 hover:underline">Criar conta</a>
        <a href="{{ route('password.request') }}" class="text-yellow-400 hover:underline">Esqueci a senha</a>
    </div>

    <!-- Divider -->
    <div class="flex items-center justify-center my-4">
        <div class="flex-grow border-t border-slate-700"></div>
        <span class="flex-shrink mx-4 text-gray-400 text-sm">ou</span>
        <div class="flex-grow border-t border-slate-700"></div>
    </div>

    <!-- Login com Google -->
    <a href="{{ route('login.google') }}"
        class="w-full flex items-center justify-center px-4 py-2 border border-slate-700 text-sm font-medium text-white bg-slate-800 hover:bg-slate-700 rounded-lg focus:ring-4 focus:ring-slate-600 transition-colors">
        <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
        </svg>
        Entrar com Google
    </a>
</div>
