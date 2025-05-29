<x-admin-layout title="Adicionar novo disco">
<!-- Meta tags para URLs -->
<meta name="store-vinyl-url" content="{{ route('admin.vinyls.store') }}">
<meta name="vinyl-index-url" content="{{ route('admin.vinyls.index') }}">
<meta name="complete-vinyl-url" content="{{ route('admin.vinyls.complete', ':id') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<div
    x-data="{
        loading: false,
        search() {
            this.loading = true;
            document.getElementById('search-form').submit();
        }
    }" 
    class="p-4">


    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Pesquisar novo disco:</h2>

       <x-admin.vinyls-components.search-discogs :query="$query" />

        <div id="searchResults" class="mt-6">
            @if($selectedRelease)
                <!-- Selected Release Content -->
            <x-admin.vinyls-components.selected-release :release="$selectedRelease" />
            @elseif(count($searchResults) > 0)
                <!-- Search Results -->
               <x-admin.vinyls-components.search-result :searchResults="$searchResults" :query="$query" />
            @elseif($query)
                <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-blue-900 dark:text-blue-300" role="alert">
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span>Nenhum resultado encontrado para "{{ $query }}".</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- Nenhum modal é necessário, estamos redirecionando diretamente -->
</div>
</x-admin-layout>
@push('scripts')
<script>
// Variável global para armazenar o ID do vinyl salvo
let savedVinylId = null;

// Função para testar o modal
function testModal() {
    // Definir o conteúdo do modal
    document.getElementById('modal-title').textContent = 'Teste do Modal';
    document.getElementById('modal-message').textContent = 'Este é um teste do modal para verificar se está funcionando';
    
    // Mostrar botões de sucesso
    document.getElementById('success-buttons').classList.remove('hidden');
    document.getElementById('exists-button').classList.add('hidden');
    document.getElementById('error-button').classList.add('hidden');
    
    // Configurar URL de completar cadastro
    }
    
    if (!document.querySelector('meta[name="complete-vinyl-url"]')) {
        const completeUrl = document.createElement('meta');
        completeUrl.name = 'complete-vinyl-url';
// Função para salvar o disco com JavaScript puro
function saveVinyl(releaseId) {
    // Pegar o botão que foi clicado
    const saveButton = event.target.closest('button');
    
    // Mostrar loading no botão
    if (saveButton) {
        saveButton.disabled = true;
        saveButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Salvando...';
    }
    
    // Obter o CSRF token do Laravel
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Fazer requisição para salvar o disco
    fetch('{{ route("admin.vinyls.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ release_id: releaseId })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error('Erro HTTP: ' + response.status + ' ' + text);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Resposta:', data);
        
        // Dependendo do status da resposta, fazer uma ação diferente
        if (data.status === 'success' && data.vinyl_id) {
            // Redirecionar para a página de completar o cadastro
            window.location.href = '{{ route("admin.vinyls.complete", ":id") }}'.replace(':id', data.vinyl_id);
        } else if (data.status === 'exists') {
            // Mostrar alerta se o disco já existir
            alert(data.message || 'Este disco já está cadastrado no sistema.');
            // Restaurar o botão
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.innerHTML = '<span>Salvar disco</span>';
            }
        } else {
            // Mostrar erro genérico
            alert(data.message || 'Ocorreu um erro ao salvar o disco.');
            // Restaurar o botão
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.innerHTML = '<span>Salvar disco</span>';
            }
        }
    })
    .catch(error => {
        // Tratar erros de requisição
        console.error('Erro:', error);
        alert(error.message || 'Ocorreu um erro ao salvar o disco.');
        
        // Restaurar o botão
        if (saveButton) {
            saveButton.disabled = false;
            saveButton.innerHTML = '<span>Salvar disco</span>';
        }
    });
}
</script>
@endpush
