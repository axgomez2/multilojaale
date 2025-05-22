<x-layouts.guest>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-zinc-800 shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-4 text-center">
                <x-app-logo class="w-16 h-16 mx-auto" />
                <h2 class="mt-4 text-2xl font-bold text-zinc-100">Redefinir Senha</h2>
            </div>

            <!-- Reset Password Form -->
            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <!-- Hidden Token -->
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email Address -->
                <div>
                    <label for="email" class="block font-medium text-sm text-zinc-300">Email</label>
                    <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus
                        class="block mt-1 w-full rounded-md bg-zinc-700 border-zinc-600 focus:border-zinc-400 focus:ring focus:ring-zinc-400 focus:ring-opacity-50 text-zinc-100">
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label for="password" class="block font-medium text-sm text-zinc-300">Nova Senha</label>
                    <input id="password" type="password" name="password" required
                        class="block mt-1 w-full rounded-md bg-zinc-700 border-zinc-600 focus:border-zinc-400 focus:ring focus:ring-zinc-400 focus:ring-opacity-50 text-zinc-100">
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <label for="password_confirmation" class="block font-medium text-sm text-zinc-300">Confirmar Nova Senha</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="block mt-1 w-full rounded-md bg-zinc-700 border-zinc-600 focus:border-zinc-400 focus:ring focus:ring-zinc-400 focus:ring-opacity-50 text-zinc-100">
                </div>

                @error('email')
                    <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
                @enderror

                @error('password')
                    <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
                @enderror

                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-zinc-700 border border-transparent rounded-md font-semibold text-xs text-zinc-300 uppercase tracking-widest hover:bg-zinc-600 active:bg-zinc-600 focus:outline-none focus:border-zinc-600 focus:ring focus:ring-zinc-400 disabled:opacity-25 transition">
                        Redefinir Senha
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.guest>
