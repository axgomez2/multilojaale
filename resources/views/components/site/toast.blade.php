@props([
    'type' => 'success',
    'message' => '',
    'position' => 'top-right',
    'duration' => 5000,
    'id' => 'toast-notification'
])

@php
    $typeClasses = [
        'success' => 'text-green-500 bg-green-50 dark:bg-gray-800 dark:text-green-400',
        'error' => 'text-red-500 bg-red-50 dark:bg-gray-800 dark:text-red-400',
        'warning' => 'text-yellow-500 bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300',
        'info' => 'text-blue-500 bg-blue-50 dark:bg-gray-800 dark:text-blue-400'
    ];
    
    $icons = [
        'success' => '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/></svg>',
        'error' => '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/></svg>',
        'warning' => '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/></svg>',
        'info' => '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/></svg>'
    ];
    
    $typeClass = $typeClasses[$type] ?? $typeClasses['info'];
    $icon = $icons[$type] ?? $icons['info'];
@endphp

<div id="{{ $id }}" 
     class="fixed {{ $position }} z-50 flex items-center w-full max-w-xs p-4 mb-4 rounded-lg shadow {{ $typeClass }}" 
     role="alert" 
     x-data="siteToast('{{ $id }}', '{{ $message }}', '{{ $type }}', '{{ $position }}', {{ $duration }}, '{{ $typeClass }}', '{{ Js::from($icon) }}')"
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-90"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90">
    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8">
        <div x-html="icon"></div>
    </div>
    <div class="ml-3 text-sm font-normal" x-text="message"></div>
    <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex h-8 w-8 text-gray-500 hover:text-white bg-white hover:bg-gray-200 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700" 
            @click="show = false" aria-label="Close">
        <span class="sr-only">Fechar</span>
        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
        </svg>
    </button>
</div>
