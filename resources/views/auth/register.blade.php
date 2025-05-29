<x-app-layout>
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-8">
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
        <!-- Cabeçalho -->
        <div class="text-center mb-6">
           
            <h1 class="text-2xl font-bold text-gray-900 mt-4">Criar Conta</h1>
            <p class="text-sm text-gray-500">Preencha os dados para se cadastrar</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Formulário em Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Coluna esquerda: obrigatórios -->
                <div class="space-y-4">
                    <!-- Nome -->
                    <div>
                        <label for="name" class="text-sm font-medium text-gray-700 block mb-1">
                            Nome <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                        @error('name')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="text-sm font-medium text-gray-700 block mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Senha -->
                    <div>
                        <label for="password" class="text-sm font-medium text-gray-700 block mb-1">
                            Senha <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" id="password" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                        @error('password')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirmar Senha -->
                    <div>
                        <label for="password_confirmation" class="text-sm font-medium text-gray-700 block mb-1">
                            Confirmar Senha <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                    </div>
                </div>

                <!-- Coluna direita: opcionais -->
                <div class="space-y-4">
                    <!-- Telefone -->
                    <div>
                        <label for="phone" class="text-sm font-medium text-gray-700 block mb-1">Telefone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                        @error('phone')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- CPF -->
                    <div>
                        <label for="cpf" class="text-sm font-medium text-gray-700 block mb-1">CPF</label>
                        <input type="text" name="cpf" id="cpf" value="{{ old('cpf') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                        @error('cpf')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Data de Nascimento -->
                    <div>
                        <label for="birth_date" class="text-sm font-medium text-gray-700 block mb-1">Data de Nascimento</label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                        @error('birth_date')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Ações -->
            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('login') }}" class="text-sm text-green-600 hover:underline">
                    Já tem uma conta?
                </a>
                <button type="submit"
                    class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Registrar
                </button>
            </div>
        </form>

        <!-- Divider -->
        <div class="flex items-center justify-center my-6 relative">
            <hr class="w-full border-gray-200">
            <span class="absolute bg-white px-3 text-gray-400 text-sm">ou</span>
        </div>

        <!-- Botão Google -->
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
