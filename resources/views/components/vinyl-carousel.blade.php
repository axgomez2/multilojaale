@props(['vinyls'])

<div class="bg-gray-100 dark:bg-gray-800 overflow-hidden"
     x-data="vinylCarousel"
     x-init="startAutoplay">
    <div class="relative">
        <!-- Hot Sales Badge -->
        <div class="absolute top-4 left-4 z-20">
            <span class="bg-blue-700 text-white px-3 py-1 rounded-md text-sm font-medium dark:bg-blue-600">
                mais desejados!
            </span>
        </div>

        <!-- Carousel Container -->
        <div class="relative overflow-hidden">
            <div class="flex transition-transform duration-500 ease-out"
                 :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
                @foreach($vinyls as $vinyl)
                    <div class="w-full flex-shrink-0">
                        <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug, 'titleSlug' => $vinyl->slug]) }}"
                           class="block relative h-[250px] group">
                            <!-- Background Image with Filter -->
                            <div class="absolute inset-0 bg-cover bg-center"
                                 style="background-image: url('{{ asset('storage/' . $vinyl->cover_image) ?? '/placeholder.svg?height=300&width=600' }}');">
                                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm group-hover:bg-black/40 transition-colors duration-300"></div>
                            </div>

                            <!-- Content -->
                            <div class="relative z-10 h-full p-6 flex items-center">
                                <img src="{{ asset('storage/' . $vinyl->cover_image) ?? '/placeholder.svg?height=150&width=150' }}"
                                     alt="{{ $vinyl->title }}"
                                     class="w-32 h-32 object-cover rounded-lg shadow-lg transition-transform duration-300 group-hover:scale-105"/>
                                <div class="ml-6 flex flex-col justify-center text-white">
                                    <h3 class="text-2xl font-bold group-hover:text-blue-500 transition-colors duration-300">
                                        {{ $vinyl->artists->pluck('name')->join(', ') }}
                                    </h3>
                                    <p class="text-xl mt-1">{{ $vinyl->title }}</p>
                                    <p class="text-sm text-gray-300 mt-2">
                                        {{ $vinyl->format ?? '12 inch' }} /
                                        {{ $vinyl->record_label->name ?? 'Unknown Label' }}
                                    </p>
                                    <div class="flex gap-2 mt-3">
                                        @foreach($vinyl->categories as $category)
                                            <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-blue-700 text-white dark:bg-blue-600">
                                                {{ $category->nome }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- View Details Button -->
                            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                    Ver Detalhes
                                </button>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Navigation Dots -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex justify-center gap-1 z-20">
            @foreach($vinyls as $vinyl)
                <button type="button"
                    @click="currentSlide = {{ $loop->index }}"
                    :class="{'bg-blue-700 dark:bg-blue-600': currentSlide === {{ $loop->index }}}"
                    class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600 transition-colors">
                </button>
            @endforeach
        </div>

        <!-- Navigation Buttons -->
        <button type="button" @click.stop="prev"
                class="absolute left-2 top-1/2 -translate-y-1/2 inline-flex items-center justify-center w-8 h-8 rounded-full bg-white/30 hover:bg-white/50 focus:outline-none focus:ring-2 focus:ring-white dark:bg-gray-800/30 dark:hover:bg-gray-800/50 dark:focus:ring-gray-800 z-20">
            <span class="sr-only">Previous</span>
            <svg class="w-4 h-4 text-white dark:text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
            </svg>
        </button>
        <button type="button" @click.stop="next"
                class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center justify-center w-8 h-8 rounded-full bg-white/30 hover:bg-white/50 focus:outline-none focus:ring-2 focus:ring-white dark:bg-gray-800/30 dark:hover:bg-gray-800/50 dark:focus:ring-gray-800 z-20">
            <span class="sr-only">Next</span>
            <svg class="w-4 h-4 text-white dark:text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
            </svg>
        </button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('vinylCarousel', () => ({
        currentSlide: 0,
        autoplayInterval: null,
        totalSlides: {{ count($vinyls) }},

        next() {
            this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        },

        prev() {
            this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
        },

        startAutoplay() {
            this.autoplayInterval = setInterval(() => this.next(), 5000);
        },

        stopAutoplay() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
            }
        }
    }));
});
</script>
@endpush



