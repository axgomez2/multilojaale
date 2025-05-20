@props(['vinyl'])

<tr class="bg-white border-b hover:bg-gray-50
    {{ !$vinyl->vinylSec ? 'bg-red-200' :
       ($vinyl->vinylSec && $vinyl->vinylSec->quantity == 0 ? 'bg-yellow-200' : '') }}">
    <td class="p-4">
        <img class="w-16 h-16 rounded-lg object-cover"
             src="{{ $vinyl->cover_image ? asset('storage/' . $vinyl->cover_image) : asset('assets/images/placeholder.jpg') }}"
             alt="Cover">
    </td>
    <td class="px-6 py-4">
        <div class="font-semibold text-gray-900">{{ $vinyl->artists->pluck('name')->join(', ') }}</div>
        <div class="text-sm text-gray-500">{{ $vinyl->title }}</div>
        @php
            $totalTracks = $vinyl->tracks->count();
            $tracksWithYoutube = $vinyl->tracks->whereNotNull('youtube_url')->count();
            $allTracksHaveYoutube = $totalTracks > 0 && $totalTracks === $tracksWithYoutube;
        @endphp
        <span class="inline-flex items-center {{ $allTracksHaveYoutube ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-medium px-2.5 py-0.5 rounded-full mt-2">
            Faixas: {{ $totalTracks }} ({{ $tracksWithYoutube }} com YouTube)
        </span>
    </td>
    <td class="px-6 py-4 font-medium text-gray-900">R$ {{ $vinyl->vinylSec->price ?? '--' }}</td>
    <td class="px-6 py-4 font-medium text-gray-900">R$ {{ $vinyl->vinylSec->promotional_price ?? '--' }}</td>
    <td class="px-6 py-4">{{ $vinyl->release_year }}</td>
    <td class="px-6 py-4">{{ $vinyl->vinylSec->quantity ?? '0' }}</td>
    <td class="px-6 py-4">
        <div class="space-y-3">
            <!-- Toggle Promoção -->
<div class="flex items-center">
    <div x-data="toggleSwitch({{ $vinyl->id }}, 'is_promotional', {{ $vinyl->vinylSec && $vinyl->vinylSec->is_promotional ? 'true' : 'false' }})"
         class="relative inline-flex items-center">
        <button type="button"
                @click="toggle()"
                :disabled="loading"
                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                :class="{ 'bg-blue-600': checked, 'bg-gray-200': !checked, 'opacity-50': loading }">
            <span class="sr-only">Toggle Promotional</span>
            <span class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                  :class="{ 'translate-x-5': checked, 'translate-x-0': !checked }"></span>
        </button>
    </div>
    <span class="ml-3 text-sm font-medium text-gray-900">Em promoção</span>
</div>

          <!-- Toggle Estoque -->
<div class="flex items-center">
    <div x-data="toggleSwitch({{ $vinyl->id }}, 'in_stock', {{ $vinyl->vinylSec && $vinyl->vinylSec->in_stock ? 'true' : 'false' }})"
         class="relative inline-flex items-center">
        <button type="button"
                @click="toggle()"
                :disabled="loading"
                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                :class="{ 'bg-blue-600': checked, 'bg-gray-200': !checked, 'opacity-50': loading }">
            <span class="sr-only">Toggle Stock</span>
            <span class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                  :class="{ 'translate-x-5': checked, 'translate-x-0': !checked }"></span>
        </button>
    </div>
    <span class="ml-3 text-sm font-medium text-gray-900">Em estoque</span>
</div>
        </div>
    </td>
    <td class="px-6 py-4">
        <div class="flex flex-col space-y-2">
            <div class="flex items-center space-x-2">
                <!-- Botões principais -->
                <a href="{{ route('admin.vinyls.edit', $vinyl->id) }}"
                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>

                <a href="{{ route('admin.vinyls.edit-tracks', $vinyl->id) }}"
                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                    </svg>
                    Faixas
                </a>
            </div>

            <div class="flex items-center space-x-2">
                <!-- Botões secundários -->
                @if($vinyl->vinylSec)
                    <a href="{{ route('admin.vinyl.images', $vinyl->id) }}"
                       class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-green-700 bg-green-100 rounded-md hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Imagens
                    </a>
                    <a href="{{ route('admin.vinyls.show', $vinyl->id) }}"
                       class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Ver
                    </a>
                @else
                    <a href="{{ route('admin.vinyls.complete', $vinyl->id) }}"
                       class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-yellow-700 bg-yellow-100 rounded-md hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Completar
                    </a>
                @endif

                <form action="{{ route('admin.vinyls.destroy', $vinyl->id) }}"
                      method="POST"
                      class="inline-block"
                      x-data
                      @submit.prevent="if (confirm('Tem certeza que deseja excluir esse disco?')) $el.submit()">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Excluir
                    </button>
                </form>
            </div>
        </div>
    </td>
</tr>

