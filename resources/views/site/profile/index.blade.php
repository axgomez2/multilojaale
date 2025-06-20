<x-app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Meu Perfil</h1>
                <p class="text-gray-600">Gerencie suas informações pessoais e preferências</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Seção de Dados Pessoais -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-2">Dados Pessoais</h2>
                            <p class="text-gray-600 text-sm mb-4">Atualize suas informações pessoais</p>
                        </div>
                        <div class="rounded-full bg-gray-100 p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 mb-1"><span class="font-medium text-gray-700">Nome:</span> {{ $user->name }}</p>
                        <p class="text-sm text-gray-500 mb-1">
                            <span class="font-medium text-gray-700">Email:</span> {{ $user->email }}
                            @if($user->hasVerifiedEmail())
                                <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Verificado
                                </span>
                            @else
                                <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 cursor-pointer" onclick="document.getElementById('resend-verification-form').submit();">
                                    <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Não verificado - Clique para reenviar
                                </span>
                                <form id="resend-verification-form" method="POST" action="{{ route('verification.send') }}" class="hidden">
                                    @csrf
                                </form>
                            @endif
                        </p>
                        <p class="text-sm text-gray-500 mb-1">
                            <span class="font-medium text-gray-700">Telefone:</span> 
                            {{ $user->phone ?: 'Não informado' }}
                        </p>
                        <p class="text-sm text-gray-500 mb-1">
                            <span class="font-medium text-gray-700">CPF:</span> 
                            {{ $user->cpf ?: 'Não informado' }}
                        </p>
                        <p class="text-sm text-gray-500 mb-1">
                            <span class="font-medium text-gray-700">Data de Nascimento:</span> 
                            {{ $user->birth_date ? $user->birth_date->format('d/m/Y') : 'Não informada' }}
                        </p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('site.profile.personal-info.edit') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                            Editar Dados
                        </a>
                    </div>
                </div>

                <!-- Seção de Segurança -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-2">Segurança</h2>
                            <p class="text-gray-600 text-sm mb-4">Gerencie suas credenciais de acesso</p>
                        </div>
                        <div class="rounded-full bg-gray-100 p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 mb-1">
                            <span class="font-medium text-gray-700">Status de Verificação:</span> 
                            @if($user->hasVerifiedEmail())
                                <span class="text-green-600">Email verificado</span>
                            @else
                                <span class="text-red-600">Email não verificado</span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-500 mb-1">
                            <span class="font-medium text-gray-700">Senha:</span> 
                            <span class="text-gray-500">••••••••</span>
                        </p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('site.profile.password.edit') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                            Alterar Senha
                        </a>
                    </div>
                </div>

                <!-- Seção de Endereços -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-2">Endereços</h2>
                            <p class="text-gray-600 text-sm mb-4">Gerencie seus endereços de entrega</p>
                        </div>
                        <div class="rounded-full bg-gray-100 p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 mb-1">
                            <span class="font-medium text-gray-700">Endereços Cadastrados:</span> 
                            {{ $addressCount }}
                        </p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('site.profile.addresses.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                            Gerenciar Endereços
                        </a>
                    </div>
                </div>

                <!-- Outras seções podem ser adicionadas conforme necessário -->
            </div>
        </div>
    </div>
</x-app-layout>
