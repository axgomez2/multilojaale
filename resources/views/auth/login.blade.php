<x-layouts.guest>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-zinc-800 shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-4 text-center">
                <x-app-logo class="w-16 h-16 mx-auto" />
                <h2 class="mt-4 text-2xl font-bold text-zinc-100">Login</h2>
            </div>

            <!-- Email and Password Login Form -->
            <form method="POST" action="{{ route('login.standard') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block font-medium text-sm text-zinc-300">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="block mt-1 w-full rounded-md bg-zinc-700 border-zinc-600 focus:border-zinc-400 focus:ring focus:ring-zinc-400 focus:ring-opacity-50 text-zinc-100">
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label for="password" class="block font-medium text-sm text-zinc-300">Password</label>
                    <input id="password" type="password" name="password" required
                        class="block mt-1 w-full rounded-md bg-zinc-700 border-zinc-600 focus:border-zinc-400 focus:ring focus:ring-zinc-400 focus:ring-opacity-50 text-zinc-100">
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded bg-zinc-700 border-zinc-600 text-zinc-400 focus:ring-zinc-400">
                        <span class="ml-2 text-sm text-zinc-300">Remember me</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a href="{{ route('register') }}" class="text-sm text-zinc-400 hover:text-zinc-300 mr-4">
                        Criar conta
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-zinc-700 border border-transparent rounded-md font-semibold text-xs text-zinc-300 uppercase tracking-widest hover:bg-zinc-600 active:bg-zinc-600 focus:outline-none focus:border-zinc-600 focus:ring focus:ring-zinc-400 disabled:opacity-25 transition">
                        Login
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative flex items-center justify-center my-6">
                <div class="flex-grow border-t border-zinc-600"></div>
                <span class="flex-shrink mx-4 text-zinc-400">or</span>
                <div class="flex-grow border-t border-zinc-600"></div>
            </div>

            <!-- WorkOS OAuth Login Button -->
            <div class="flex justify-center">
                <a href="{{ route('login.google') }}" class="inline-flex items-center px-4 py-2 bg-zinc-700 border border-transparent rounded-md font-semibold text-xs text-zinc-300 uppercase tracking-widest hover:bg-zinc-600 active:bg-zinc-600 focus:outline-none focus:border-zinc-600 focus:ring focus:ring-zinc-400 disabled:opacity-25 transition">
                    <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                    </svg>
                    Login with Google
                </a>
            </div>
        </div>
    </div>
</x-layouts.guest>
