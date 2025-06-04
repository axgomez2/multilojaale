<x-app-layout>



<div class="bg-gray-50 min-h-screen">
       

    <!-- hero content -->
  <section style="background-image: url('/assets/images/hero.webp')" class="bg-no-repeat bg-cover bg-center mt-8">
    <div class="py-8 px-4 mx-auto max-w-screen-xl text-center lg:py-16 lg:px-6">
       
        <h1 class="mb-4 text-4xl font-extrabold tracking-tight leading-none text-gray-900 md:text-5xl lg:text-6xl dark:text-white">Estamos em fase beta e finalização!</h1>
        <p class="mb-8 text-lg font-normal text-black lg:text-xl sm:px-16 xl:px-48 ">Estamos finalizando nossa loja, mas você ja pode se cadastrar e criar sua lista de desejos ou ate mesmo comprar pelo whatsapp, </p>
       
        <div class="px-4 mx-auto text-center md:max-w-screen-md lg:max-w-screen-lg lg:px-36">
            <span class="font-semibold text-black uppercase">ACOMPANHE NOSSAS REDES SOCIAIS</span>


            <div class="flex flex-wrap justify-center items-center mt-8 text-black sm:justify-between">
                <!-- Instagram -->
                <a href="https://www.instagram.com/seuperfil" target="_blank" class="flex items-center space-x-2 hover:text-black">
                  <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7.5 2C4.462 2 2 4.462 2 7.5v9C2 19.538 4.462 22 7.5 22h9c3.038 0 5.5-2.462 5.5-5.5v-9C22 4.462 19.538 2 16.5 2h-9Zm4.5 5.5a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9Zm5.25-1.5a.75.75 0 1 1 0 1.5.75.75 0 0 1 0-1.5Z"/>
                  </svg>
                  <span class="text-xl font-semibold">Instagram</span>
                </a>

                <!-- Facebook -->
                <a href="https://www.facebook.com/seupagina" target="_blank" class="flex items-center space-x-2 hover:text-black">
                  <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M22 12c0-5.522-4.478-10-10-10S2 6.478 2 12c0 5.004 3.663 9.128 8.438 9.878v-6.988H7.898v-2.89h2.54V9.845c0-2.506 1.493-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.462h-1.26c-1.243 0-1.63.772-1.63 1.562v1.875h2.773l-.443 2.89h-2.33v6.988C18.337 21.128 22 17.004 22 12Z"/>
                  </svg>
                  <span class="text-xl font-semibold">Facebook</span>
                </a>

                <!-- WhatsApp -->
                <a href="https://wa.me/5500000000000" target="_blank" class="flex items-center space-x-2 hover:text-black">
                  <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.001 2.002c-5.523 0-10 4.477-10 10 0 1.768.466 3.465 1.347 4.977L2 22l5.166-1.343A9.95 9.95 0 0 0 12 22.002c5.523 0 10-4.478 10-10 0-5.523-4.477-10-9.999-10Zm.195 17.338a8.078 8.078 0 0 1-4.108-1.126l-.294-.175-3.064.796.82-2.988-.191-.306a8.08 8.08 0 1 1 14.922-4.357 8.08 8.08 0 0 1-8.085 8.156Zm4.544-6.087c-.248-.124-1.472-.729-1.7-.812-.228-.084-.394-.124-.559.124-.166.248-.64.812-.785.978-.145.166-.29.186-.538.062-.248-.125-1.045-.384-1.99-1.225-.736-.656-1.232-1.468-1.377-1.716-.144-.248-.016-.382.109-.506.112-.111.248-.29.372-.434.124-.145.165-.248.248-.414.082-.165.041-.31-.02-.434-.062-.124-.559-1.35-.767-1.85-.2-.48-.404-.414-.559-.422l-.476-.008a.914.914 0 0 0-.663.31c-.228.248-.872.852-.872 2.08 0 1.228.892 2.416 1.016 2.58.124.165 1.754 2.68 4.253 3.76.595.256 1.059.409 1.42.523.596.189 1.138.162 1.566.098.478-.07 1.472-.602 1.679-1.183.207-.58.207-1.077.145-1.183-.062-.105-.228-.165-.476-.289Z"/>
                  </svg>
                  <span class="text-xl font-semibold">WhatsApp</span>
                </a>

                <!-- Cadastro -->
                <a href="/cadastro" class="flex items-center space-x-2 hover:text-black">
                  <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4Zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4Z"/>
                  </svg>
                  <span class="text-xl font-semibold">Cadastro</span>
                </a>
            </div>
        </div> 
    </div>
</section>

