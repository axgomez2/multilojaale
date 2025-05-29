<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        @php
            $store = \App\Models\StoreInformation::getInstance();
            $pageTitle = $title ?? $store->name ?? config('app.name', 'Laravel');
        @endphp

        <title>{{ $pageTitle }}</title>
        
        <!-- Favicon -->
        @if($store->favicon_url)
        <link rel="icon" href="{{ $store->favicon_url }}" type="image/x-icon">
        @endif
        
        <!-- Meta Tags -->
        <meta name="description" content="{{ $store->description }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Livewire Styles -->
        @livewireStyles
        
        <!-- Script desativado para evitar conflitos com Livewire -->
        {{-- <script src="{{ asset('js/wishlist-fix.js') }}"></script> --}}
    </head>
    <body class="font-sans antialiased {{ auth()->check() ? 'user-authenticated' : '' }}">
        <!-- Flash Messages/Toasts -->
        <x-site.flash-messages />
        
        <div class="min-h-screen">
            <!-- Navbar -->
            <x-site.navbar2 :store="$store" />
           
            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <x-site.footer :store="$store" />
        
        <!-- Livewire Scripts -->
        @livewireScripts
        
        <script>
            // Listener para eventos de notificação do Livewire
            document.addEventListener('DOMContentLoaded', function() {
                // Escutar eventos de notificação do Livewire
                window.addEventListener('notify', event => {
                    // Criar dinamicamente um toast usando o componente Flowbite
                    const type = event.detail.type || 'success';
                    const message = event.detail.message;
                    
                    // Criar o elemento toast
                    const toastId = `toast-${Date.now()}`;
                    const position = 'top-right';
                    
                    // Definir classes e ícone com base no tipo
                    let typeClass = '';
                    let icon = '';
                    
                    switch(type) {
                        case 'success':
                            typeClass = 'text-green-500 bg-green-50 dark:bg-gray-800 dark:text-green-400';
                            icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/></svg>';
                            break;
                        case 'error':
                            typeClass = 'text-red-500 bg-red-50 dark:bg-gray-800 dark:text-red-400';
                            icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.5 11.5a1 1 0 0 1-2 0v-4a1 1 0 0 1 2 0v4Zm-3.5-2a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/></svg>';
                            break;
                        case 'warning':
                            typeClass = 'text-yellow-500 bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300';
                            icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/></svg>';
                            break;
                        case 'info':
                            typeClass = 'text-blue-500 bg-blue-50 dark:bg-gray-800 dark:text-blue-400';
                            icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/></svg>';
                            break;
                    }
                    
                    // Criar o HTML do toast
                    const toastHTML = `
                        <div id="${toastId}" class="fixed ${position} z-50 flex items-center w-full max-w-xs p-4 mb-4 rounded-lg shadow ${typeClass}" role="alert">
                            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8">
                                ${icon}
                            </div>
                            <div class="ml-3 text-sm font-normal">${message}</div>
                            <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex h-8 w-8 text-gray-500 hover:text-white bg-white hover:bg-gray-200 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700" onclick="this.parentElement.remove()" aria-label="Close">
                                <span class="sr-only">Fechar</span>
                                <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    `;
                    
                    // Adicionar o toast ao DOM
                    document.body.insertAdjacentHTML('beforeend', toastHTML);
                    
                    // Remover o toast após 5 segundos
                    setTimeout(() => {
                        const toastElement = document.getElementById(toastId);
                        if (toastElement) {
                            toastElement.remove();
                        }
                    }, 5000);
                });
            });
        </script>
        
        <!-- Script para o componente vinyl-card -->
        <script src="{{ asset('js/vinyl-card.js') }}"></script>
        
        <!-- Script para wishlist e wantlist -->
        <script src="{{ asset('js/wishlist-wantlist.js') }}"></script>
    </body>
</html>
