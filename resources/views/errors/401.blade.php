<x-app-layout>
    <section class="bg-white dark:bg-zinc-900">
        <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
            <div class="mx-auto max-w-screen-sm text-center">
                <h1 class="mb-4 text-7xl tracking-tight font-extrabold lg:text-9xl text-yellow-400 dark:text-yellow-400">401</h1>
                <p class="mb-4 text-3xl tracking-tight font-bold text-zinc-900 md:text-4xl dark:text-white">Não autorizado</p>
                <p class="mb-4 text-lg font-light text-zinc-500 dark:text-zinc-400">Você precisa estar autenticado para acessar esta página. Por favor, faça login para continuar.</p>
                <a href="{{ route('login') }}" class="inline-flex text-zinc-900 bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:focus:ring-yellow-900 my-4">Fazer login</a>
                <a href="{{ route('home') }}" class="inline-flex text-yellow-400 bg-transparent border border-yellow-400 hover:bg-yellow-400 hover:text-zinc-900 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:focus:ring-yellow-900 my-4 ml-2">Voltar para a página inicial</a>
            </div>   
        </div>
    </section>
</x-app-layout>
