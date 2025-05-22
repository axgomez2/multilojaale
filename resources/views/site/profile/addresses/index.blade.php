<x-app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('site.profile.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Voltar para o Perfil
                </a>
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Meus Endereços</h1>
                        <p class="text-gray-600">Gerencie seus endereços de entrega</p>
                    </div>
                    <a href="{{ route('site.profile.addresses.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Adicionar Endereço
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if($addresses->isEmpty())
                <div class="bg-white shadow-md rounded-lg p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Nenhum endereço cadastrado</h2>
                    <p class="text-gray-600 mb-6">Você ainda não possui endereços cadastrados. Adicione um endereço para facilitar suas compras.</p>
                    <a href="{{ route('site.profile.addresses.create') }}" class="inline-flex items-center px-5 py-2 text-base font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                        Adicionar Meu Primeiro Endereço
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($addresses as $address)
                        <div class="bg-white shadow-md rounded-lg p-6 relative {{ $address->is_default ? 'border-2 border-indigo-500' : '' }}">
                            @if($address->is_default)
                                <div class="absolute top-3 right-3 bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-1 rounded-full">
                                    Endereço Padrão
                                </div>
                            @endif
                            
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $address->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $address->type == 'residential' ? 'Residencial' : ($address->type == 'commercial' ? 'Comercial' : 'Outro') }}</p>
                            </div>
                            
                            <div class="space-y-1 mb-4">
                                <p class="text-sm text-gray-800">
                                    <span class="font-medium">Destinatário:</span> {{ $address->recipient }}
                                </p>
                                <p class="text-sm text-gray-800">
                                    {{ $address->street }}, {{ $address->number }}
                                    @if($address->complement)
                                        - {{ $address->complement }}
                                    @endif
                                </p>
                                <p class="text-sm text-gray-800">
                                    {{ $address->district }}, {{ $address->city }}/{{ $address->state }}
                                </p>
                                <p class="text-sm text-gray-800">
                                    <span class="font-medium">CEP:</span> {{ $address->zipcode }}
                                </p>
                                @if($address->phone)
                                    <p class="text-sm text-gray-800">
                                        <span class="font-medium">Telefone:</span> {{ $address->phone }}
                                    </p>
                                @endif
                                @if($address->reference)
                                    <p class="text-sm text-gray-800">
                                        <span class="font-medium">Referência:</span> {{ $address->reference }}
                                    </p>
                                @endif
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-2">
                                    <a href="{{ route('site.profile.addresses.edit', $address->id) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        Editar
                                    </a>
                                    
                                    @if(!$address->is_default)
                                        <form action="{{ route('site.profile.addresses.set-default', $address->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-700 bg-indigo-100 border border-transparent rounded-md hover:bg-indigo-200">
                                                Definir como Padrão
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                
                                @if(!$address->is_default || $addresses->count() > 1)
                                    <form action="{{ route('site.profile.addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este endereço?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-700 bg-red-100 border border-transparent rounded-md hover:bg-red-200">
                                            Remover
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
