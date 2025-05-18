<x-layouts.guest>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-zinc-800 shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-4 text-center">
                <x-app-logo class="w-16 h-16 mx-auto" />
                <h2 class="mt-4 text-2xl font-bold text-zinc-100">Criar Conta</h2>
            </div>

            <!-- Formulário de Registro -->
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Nome -->
                <div>
                    <label for="name" class="block font-medium text-sm text-zinc-300">Nome</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="block mt-1 w-full rounded-md bg-zinc-700 border-zinc-600 focus:border-zinc-400 focus:ring focus:ring-zinc-400 focus:ring-opacity-50 text-zinc-100">
                    @error('name')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <label for="email" class="block font-medium text-sm text-zinc-300">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="block mt-1 w-full rounded-md bg-zinc-700 border-zinc-600 focus:border-zinc-400 focus:ring focus:ring-zinc-400 focus:ring-opacity-50 text-zinc-100">
                    @error('email')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Telefone -->
                <div class="mt-4">
                    <label for="phone" class="block font-medium text-sm text-zinc-300">Telefone</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                        class="block mt-1 w-full rounded-md bg-zinc-700 border-zinc-600 focus:border-zinc-400 focus:ring focus:ring-zinc-400 focus:ring-opacity-50 text-zinc-100">
                    @error('phone')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- CPF -->
                <div class="mt-4">
                    <label for="cpf" class="block font-medium text-sm text-zinc-300">CPF</label>
                    <input id="cpf" type="text" name="cpf" value="{{ old('cpf') }}"
                        class="block mt-1 w-full rounded-md bg-zinc-700 border-zinc-600 focus:border-zinc-400 focus:ring focus:ring-zinc-400 focus:ring-opacity-50 text-zinc-100">
                    @error('cpf')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Data de Nascimento -->
                <div class="mt-4">
                    <label for="birth_date" class="block font-medium text-sm text-zinc-300">Data de Nascimento</label>
                    <input id="birth_date" type="date" name="birth_date" value="{{ old('birth_date') }}"
                        class="block mt-1 w-full rounded-md bg-zinc-700 border-zinc-600 focus:border-zinc-400 focus:ring focus:ring-zinc-400 focus:ring-opacity-50 text-zinc-100">
                    @error('birth_date')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Senha -->
                <div class="mt-4">
                    <label for="password" class="block font-medium text-sm text-zinc-300">Senha</label>
                    <input id="password" type="password" name="password" required
                        class="block mt-1 w-full rounded-md bg-zinc-700 border-zinc-600 focus:border-zinc-400 focus:ring focus:ring-zinc-400 focus:ring-opacity-50 text-zinc-100">
                    @error('password')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirmação de Senha -->
                <div class="mt-4">
                    <label for="password_confirmation" class="block font-medium text-sm text-zinc-300">Confirmar Senha</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="block mt-1 w-full rounded-md bg-zinc-700 border-zinc-600 focus:border-zinc-400 focus:ring focus:ring-zinc-400 focus:ring-opacity-50 text-zinc-100">
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a href="{{ route('login') }}" class="text-sm text-zinc-400 hover:text-zinc-300 mr-4">
                        Já tem uma conta?
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-zinc-700 border border-transparent rounded-md font-semibold text-xs text-zinc-300 uppercase tracking-widest hover:bg-zinc-600 active:bg-zinc-600 focus:outline-none focus:border-zinc-600 focus:ring focus:ring-zinc-400 disabled:opacity-25 transition">
                        Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.guest>
