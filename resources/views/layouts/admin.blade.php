<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'RDV DISCOS') }} - Admin</title>

    <!-- Favicon -->
    @if($favicon = \App\Models\StoreInformation::getInstance()->favicon_url)
    <link rel="icon" href="{{ $favicon }}" type="image/x-icon">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-zinc-100 dark:bg-zinc-950">
    <div class="min-h-screen">
        <!-- Sidebar Component -->
        <x-admin.sidebar />
        
        <!-- Page Content --> 
        <div class="ml-64">
            <!-- Topbar Component -->
            <x-admin.topbar :title="$title ?? 'Dashboard'" />
            
            <!-- Main Content -->
            <main class="p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
