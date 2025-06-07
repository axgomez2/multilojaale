<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 w-full">
    @foreach($vinyls as $vinyl)
        <div class="vinyl-card-container">
            <livewire:vinyl-card 
                :wire:key="'vinyl-'.$vinyl->id" 
                :vinyl="$vinyl" 
                :show-actions="true" 
                :size="'normal'"
                :in-wishlist="$wishlistIds->contains($vinyl->id)" 
                :in-wantlist="$wantlistIds->contains($vinyl->id)" 
            />
        </div>
    @endforeach
    
    @if($vinyls->isEmpty())
        <div class="col-span-full text-center py-10">
            <p class="text-gray-500">Nenhum disco encontrado.</p>
        </div>
    @endif
</div>
