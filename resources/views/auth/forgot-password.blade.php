<x-layouts.guest>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-zinc-800 shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-4 text-center">
                <x-app-logo class="w-16 h-16 mx-auto" />
                <h2 class="mt-4 text-2xl font-bold text-zinc-100">Recuperar Senha</h2>
            </div>

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-400">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Forgot Password Form -->
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block font-medium text-sm text-zinc-300">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="block mt-1 w-full rounded-md bg-zinc-700 border-zinc-600 focus:border-zinc-400 focus:ring focus:ring-zinc-400 focus:ring-opacity-50 text-zinc-100">
                </div>

                @error('email')
                    <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
                @enderror

                <div class="flex items-center justify-end mt-4">
                    <a href="{{ route('login') }}" class="text-sm text-zinc-400 hover:text-zinc-300 mr-4">
                        Voltar ao login
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-zinc-700 border border-transparent rounded-md font-semibold text-xs text-zinc-300 uppercase tracking-widest hover:bg-zinc-600 active:bg-zinc-600 focus:outline-none focus:border-zinc-600 focus:ring focus:ring-zinc-400 disabled:opacity-25 transition">
                        Enviar link de recuperação
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.guest>
