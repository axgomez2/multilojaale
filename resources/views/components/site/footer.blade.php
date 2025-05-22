@props(['store' => null])

<footer class="p-4 bg-black sm:p-6">
  <div class="mx-auto max-w-screen-xl">
    <div class="md:flex md:justify-between">
      <div class="mb-6 md:mb-0">
        <a href="{{ route('home') }}" class="flex items-center">
          @if(isset($store) && $store->logo_path)
            <img src="{{ asset('storage/' . $store->logo_path) }}" class="mr-3 h-12" alt="{{ $store->name }}" />
          @endif
          <span class="self-center text-2xl font-semibold whitespace-nowrap text-white">{{ isset($store) ? $store->name : 'Loja de Vinil' }}</span>
        </a>
      </div>
      <div class="grid grid-cols-2 gap-8 sm:gap-6 sm:grid-cols-3">
        <div>
          <!-- Adicionando informações de contato da loja -->
          <h2 class="mb-6 text-sm font-semibold text-yellow-400 uppercase">Contato</h2>
          <ul class="text-white">
            @if(isset($store) && $store->address)
              <li class="mb-4">{{ $store->address }}</li>
            @endif
            @if(isset($store) && $store->phone)
              <li class="mb-4">{{ $store->phone }}</li>
            @endif
            @if(isset($store) && $store->email)
              <li class="mb-4"><a href="mailto:{{ $store->email }}" class="hover:text-yellow-400">{{ $store->email }}</a></li>
            @endif
          </ul>
        </div>
        <div>
          <h2 class="mb-6 text-sm font-semibold text-yellow-400 uppercase">Links Úteis</h2>
          <ul class="text-white">
            <li class="mb-4">
              <a href="{{ route('home') }}" class="hover:text-yellow-400">Página Inicial</a>
            </li>
            <li class="mb-4">
              <a href="#" class="hover:text-yellow-400">Catálogo</a>
            </li>
            <li>
              <a href="#" class="hover:text-yellow-400">Sobre Nós</a>
            </li>
          </ul>
        </div>
        <div>
          <h2 class="mb-6 text-sm font-semibold text-yellow-400 uppercase">Categorias</h2>
          <ul class="text-white">
            <li class="mb-4">
              <a href="#" class="hover:text-yellow-400">Rock</a>
            </li>
            <li class="mb-4">
              <a href="#" class="hover:text-yellow-400">Jazz</a>
            </li>
            <li class="mb-4">
              <a href="#" class="hover:text-yellow-400">Blues</a>
            </li>
            <li>
              <a href="#" class="hover:text-yellow-400">Clássica</a>
            </li>
          </ul>
        </div>
        <div>
          <h2 class="mb-6 text-sm font-semibold text-yellow-400 uppercase">Legal</h2>
          <ul class="text-white">
            <li class="mb-4">
              <a href="#" class="hover:text-yellow-400">Política de Privacidade</a>
            </li>
            <li>
              <a href="#" class="hover:text-yellow-400">Termos e Condições</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <hr class="my-6 border-gray-200 sm:mx-auto border-gray-700 lg:my-8" />
    <div class="sm:flex sm:items-center sm:justify-between">
      <span class="text-sm text-white sm:text-center"> {{ date('Y') }} <a href="{{ route('home') }}" class="hover:text-yellow-400">{{ isset($store) ? $store->name : 'Loja de Vinil' }}</a>. Todos os direitos reservados.</span>
      <div class="flex mt-4 space-x-6 sm:justify-center sm:mt-0">
        <!-- Instagram -->
        <a href="{{ isset($store) && $store->instagram_url ? $store->instagram_url : '#' }}" class="text-white hover:text-yellow-400" title="Instagram">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
        </a>
        <!-- Facebook -->
        <a href="{{ isset($store) && $store->facebook_url ? $store->facebook_url : '#' }}" class="text-white hover:text-yellow-400" title="Facebook">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
        </a>
        <!-- Twitter/X -->
        <a href="{{ isset($store) && $store->twitter_url ? $store->twitter_url : '#' }}" class="text-white hover:text-yellow-400" title="Twitter/X">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg>
        </a>
        <!-- WhatsApp -->
        <a href="{{ isset($store) && $store->whatsapp ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $store->whatsapp) : '#' }}" class="text-white hover:text-yellow-400" title="WhatsApp">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
        </a>
        <!-- YouTube -->
        <a href="{{ isset($store) && $store->youtube_url ? $store->youtube_url : '#' }}" class="text-white hover:text-yellow-400" title="YouTube">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
        </a>
      </div>
    </div>
  </div>
</footer>
