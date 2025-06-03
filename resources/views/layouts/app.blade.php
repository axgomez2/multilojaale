<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    @php
        $store = \App\Models\StoreInformation::getInstance();
        
        // Verificar se estamos em uma página de vinil
        // Usamos $vinyl passado como parâmetro do componente
        $isVinyl = isset($vinyl) && $vinyl instanceof \App\Models\VinylMaster;
        
        // Título da página - usamos o $title passado pelo componente se existir
        $pageTitle = $title ?? '';
        if (empty($pageTitle)) {
            if ($isVinyl) {
                $pageTitle = $vinyl->title;
                if ($vinyl->artists && $vinyl->artists->count() > 0) {
                    $pageTitle .= ' - ' . $vinyl->artists->first()->name;
                }
            } else {
                $pageTitle = $store->name ?? config('app.name', 'RDV DISCOS');
            }
        }
        
        // Meta description - usamos $description passado pelo componente se existir
        $metaDescription = $description ?? '';
        if (empty($metaDescription)) {
            if ($isVinyl) {
                $metaDescription = 'Disco ' . $vinyl->title;
                if ($vinyl->artists && $vinyl->artists->count() > 0) {
                    $metaDescription .= ' de ' . $vinyl->artists->first()->name;
                }
                if ($vinyl->description) {
                    $metaDescription .= '. ' . substr(strip_tags($vinyl->description), 0, 150);
                    if (strlen($vinyl->description) > 150) {
                        $metaDescription .= '...';
                    }
                }
            } else {
                $metaDescription = $store->description ?? 'Loja especializada em discos de vinil';
            }
        }
        
        // Meta keywords - usamos $keywords passado pelo componente se existir
        $metaKeywords = $keywords ?? '';
        if (empty($metaKeywords)) {
            if ($isVinyl) {
                $metaKeywords = 'vinil, ' . $vinyl->title;
                if ($vinyl->artists && $vinyl->artists->count() > 0) {
                    $metaKeywords .= ', ' . $vinyl->artists->first()->name;
                }
                if ($vinyl->categories && $vinyl->categories->count() > 0) {
                    $metaKeywords .= ', ' . $vinyl->categories->pluck('nome')->implode(', ');
                }
            } else {
                $metaKeywords = $store->keywords ?? 'vinil, discos, música, loja';
            }
        }
        
        // Imagem para compartilhamento
        $pageImage = $image ?? '';
        if (empty($pageImage)) {
            if ($isVinyl && !empty($vinyl->cover_image)) {
                $pageImage = asset('storage/' . $vinyl->cover_image);
            } else {
                $pageImage = $store->logo_url ?? asset('images/default-og.png');
            }
        }
        
        // Já definimos os keywords acima, então podemos usar $metaKeywords diretamente
    @endphp

    <title>{{ $pageTitle }}</title>

    <!-- SEO Básico -->
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="index, follow">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <meta name="author" content="{{ $store->name }}">
    <meta name="revisit-after" content="7 days">
    <meta name="language" content="{{ str_replace('_', '-', app()->getLocale()) }}">

    <!-- Open Graph (Facebook, LinkedIn etc.) -->
    <meta property="og:type" content="{{ $isVinyl ? 'product' : 'website' }}">
    <meta property="og:site_name" content="{{ $store->name }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="{{ app()->getLocale() }}">
    @if($isVinyl && isset($vinyl->vinylSec) && $vinyl->vinylSec->price)
    <meta property="og:price:amount" content="{{ $vinyl->vinylSec->price }}">
    <meta property="og:price:currency" content="BRL">
    <meta property="product:price:amount" content="{{ $vinyl->vinylSec->price }}">
    <meta property="product:price:currency" content="BRL">
    <meta property="product:availability" content="{{ isset($vinyl->vinylSec) && $vinyl->vinylSec->stock > 0 ? 'in stock' : 'out of stock' }}">
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $pageImage }}">
    @php
        $twitterHandle = $store->twitter_handle ?? '';
        $twitterHandle = str_replace('@', '', $twitterHandle);
        if (!empty($twitterHandle)) {
            $twitterHandle = '@' . $twitterHandle;
        }
    @endphp
    <meta name="twitter:site" content="{{ $twitterHandle }}">
    <meta name="twitter:creator" content="{{ $twitterHandle }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- JSON-LD (Dados estruturados para o Google) -->
    <script type="application/ld+json">
    @if(isset($vinyl) && $isVinyl)
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "{{ $vinyl->title }}",
        "image": "{{ $pageImage }}",
        "description": "{{ $pageDescription }}",
        @if($vinyl->artists && $vinyl->artists->count() > 0)
        "brand": {
            "@type": "Brand",
            "name": "{{ $vinyl->artists->first()->name }}"
        },
        @endif
        @if($vinyl->release_date)
        "releaseDate": "{{ $vinyl->release_date }}",
        @endif
        @if(isset($vinyl->vinylSec))
        "offers": {
            "@type": "Offer",
            "priceCurrency": "BRL",
            @if($vinyl->vinylSec->price)
            "price": "{{ $vinyl->vinylSec->price }}",
            @endif
            "availability": "{{ isset($vinyl->vinylSec->stock) && $vinyl->vinylSec->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
            "url": "{{ url()->current() }}"
        },
        @endif
        @if($vinyl->categories && $vinyl->categories->count() > 0)
        "category": "{{ $vinyl->categories->pluck('name')->implode(', ') }}",
        @endif
        "sku": "{{ $vinyl->id }}"
    }
    @else
    {
        "@context": "https://schema.org",
        "@type": "Store",
        "name": "{{ $store->name }}",
        "url": "{{ url('/') }}",
        "logo": "{{ $store->logo_url }}",
        "image": "{{ $store->logo_url }}",
        "description": "{{ $store->description }}",
        "email": "{{ $store->contact_email ?? '' }}",
        "telephone": "{{ $store->contact_phone ?? '' }}",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "{{ $store->address ?? '' }}",
            "addressLocality": "{{ $store->city ?? '' }}",
            "addressRegion": "{{ $store->state ?? '' }}",
            "postalCode": "{{ $store->postal_code ?? '' }}",
            "addressCountry": "BR"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": "{{ $store->latitude ?? '' }}",
            "longitude": "{{ $store->longitude ?? '' }}"
        },
        "openingHoursSpecification": [
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
                "opens": "{{ $store->weekday_opening_time ?? '09:00' }}",
                "closes": "{{ $store->weekday_closing_time ?? '18:00' }}"
            },
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": "Saturday",
                "opens": "{{ $store->weekend_opening_time ?? '10:00' }}",
                "closes": "{{ $store->weekend_closing_time ?? '16:00' }}"
            }
        ],
        "priceRange": "$$$",
        "sameAs": [
            "{{ $store->facebook_url }}",
            "{{ $store->instagram_url }}",
            "{{ $store->twitter_url ?? '' }}",
            "{{ $store->youtube_url ?? '' }}"
        ]
    }
    @endif
    </script>
    
    <!-- Breadcrumb JSON-LD Schema -->
    @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            @foreach($breadcrumbs as $index => $breadcrumb)
            {
                "@type": "ListItem",
                "position": {{ $index + 1 }},
                "name": "{{ $breadcrumb['name'] }}",
                "item": "{{ $breadcrumb['url'] }}"
            }@if(!$loop->last),@endif
            @endforeach
        ]
    }
    </script>
    @endif
    
    <!-- Organization Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "{{ $store->name }}",
        "url": "{{ url('/') }}",
        "logo": "{{ $store->logo_url }}",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "{{ $store->contact_phone ?? '' }}",
            "contactType": "customer service",
            "email": "{{ $store->contact_email ?? '' }}",
            "availableLanguage": ["Portuguese", "English"]
        }
    }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire -->
    @livewireStyles
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
