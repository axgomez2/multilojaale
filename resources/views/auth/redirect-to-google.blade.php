<x-layouts.guest>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-zinc-800 shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-4 text-center">
                <x-app-logo class="w-16 h-16 mx-auto" />
                <h2 class="mt-4 text-2xl font-bold text-zinc-100">Redirecionando para o Google</h2>
            </div>
            
            <div class="py-4 text-center">
                <p class="text-zinc-300 mb-4">Você será redirecionado para o Google para realizar o login de forma segura.</p>
                <div class="flex justify-center items-center mb-4">
                    <svg class="h-6 w-6 text-zinc-300 animate-spin mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-zinc-300">Aguarde...</span>
                </div>
                <p class="text-zinc-400 text-sm">Caso não seja redirecionado automaticamente, clique no botão abaixo:</p>
                <div class="mt-4">
                    <a id="redirect-button" href="{{ $redirectUrl }}" class="inline-flex items-center px-4 py-2 bg-zinc-700 border border-transparent rounded-md font-semibold text-xs text-zinc-300 uppercase tracking-widest hover:bg-zinc-600 active:bg-zinc-600 focus:outline-none focus:border-zinc-600 focus:ring focus:ring-zinc-400 disabled:opacity-25 transition">
                        Continuar para o Google
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Redireciona automaticamente após 2 segundos
        setTimeout(function() {
            window.location.href = document.getElementById('redirect-button').getAttribute('href');
        }, 2000);
    </script>
</x-layouts.guest>
