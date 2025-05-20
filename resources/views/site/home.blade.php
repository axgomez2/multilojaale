<x-app-layout>
<div class="bg-gray-50 min-h-screen">
    <x-site.navbar :store="$store" />
    
    <main class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                    <span class="block">{{ $store->name }}</span>
                </h1>
                @if($store->description)
                    <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                        {{ $store->description }}
                    </p>
                @endif
            </div>
            
            <!-- Informações da Loja -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Informações da Loja
                    </h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        @if($store->address)
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Endereço</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $store->address }}
                                @if($store->neighborhood), {{ $store->neighborhood }}@endif
                                @if($store->state) - {{ $store->state }}@endif
                                @if($store->zipcode), CEP: {{ $store->zipcode }}@endif
                            </dd>
                        </div>
                        @endif
                        
                        @if($store->phone)
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $store->phone }}</dd>
                        </div>
                        @endif
                        
                        @if($store->email)
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">E-mail</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $store->email }}</dd>
                        </div>
                        @endif
                        
                        @if($store->document_type && $store->document)
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                {{ $store->document_type == 'cpf' ? 'CPF' : 'CNPJ' }}
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $store->document }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="bg-white border-t border-gray-200 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500">
                &copy; {{ date('Y') }} {{ $store->name }}. Todos os direitos reservados.
            </p>
        </div>
    </footer>
</div>
</x-app-layout>
