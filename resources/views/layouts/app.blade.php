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
    </head>
    <body class="font-sans antialiased bg-white dark:bg-zinc-800">
        <div class="min-h-screen">
            <!-- Navbar -->
            <x-site.navbar2 :store="$store" />
           

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <x-site.footer :store="$store" />
    </body>
</html>
