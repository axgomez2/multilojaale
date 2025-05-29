<x-app-layout>

<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
        <!-- Logo e título -->
        <div class="text-center mb-6">
       
            <h1 class="text-2xl font-bold text-gray-900 mt-4">Entrar na Conta</h1>
            <p class="text-sm text-gray-500">Use seus dados de acesso ou entre com Google</p>
        </div>

        <!-- Formulário de login -->
        <form method="POST" action="{{ route('login.standard') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div id="email">
                <div class="mb-2 block">
                    <label for="email" class="text-sm font-medium text-gray-700">E-mail</label>
                </div>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5"
                    placeholder="email@exemplo.com" />
            </div>

            <!-- Senha -->
            <div id="password">
                <div class="mb-2 block">
                    <label for="password" class="text-sm font-medium text-gray-700">Senha</label>
                </div>
                <input type="password" name="password" id="password" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5"
                    placeholder="••••••••" />
            </div>

            <!-- Lembrar de mim -->
            <div class="flex items-center gap-2">
                <input id="remember_me" type="checkbox" name="remember"
                    class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 focus:ring-2">
                <label for="remember_me" class="text-sm text-gray-600">Lembrar de mim</label>
            </div>

            <!-- Ações -->
            <div class="flex items-center justify-between">
                <a href="{{ route('register') }}" class="text-sm text-primary-600 hover:underline">Criar conta</a>
                <button type="submit"
                    class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    Entrar
                </button>
            </div>
        </form>

        <!-- Divider -->
        <div class="flex items-center justify-center my-6">
            <hr class="w-full border-gray-200">
            <span class="absolute bg-white px-3 text-gray-400 text-sm">ou</span>
        </div>

        <!-- Login com Google -->
        <div class="flex justify-center">
            <a href="{{ route('login.google') }}"
                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 rounded-lg focus:ring-4 focus:ring-gray-200">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-5 h-5 mr-2"
                    alt="Google logo">
                Entrar com Google
            </a>
        </div>
    </div>
</div>



</x-app-layout>
