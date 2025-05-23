<div class="bg-zinc-800 shadow-md py-3 px-6  flex justify-between items-center">
    <div class="flex items-center">
        <h1 class="text-white text-lg font-medium">{{ $title ?? 'Dashboard' }}</h1>
    </div>
    
    <div class="flex items-center space-x-4">
        <div class="relative">
            <button class="text-zinc-300 hover:text-white focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>
        </div>
        
        <div class="flex items-center">
            <div class="h-8 w-8 bg-emerald-500 rounded-full flex items-center justify-center text-white font-medium">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <span class="ml-2 text-white">{{ auth()->user()->name }}</span>
        </div>
    </div>
</div>