<!-- Seção de produtos em destaque -->
<section class="py-12 bg-gray-50">
    <div >
        <h2 class="text-2xl font-semibold text-slate-800 mb-8 text-left subpixel-antialiased ">Discos em Destaque</h2>
       
        
        @if($featuredVinyls->isNotEmpty())
            <!-- Grade de produtos usando o componente vinyl-card -->
            <div class="grid grid-cols-1 md:grid-cols-4 xl:grid-cols-5 gap-6 mb-10">
                @foreach($featuredVinyls as $vinyl)
                    <x-site.vinyl-card :vinyl="$vinyl" size="normal"
                        :inWishlist="in_array($vinyl->id, is_array($wishlistItems) ? $wishlistItems : ($wishlistItems ? $wishlistItems->toArray() : []))"
                        :inWantlist="in_array($vinyl->id, is_array($wantlistItems) ? $wantlistItems : ($wantlistItems ? $wantlistItems->toArray() : []))" />
                @endforeach
            </div>
            
            @if($featuredVinyls->count() > 8)
                <div class="text-center mt-6">
                    <a href="{{ route('site.category', 'destaque') }}" class="inline-flex items-center px-6 py-3 text-base font-medium text-center text-black bg-yellow-400 rounded-lg hover:bg-yellow-500 transition-colors">
                        Ver mais destaques
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            @endif
        @else
            <!-- Mensagem quando não há discos em destaque -->
            <div class="bg-slate-800 rounded-lg p-8 text-center">
                <p class="text-black subpixel-antialiased text-lg">Novos discos em destaque em breve!</p>
            </div>
        @endif
    </div>
</section>

