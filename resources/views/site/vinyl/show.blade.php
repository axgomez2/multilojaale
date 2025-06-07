<x-app-layout
    :vinyl="$vinyl"
    :title="$title ?? null"
    :description="$description ?? null"
    :keywords="$keywords ?? null"
    :image="$image ?? null"
    :breadcrumbs="$breadcrumbs ?? null"
>

<div class="p-4 ">
      <div class="lg:max-w-6xl max-w-xl mx-auto">
         <!-- Navegação / Breadcrumbs -->
         <nav class="flex mb-8 text-sm">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="inline-flex items-center text-slate-600 hover:text-slate-800">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Início
                        </a>
                    </li>
                    @if($vinyl->categories->count() > 0)
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <a href="{{ route('site.category', $vinyl->categories->first()->slug) }}" class="ml-1 text-slate-600 hover:text-slate-800 md:ml-2">{{ $vinyl->categories->first()->nome }}</a>
                        </div>
                    </li>
                    @endif
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-gray-400 md:ml-2">{{ $vinyl->title }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
<!-- fim do Navegação / Breadcrumbs -->


        <div class="grid items-start grid-cols-1 lg:grid-cols-2 gap-8 max-lg:gap-12 max-sm:gap-8">
          <div class="w-full lg:sticky top-0">
            <div class="flex flex-col gap-4">
              <div class="bg-white shadow-sm p-2">
                @php 
                  $coverImage = asset('storage/' . $vinyl->cover_image) ?? asset('images/no-image.webp');
                  $mediaImages = $vinyl->media()->get();
                @endphp
                <img id="main-product-image" src="{{ $coverImage }}" alt="{{ $vinyl->title }}" class="w-full aspect-[8/8] object-cover object-top" />
              </div>
              <div class="bg-white shadow-sm p-2 w-full max-w-full overflow-auto">
                <div class="flex justify-between flex-row gap-4 shrink-0">
                  <!-- Cover image as first thumbnail -->
                  <img src="{{ $coverImage }}" alt="{{ $vinyl->title }} Cover" 
                    class="thumbnail-image w-16 h-16 aspect-square object-cover object-top cursor-pointer shadow-md border-b-2 border-black" 
                    data-image="{{ $coverImage }}" onclick="changeMainImage(this)" />
                  
                  <!-- Additional media images -->
                  @if($mediaImages->count() > 0)
                    @foreach($mediaImages as $media)
                      <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->alt_text ?? $vinyl->title }}" 
                        class="thumbnail-image w-16 h-16 aspect-square object-cover object-top cursor-pointer shadow-md border-b-2 border-transparent" 
                        data-image="{{ asset('storage/' . $media->file_path) }}" onclick="changeMainImage(this)" />
                    @endforeach
                  @else
                    <div class="w-full text-center flex items-center justify-center">
                      <p class="text-gray-500 text-sm italic">Mais fotos em breve</p>
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <!-- JavaScript para trocar a imagem principal -->
            <script>
              function changeMainImage(element) {
                // Update main image source
                document.getElementById('main-product-image').src = element.getAttribute('data-image');
                
                // Update active thumbnail border
                const thumbnails = document.getElementsByClassName('thumbnail-image');
                for(let i = 0; i < thumbnails.length; i++) {
                  thumbnails[i].classList.remove('border-black');
                  thumbnails[i].classList.add('border-transparent');
                }
                
                // Add active border to clicked thumbnail
                element.classList.remove('border-transparent');
                element.classList.add('border-black');
              }
            </script>
          </div>

          <div class="w-full">
            <div>
              <h3 class="text-lg sm:text-xl font-semibold text-slate-900">
                @foreach($vinyl->artists as $index => $artist)
                    <a href="{{ route('site.artist', ['slug' => $artist->slug]) }}" class="hover:text-purple-700 transition-colors duration-300">
                        {{ $artist->name }}
                    </a>{{ $index < count($vinyl->artists) - 1 ? ', ' : '' }}
                @endforeach
              </h3>
              <p class="text-base text-slate-700">{{ $vinyl->title }}</p>
              <div class="flex flex-col gap-2 mt-2">
                <!-- Categorias -->
                @if($vinyl->categories->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($vinyl->categories as $category)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-800 text-white">
                                {{ $category->nome }}
                            </span>
                        @endforeach
                    </div>
                @endif
                
                <!-- Estilos -->
                @if($vinyl->styles->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($vinyl->styles as $style)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-800 text-white">
                                {{ $style->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
              </div>
              <div class="mt-4 flex items-center bg-white shadow-sm rounded-md overflow-hidden">
                <!-- Logo da gravadora (2/12) -->
                <div class="w-2/12">
                    @if($vinyl->recordLabel && $vinyl->recordLabel->getLogoUrlAttribute())
                        <a href="{{ route('site.label', ['slug' => $vinyl->recordLabel->slug]) }}" class="block">
                            <img src="{{ $vinyl->recordLabel->getLogoUrlAttribute() }}" alt="{{ $vinyl->recordLabel->name }}" class="w-full h-auto max-h-12 object-contain p-1 hover:opacity-80 transition-opacity">
                        </a>
                    @else
                        <div class="bg-gray-100 h-full flex items-center justify-center p-2">
                            <span class="text-gray-400 text-xs text-center">Sem logo</span>
                        </div>
                    @endif
                </div>
                
                <!-- Informações da gravadora (10/12) -->
                <div class="w-10/12 p-3">
                    <h4 class="text-sm font-semibold text-gray-800">
                        @if($vinyl->recordLabel)
                            <a href="{{ route('site.label', ['slug' => $vinyl->recordLabel->slug]) }}" class="hover:text-purple-700 transition-colors duration-300">{{ $vinyl->recordLabel->name }}</a>
                        @else
                            Gravadora desconhecida
                        @endif
                    </h4>
                    <div class="flex flex-wrap gap-1 mt-1 text-xs text-gray-600">
                        @if($vinyl->release_year)
                            <span>{{ $vinyl->release_year }}</span>
                            <span class="text-gray-400">&bull;</span>
                        @endif
                        @if($vinyl->country)
                            <span>{{ $vinyl->country }}</span>
                        @endif
                    </div>
                </div>
              </div>

              <div class="mt-6">
                <h4 class="text-lg font-semibold text-slate-900 mb-2">Descrição</h4>
                <div class="prose prose-sm text-slate-700">
                  {!! $vinyl->description ?? '<p class="text-gray-500 italic">Sem descrição disponível para este disco.</p>' !!}
                </div>
                
                <!-- Badges para status da capa e mídia -->
                <div class="flex flex-wrap gap-4 mt-4">
                    <!-- Status da Capa -->
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-md bg-blue-50 text-blue-700">
                            Status da Capa: {{ $vinyl->vinylSec->coverStatus->title ?? 'Desconhecido' }}
                            <button data-popover-target="popover-cover-status" data-popover-placement="bottom" type="button" class="ml-1">
                                <svg class="w-4 h-4 text-blue-500 hover:text-blue-700" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Mostrar informações</span>
                            </button>
                        </span>
                        <!-- Popover para status da capa -->
                        <div data-popover id="popover-cover-status" role="tooltip" class="absolute z-10 invisible inline-block text-sm transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 w-72">
                            <div class="p-3 space-y-2">
                                <h3 class="font-semibold text-gray-900">Status da Capa: {{ $vinyl->vinylSec->coverStatus->title ?? 'Desconhecido' }}</h3>
                                <p>{{ $vinyl->vinylSec->coverStatus->description ?? 'Sem descrição disponível.' }}</p>
                            </div>
                            <div data-popper-arrow></div>
                        </div>
                    </div>
                    
                    <!-- Status da Mídia -->
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-md bg-purple-50 text-purple-700">
                            Status da Mídia: {{ $vinyl->vinylSec->midiaStatus->title ?? 'Desconhecido' }}
                            <button data-popover-target="popover-midia-status" data-popover-placement="bottom" type="button" class="ml-1">
                                <svg class="w-4 h-4 text-purple-500 hover:text-purple-700" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Mostrar informações</span>
                            </button>
                        </span>
                        <!-- Popover para status da mídia -->
                        <div data-popover id="popover-midia-status" role="tooltip" class="absolute z-10 invisible inline-block text-sm transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 w-72">
                            <div class="p-3 space-y-2">
                                <h3 class="font-semibold text-gray-900">Status da Mídia: {{ $vinyl->vinylSec->midiaStatus->title ?? 'Desconhecido' }}</h3>
                                <p>{{ $vinyl->vinylSec->midiaStatus->description ?? 'Sem descrição disponível.' }}</p>
                            </div>
                            <div data-popper-arrow></div>
                        </div>
                    </div>
                </div>
              </div>
              <div class="flex items-center flex-wrap gap-2 mt-6">
                @if($vinyl->vinylSec->in_stock && $vinyl->vinylSec->stock > 0)
                    @if($vinyl->vinylSec->is_promotional && $vinyl->vinylSec->promotional_price > 0)
                        <p class="text-slate-500 text-base"><strike>R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}</strike></p>
                        <h4 class="text-purple-800 text-2xl sm:text-3xl font-semibold">R$ {{ number_format($vinyl->vinylSec->promotional_price, 2, ',', '.') }}</h4>
                        <div class="flex py-1 px-2 bg-purple-600 font-semibold !ml-4">
                            <span class="text-white text-sm">{{ round((1 - $vinyl->vinylSec->promotional_price / $vinyl->vinylSec->price) * 100) }}% OFF</span>
                        </div>
                    @else
                        <h4 class="text-purple-800 text-2xl sm:text-3xl font-semibold">R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}</h4>
                    @endif
                @else
                    <p class="text-red-600 text-lg font-medium">Produto indisponível</p>
                @endif
              </div>
              
              <!-- Botões de compartilhamento social -->
              <div class="mt-6">
                <p class="text-gray-700 text-sm mb-2">Compartilhar:</p>
                <div class="flex flex-wrap gap-2">
                  @php
                    // Preparar os dados para compartilhamento
                    $currentUrl = url()->current();
                    $title = $vinyl->artists->pluck('name')->implode(', ') . ' - ' . $vinyl->title;
                    $price = $vinyl->vinylSec->is_promotional && $vinyl->vinylSec->promotional_price > 0
                      ? number_format($vinyl->vinylSec->promotional_price, 2, ',', '.')
                      : number_format($vinyl->vinylSec->price, 2, ',', '.');
                    $imageUrl = $vinyl->media->first() ? asset('storage/' . $vinyl->media->first()->file_path) : '';
                    
                    // Mensagem formatada com cada informação em uma linha
                    $formattedTitle = "Disco de vinil";
                    $formattedArtist = "Artista: {$vinyl->artists->pluck('name')->implode(', ')}";
                    $formattedAlbum = "Album: {$vinyl->title}";
                    $formattedPrice = "Preço: R$ {$price}";
                    if($vinyl->vinylSec->is_promotional && $vinyl->vinylSec->promotional_price > 0) {
                        $discountPercent = round((1 - $vinyl->vinylSec->promotional_price / $vinyl->vinylSec->price) * 100);
                        $formattedPrice .= " (economia de {$discountPercent}%)";
                    }
                    $formattedStore = "Confira na RDV DISCOS!";
                    
                    // Montando a descrição completa com quebras de linha
                    $description = "$formattedTitle\n$formattedArtist\n$formattedAlbum\n$formattedPrice\n$formattedStore";
                    
                    // Para WhatsApp, o \n funciona como quebra de linha
                    $whatsappDescription = "$formattedTitle\n$formattedArtist\n$formattedAlbum\n$formattedPrice\n$formattedStore\n\n$currentUrl";
                    $whatsappUrl = 'https://api.whatsapp.com/send?text=' . urlencode($whatsappDescription);
                    
                    // Para Facebook
                    $facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($currentUrl) . '&quote=' . urlencode($description);
                    
                    // Para Twitter (mais curto devido ao limite de caracteres)
                    $twitterDescription = "$formattedTitle: $formattedArtist - $formattedPrice";
                    $twitterUrl = 'https://twitter.com/intent/tweet?text=' . urlencode($twitterDescription) . '&url=' . urlencode($currentUrl);
                  @endphp
                  
                  <!-- WhatsApp -->
                  <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="p-2 bg-green-500 hover:bg-green-600 text-white rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                      <path d="M12 0C5.373 0 0 5.373 0 12c0 6.628 5.373 12 12 12 6.628 0 12-5.373 12-12 0-6.628-5.373-12-12-12zm.5 23c-2.003 0-3.887-.5-5.548-1.383l-3.851 1.268 1.272-3.851A10.95 10.95 0 0 1 1.5 12C1.5 6.201 6.201 1.5 12 1.5S22.5 6.201 22.5 12 17.799 22.5 12 22.5z"/>
                    </svg>
                  </a>
                  
                  <!-- Facebook -->
                  <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                  </a>
                  
                  <!-- Twitter -->
                  <a href="{{ $twitterUrl }}" target="_blank" rel="noopener noreferrer" class="p-2 bg-blue-400 hover:bg-blue-500 text-white rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 14-7.496 14-13.986 0-.21 0-.42-.015-.63a9.936 9.936 0 002.46-2.548l-.047-.02z"/>
                    </svg>
                  </a>
                  
                  <!-- Instagram (link para copiar) -->
                  <button type="button" onclick="copyToClipboard()" class="p-2 bg-gradient-to-r from-purple-500 via-pink-500 to-yellow-500 text-white rounded-full transition-colors hover:opacity-80">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                  </button>
                  
                  <!-- Botão de copiar link -->
                  <button type="button" onclick="copyToClipboard()" class="p-2 bg-gray-600 hover:bg-gray-700 text-white rounded-full transition-colors" title="Copiar link">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path d="M7.24 4h-.014L4 4.013v12.988L4.014 17h2.225v3h14.008v-14H16V4H7.24zm-1.245 11V6h8.983v3h3.018v6h-12z"/>
                    </svg>
                  </button>
                  
                  <!-- Script para copiar link -->
                  <script>
                    function copyToClipboard() {
                      navigator.clipboard.writeText('{{ $currentUrl }}');
                      // Você pode adicionar uma notificação de sucesso
                      alert('Link copiado para a área de transferência!');
                    }
                  </script>
                </div>
              </div>
            </div>

            <hr class="my-6 border-gray-300" />

            <div>
              <div class="flex gap-4 items-center border border-gray-300 bg-white px-4 py-2.5 w-max" x-data="{ quantity: 1, maxStock: {{ $vinyl->vinylSec->stock }} }">
                <button type="button" class="border-0 outline-0 cursor-pointer" 
                  @click="quantity > 1 ? quantity-- : null" 
                  :disabled="quantity <= 1"
                  :class="{'opacity-50': quantity <= 1}">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" viewBox="0 0 121.805 121.804">
                    <path
                      d="M7.308 68.211h107.188a7.309 7.309 0 0 0 7.309-7.31 7.308 7.308 0 0 0-7.309-7.309H7.308a7.31 7.31 0 0 0 0 14.619z"
                      data-original="#000000" />
                  </svg>
                </button>
                <span class="text-slate-900 text-sm font-semibold px-6 block" x-text="quantity">1</span>
                <button type="button" class="border-0 outline-0 cursor-pointer" 
                  @click="quantity < maxStock ? quantity++ : $dispatch('notice', {message: 'Não é possível adicionar mais unidades que o estoque disponível.', type: 'error'})" 
                  :disabled="quantity >= maxStock"
                  :class="{'opacity-50': quantity >= maxStock}">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" viewBox="0 0 512 512">
                    <path
                      d="M256 509.892c-19.058 0-34.5-15.442-34.5-34.5V36.608c0-19.058 15.442-34.5 34.5-34.5s34.5 15.442 34.5 34.5v438.784c0 19.058-15.442 34.5-34.5 34.5z"
                      data-original="#000000" />
                    <path
                      d="M475.392 290.5H36.608c-19.058 0-34.5-15.442-34.5-34.5s15.442-34.5 34.5-34.5h438.784c19.058 0 34.5 15.442 34.5 34.5s-15.442 34.5-34.5 34.5z"
                      data-original="#000000" />
                  </svg>
                </button>
              </div>

              <div class="mt-4 flex flex-wrap gap-4" x-data="{
                showNotification: false,
                message: '',
                notificationType: 'success',
                toggleNotification(msg, type = 'success') {
                    this.message = msg;
                    this.notificationType = type;
                    this.showNotification = true;
                    setTimeout(() => {
                        this.showNotification = false;
                    }, 3000);
                },
                addToCart(vinylId) {
                    console.log('Adicionando ao carrinho:', vinylId);
                    
                    // Criar um FormData para compatibilidade com Laravel
                    const formData = new FormData();
                    formData.append('vinyl_master_id', vinylId);
                    formData.append('quantity', 1);
                    
                    fetch('/debug/cart/add', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.toggleNotification(data.message || 'Produto adicionado ao carrinho!', 'success');
                            if (data.cartCount) {
                                document.querySelectorAll('.cart-count').forEach(el => {
                                    el.textContent = data.cartCount;
                                });
                            }
                        } else {
                            this.toggleNotification(data.message || 'Erro ao adicionar ao carrinho', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.toggleNotification('Erro ao processar solicitação', 'error');
                    });
                },
                toggleWishlist(id, element) {
                    if (!window.isAuthenticated) {
                        window.location.href = '/login';
                        return;
                    }
                    
                    fetch(`/wishlist/toggle/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.toggleNotification(data.message || 'Lista de desejos atualizada!', 'success');
                            
                            // Atualizar ícone
                            const icon = element.querySelector('i');
                            if (data.inWishlist) {
                                icon.classList.add('text-red-500');
                            } else {
                                icon.classList.remove('text-red-500');
                            }
                        } else {
                            this.toggleNotification(data.message || 'Erro ao atualizar lista de desejos', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.toggleNotification('Erro ao processar solicitação', 'error');
                    });
                },
                toggleWantlist(id, element) {
                    if (!window.isAuthenticated) {
                        window.location.href = '/login';
                        return;
                    }
                    
                    fetch(`/wantlist/toggle/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.toggleNotification(data.message || 'Lista de interesse atualizada!', 'success');
                            
                            // Atualizar ícone
                            const icon = element.querySelector('i');
                            if (data.inWantlist) {
                                icon.classList.add('text-purple-500');
                            } else {
                                icon.classList.remove('text-purple-500');
                            }
                        } else {
                            this.toggleNotification(data.message || 'Erro ao atualizar lista de interesse', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.toggleNotification('Erro ao processar solicitação', 'error');
                    });
                }
              }">
                @if($vinyl->vinylSec->in_stock && $vinyl->vinylSec->stock > 0)
                  <button type="button"
                    class="wishlist-button px-4 py-3 w-[45%] cursor-pointer border border-gray-300 bg-white hover:bg-slate-50 text-slate-900 text-sm font-medium"
                    @click="toggleWishlist('{{ $vinyl->id }}', $event.currentTarget)">
                    <i class="fas fa-heart {{ auth()->check() && $vinyl->inWishlist() ? 'text-red-500' : '' }}"></i>
                    Adicionar aos favoritos
                  </button>
                  <button type="button"
                    class="add-to-cart-button px-4 py-3 w-[45%] cursor-pointer border border-purple-600 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium"
                    @click="addToCart('{{ $vinyl->id }}')">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Adicionar ao carrinho
                  </button>
                @else
                  <button type="button"
                    class="px-4 py-3 w-[45%] cursor-pointer border border-gray-300 bg-white hover:bg-slate-50 text-slate-900 text-sm font-medium"
                    @click="toggleWantlist('{{ $vinyl->id }}', $event.currentTarget)">
                    <i class="fas fa-bell {{ auth()->check() && $vinyl->inWantlist() ? 'text-purple-500' : '' }}"></i>
                    Adicionar à wantlist
                  </button>
                  <button type="button"
                    class="px-4 py-3 w-[45%] cursor-pointer border border-purple-600 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium"
                    disabled>
                    <i class="fas fa-envelope-open mr-2"></i>
                    Avisar quando disponível
                  </button>
                @endif
                
                <!-- Notificação simples integrada ao componente -->
                <div
                    x-show="showNotification"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-90"
                    class="fixed top-5 right-5 z-50 max-w-sm shadow-lg rounded-lg overflow-hidden"
                    :class="{
                        'bg-green-500': notificationType === 'success',
                        'bg-red-500': notificationType === 'error',
                        'bg-yellow-500': notificationType === 'warning',
                        'bg-blue-500': notificationType === 'info'
                    }"
                >
                    <div class="px-4 py-3 text-white flex items-center">
                        <!-- Ícone baseado no tipo -->
                        <template x-if="notificationType === 'success'">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </template>
                        <template x-if="notificationType === 'error'">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </template>
                        <template x-if="notificationType === 'warning'">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </template>
                        <template x-if="notificationType === 'info'">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </template>
                        <span x-text="message"></span>
                    </div>
                </div>
              </div>
            </div>

            <hr class="my-6 border-gray-300" />

            <div>
              <h3 class="text-lg sm:text-xl font-semibold text-slate-900">Faixas</h3>
              <p class="text-slate-500 text-sm mt-2">Lista de músicas deste disco.</p>
              <div class="mt-4 space-y-2">
                @forelse($vinyl->tracks as $track)
                  <div class="flex items-center justify-between py-2 border-b border-gray-200">
                    <div class="flex-1">
                      <p class="text-sm font-medium text-gray-800">{{ $track->name }}</p>
                      <p class="text-xs text-gray-500">{{ $track->duration }}</p>
                    </div>
                    <button 
                      type="button"
                      class="ml-4 p-2 rounded-full {{ !empty($track->youtube_url) ? 'bg-gray-100 hover:bg-gray-200 text-blue-600' : 'bg-gray-100 opacity-50 cursor-not-allowed text-gray-400' }}"
                      {{ !empty($track->youtube_url) ? '' : 'disabled' }}
                      title="{{ !empty($track->youtube_url) ? 'Ouvir amostra' : 'Áudio não disponível' }}"
                      x-data
                      x-on:click="$dispatch('play-specific-track', { 
                        youtubeUrl: '{{ $track->youtube_url }}', 
                        trackName: {{ Js::from($track->name) }},
                        vinylId: '{{ $vinyl->id }}',
                        vinylTitle: {{ Js::from($vinyl->title) }},
                        vinylArtist: {{ Js::from($vinyl->artists->pluck('name')->implode(', ')) }},
                        vinylCover: '{{ asset("storage/" . $vinyl->cover_image) }}'
                      })"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8 5.14v14l11-7-11-7z" />
                      </svg>
                    </button>
                  </div>
                @empty
                  <p class="text-sm text-gray-500 py-4">Nenhuma faixa disponível para este álbum.</p>
                @endforelse
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>









    <main class="flex-1  py-8">
        <div class="container mx-auto px-4">
            
           
            
          
            
            <!-- Discos similares -->
            @if($similarVinyls->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-black mb-6">Você também pode gostar</h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                    @foreach($similarVinyls->take(6) as $similar)
                        <x-site.vinyl-card :vinyl="$similar" size="normal"
                            :inWishlist="in_array($similar->id, is_array($wishlistItems) ? $wishlistItems : ($wishlistItems ? $wishlistItems->toArray() : []))"
                            :inWantlist="in_array($similar->id, is_array($wantlistItems) ? $wantlistItems : ($wantlistItems ? $wantlistItems->toArray() : []))" />
                    @endforeach
                </div>
            </div>
            @endif
            
        </div>
    </main>
</x-app-layout>
