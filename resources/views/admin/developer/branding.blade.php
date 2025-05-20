<x-admin-layout title="Identidade Visual">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4 text-zinc-900 dark:text-zinc-100">Identidade Visual do Site</h1>
            <p class="mb-6 text-zinc-700 dark:text-zinc-300">Gerencie os elementos visuais do site, como logo e favicon.</p>
            
            @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
            @endif
            
            <form action="{{ route('admin.developer.branding.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Logo do Site -->
                    <div class="bg-zinc-50 dark:bg-zinc-900 p-6 rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <h2 class="text-xl font-semibold mb-4 text-zinc-900 dark:text-zinc-100">Logo do Site</h2>
                        
                        <div class="mb-4">
                            <div class="h-40 flex items-center justify-center bg-zinc-100 dark:bg-zinc-800 rounded-lg mb-4 p-4">
                                @if($store->logo_path)
                                    <img src="{{ $store->logo_url }}" alt="Logo do site" class="max-h-32 max-w-full">
                                @else
                                    <div class="text-zinc-400 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p>Nenhum logo carregado</p>
                                    </div>
                                @endif
                            </div>
                            
                            <label for="logo" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                Carregar novo logo
                            </label>
                            <input type="file" name="logo" id="logo" class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900">
                            
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                                Formatos recomendados: PNG, JPG ou SVG. Tamanho máximo: 2MB.
                            </p>
                            
                            @error('logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Favicon do Site -->
                    <div class="bg-zinc-50 dark:bg-zinc-900 p-6 rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <h2 class="text-xl font-semibold mb-4 text-zinc-900 dark:text-zinc-100">Favicon</h2>
                        
                        <div class="mb-4">
                            <div class="h-40 flex items-center justify-center bg-zinc-100 dark:bg-zinc-800 rounded-lg mb-4 p-4">
                                @if($store->favicon_path)
                                    <img src="{{ $store->favicon_url }}" alt="Favicon do site" class="max-h-16 border border-zinc-200 dark:border-zinc-700 p-1 rounded">
                                @else
                                    <div class="text-zinc-400 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p>Nenhum favicon carregado</p>
                                    </div>
                                @endif
                            </div>
                            
                            <label for="favicon" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                Carregar novo favicon
                            </label>
                            <input type="file" name="favicon" id="favicon" class="w-full border border-zinc-300 dark:border-zinc-600 rounded-md py-2 px-3 dark:bg-zinc-900">
                            
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                                Formatos recomendados: ICO, PNG ou SVG. Tamanho máximo: 1MB.
                            </p>
                            
                            @error('favicon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 text-right">
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md shadow-sm">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="mt-8 bg-white dark:bg-zinc-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4 text-zinc-900 dark:text-zinc-100">Uso da Identidade Visual</h2>
            <p class="mb-4 text-zinc-700 dark:text-zinc-300">A logo e o favicon são utilizados em diversos lugares do site:</p>
            
            <ul class="list-disc pl-6 text-zinc-700 dark:text-zinc-300 space-y-2">
                <li>A logo é exibida no cabeçalho do site e em emails enviados aos usuários.</li>
                <li>O favicon aparece na aba do navegador e quando os usuários salvam o site como favorito.</li>
                <li>Ambos são essenciais para a identidade de marca e reconhecimento do site.</li>
            </ul>
            
            <div class="mt-4 p-4 border border-yellow-200 bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-800 rounded-lg">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-yellow-700 dark:text-yellow-200">Utilize imagens de alta qualidade para uma melhor experiência do usuário.</p>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