<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Carrossel de lançamentos mais recentes -->
    @if($latestVinyls->isNotEmpty())
        <div class="hidden md:block">
            <x-site.vinyl-carousel 
                title="Lançamentos Recentes" 
                :vinyls="$latestVinyls"
                :wishlistItems="$wishlistItems"
                :wantlistItems="$wantlistItems" />  
        </div>
    @endif
    
    <!-- Carrossel por categorias para desktop -->
    @foreach($categories as $categoryData)
        <div class="hidden md:block text-slate-800">
            <x-site.vinyl-carousel 
                title="{{ $categoryData['category']->nome }}" 
                :vinyls="$categoryData['vinyls']"
                :slug="$categoryData['category']->slug"
                :wishlistItems="$wishlistItems"
                :wantlistItems="$wantlistItems" 
            />
        </div>
    @endforeach
    
    <!-- Carrossel por categorias para dispositivos móveis -->
    @foreach($categories as $categoryData)
        @if($categoryData['vinyls']->isNotEmpty())
            <div class="mb-12">
                <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-semibold text-slate-800 text-left subpixel-antialiased">{{ $categoryData['category']->nome }}</h2>
                    <a href="{{ route('site.category', $categoryData['category']->slug) }}" class="text-sm text-indigo-500 hover:text-indigo-600 flex items-center">
                        Ver todos
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div x-data="{ 
                    scroll: 0, 
                    totalItems: {{ $categoryData['vinyls']->count() }}, 
                    get visibleItems() {
                        return window.innerWidth < 640 ? 1 : 
                               window.innerWidth < 768 ? 2 : 
                               window.innerWidth < 1024 ? 3 : 4;
                    } 
                }" 
                x-init="
                    window.addEventListener('resize', () => {
                        scroll = Math.min(scroll, totalItems - visibleItems);
                    });
                " 
                class="relative">
                    <!-- Botão de navegação esquerda -->
                    <button @click="scroll = Math.max(0, scroll - visibleItems)" 
                            class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-black bg-opacity-50 hover:bg-opacity-70 text-white rounded-full p-2 md:p-3 focus:outline-none"
                            :class="{'opacity-50 cursor-not-allowed': scroll <= 0, 'opacity-100 cursor-pointer': scroll > 0}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    
                    <!-- Carrossel -->
                    <div class="overflow-hidden">
                        <div class="flex pb-4 transition-transform duration-300 ease-in-out" 
                             :style="`transform: translateX(-${scroll * (100 / visibleItems)}%)`">
                            @foreach($categoryData['vinyls'] as $vinyl)
                                <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 flex-shrink-0 px-2" 
                                     :class="{
                                        'w-full': visibleItems === 1,
                                        'w-1/2': visibleItems === 2,
                                        'w-1/3': visibleItems === 3,
                                        'w-1/4': visibleItems === 4
                                     }">
                                    <x-site.vinyl-card :vinyl="$vinyl" size="normal"
                                        :inWishlist="in_array($vinyl->id, is_array($wishlistItems) ? $wishlistItems : ($wishlistItems ? $wishlistItems->toArray() : []))"
                                        :inWantlist="in_array($vinyl->id, is_array($wantlistItems) ? $wantlistItems : ($wantlistItems ? $wantlistItems->toArray() : []))" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Botão de navegação direita -->
                    <button @click="scroll = Math.min(totalItems - visibleItems, scroll + visibleItems)" 
                            class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-black bg-opacity-50 hover:bg-opacity-70 text-white rounded-full p-2 md:p-3 focus:outline-none"
                            :class="{'opacity-50 cursor-not-allowed': scroll >= totalItems - visibleItems, 'opacity-100 cursor-pointer': scroll < totalItems - visibleItems}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    
                    <!-- Pagination dots -->
                    <div class="flex justify-center mt-4" x-show="Math.ceil(totalItems / visibleItems) > 1">
                        <template x-for="(_, index) in Array.from({ length: Math.ceil(totalItems / visibleItems) })" :key="index">
                            <button class="mx-1 w-2 h-2 rounded-full focus:outline-none transition-all duration-300 ease-in-out"
                                    :class="{
                                        'bg-indigo-600': Math.floor(scroll / visibleItems) === index,
                                        'bg-gray-300': Math.floor(scroll / visibleItems) !== index
                                    }"
                                    @click="scroll = index * visibleItems"
                                    :aria-label="`Go to page ${index + 1}`">
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    </div>

    entenda a classificação de discos usados e sua importância:
    <section class="bg-black text-white p-6">
  <div class="container mx-auto">
    <h2 class="text-3xl font-bold text-yellow-400 mb-6">Condição de Mídia vs Capa (Padrão Discogs)</h2>
    <div class="overflow-x-auto rounded-lg shadow-lg">
      <table class="w-full text-sm text-left text-white bg-gray-900 border border-gray-700">
        <thead class="text-xs uppercase bg-gray-800 text-yellow-300">
          <tr>
            <th scope="col" class="px-4 py-3 border border-gray-700">Grade</th>
            <th scope="col" class="px-4 py-3 border border-gray-700">Mídia (Disco)</th>
            <th scope="col" class="px-4 py-3 border border-gray-700">Capa (Sleeve)</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-b border-gray-700 hover:bg-gray-800">
            <th scope="row" class="px-4 py-3 font-bold text-green-400">Mint (M)</th>
            <td class="px-4 py-3">Impecável, sem sinais de uso, sem ruídos ou marcas.</td>
            <td class="px-4 py-3">Sem qualquer desgaste, como nova.</td>
          </tr>
          <tr class="border-b border-gray-700 hover:bg-gray-800">
            <th scope="row" class="px-4 py-3 font-bold text-green-300">Near Mint (NM ou M-)</th>
            <td class="px-4 py-3">Quase novo, pode ter sido tocado 1–2 vezes. Sem ruídos ou desgaste.</td>
            <td class="px-4 py-3">Leve sinal de uso, mínimo desgaste nas bordas.</td>
          </tr>
          <tr class="border-b border-gray-700 hover:bg-gray-800">
            <th scope="row" class="px-4 py-3 font-bold text-blue-300">Very Good Plus (VG+)</th>
            <td class="px-4 py-3">Leves marcas superficiais, ruído muito leve. Toca bem.</td>
            <td class="px-4 py-3">Desgaste leve, possível “ring wear”, etiquetas discretas.</td>
          </tr>
          <tr class="border-b border-gray-700 hover:bg-gray-800">
            <th scope="row" class="px-4 py-3 font-bold text-blue-200">Very Good (VG)</th>
            <td class="px-4 py-3">Marcas visíveis, ruídos perceptíveis, mas ainda toca sem pular.</td>
            <td class="px-4 py-3">Desgaste moderado, possíveis dobras ou marcas.</td>
          </tr>
          <tr class="border-b border-gray-700 hover:bg-gray-800">
            <th scope="row" class="px-4 py-3 font-bold text-yellow-200">Good Plus (G+) / Good (G)</th>
            <td class="px-4 py-3">Chiados constantes, pode pular ou distorcer. Muito usado.</td>
            <td class="px-4 py-3">Capa bastante gasta, com rasgos pequenos ou escrita.</td>
          </tr>
          <tr class="hover:bg-gray-800">
            <th scope="row" class="px-4 py-3 font-bold text-red-400">Fair (F) / Poor (P)</th>
            <td class="px-4 py-3">Muito danificado, som ruim, pode não tocar corretamente.</td>
            <td class="px-4 py-3">Capa rasgada, faltando partes ou mofada.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>





    
</div>
</x-app-layout>
