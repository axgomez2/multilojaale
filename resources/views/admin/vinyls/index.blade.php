<x-admin-layout title="Discos">
<div class="p-4">
    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="flex flex-col gap-4 mb-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-900">Todos os Discos</h1>
                <a href="{{ route('admin.vinyls.create') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                    Adicionar novo disco
                </a>
            </div>

            <form method="GET" action="{{ route('admin.vinyls.index') }}" class="flex gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="search"
                               name="search"
                               value="{{ request('search') }}"
                               class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Buscar por título, artista ou gravadora...">
                    </div>
                </div>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">
                    Buscar
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.vinyls.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200">
                        Limpar
                    </a>
                @endif
            </form>
        </div>

        @if($vinyls->isEmpty())
            <div class="flex flex-col items-center justify-center py-12">
                <div class="flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-gray-100">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                @if(request('search'))
                    <h3 class="mb-2 text-xl font-bold text-gray-900">Nenhum resultado encontrado</h3>
                    <p class="text-gray-500">Tente usar termos diferentes na busca</p>
                @else
                    <h3 class="mb-2 text-xl font-bold text-gray-900">Nenhum disco cadastrado</h3>
                    <p class="text-gray-500">Vamos começar?</p>
                @endif
            </div>
        @else
            @if(request('search'))
                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        Encontrados <span class="font-medium">{{ $vinyls->total() }}</span> resultados para "<span class="font-medium">{{ request('search') }}</span>"
                    </p>
                </div>
            @endif
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Cover</th>
                            <th scope="col" class="px-6 py-3">Informações do Disco</th>
                            <th scope="col" class="px-6 py-3">Preço de venda</th>
                            <th scope="col" class="px-6 py-3">Preço promocional</th>
                            <th scope="col" class="px-6 py-3">Ano</th>
                            <th scope="col" class="px-6 py-3">Estoque</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vinyls as $vinyl)
                            <x-admin.vinyl-row :vinyl="$vinyl" />
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($vinyls->hasPages())
                <div class="py-4">
                    {{ $vinyls->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
</x-admin-layout>


{{-- JavaScript toast functions removed since we're using Laravel flash messages now --}}
