<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white dark:bg-zinc-800">
        <div class="min-h-screen">
            <!-- Navbar -->
            <nav class="bg-zinc-900 border-b border-zinc-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <a href="{{ route('dashboard') }}" wire:navigate>
                                    <x-app-logo />
                                </a>
                            </div>
                        </div>
                        
                        <div class="flex items-center ml-auto">
                            <!-- Login/Register Links -->
                            @if (Route::has('login'))
                                <div class="space-x-4 sm:-my-px sm:ms-10 sm:flex">
                                    @auth
                                        <a href="{{ route('dashboard') }}" class="text-sm text-zinc-300 hover:text-zinc-100" wire:navigate>Dashboard</a>
                                        
                                        <!-- Logout Form -->
                                        <form method="POST" action="{{ route('logout') }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-sm text-zinc-300 hover:text-zinc-100">
                                                {{ __('Logout') }}
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" class="text-sm text-zinc-300 hover:text-zinc-100" wire:navigate>Login</a>
                                    @endauth
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
