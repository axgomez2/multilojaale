<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\VinylController;
use App\Http\Controllers\Admin\CatStyleShopController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeveloperController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\TrackController;
use App\Http\Controllers\Admin\VinylImageController;
use App\Http\Controllers\Admin\YouTubeController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\MediaStatusController;
use App\Http\Controllers\Admin\CoverStatusController;  

// Todas as rotas neste arquivo já estão com prefixo 'admin' e middleware 'auth' e 'admin'
// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

// Gerenciamento de Discos
Route::prefix('discos')->group(function () {
    // Listagem e operações básicas
    Route::get('/discos', [VinylController::class, 'index'])->name('admin.vinyls.index');
    Route::get('/adicionar', [VinylController::class, 'create'])->name('admin.vinyls.create');
    Route::post('/salvar', [VinylController::class, 'store'])->name('admin.vinyls.store');
    Route::get('{id}', [VinylController::class, 'show'])->name('admin.vinyls.show');
    Route::get('{id}/edit', [VinylController::class, 'edit'])->name('admin.vinyls.edit');
    Route::put('{id}', [VinylController::class, 'update'])->name('admin.vinyls.update');
    Route::delete('{id}', [VinylController::class, 'destroy'])->name('admin.vinyls.destroy');

    Route::get('/{id}/completar', [VinylController::class, 'complete'])->name('admin.vinyls.complete');
    Route::post('/{id}/completar', [VinylController::class, 'storeComplete'])->name('admin.vinyl.storeComplete');

    Route::get('/{id}/images', [VinylImageController::class, 'index'])->name('admin.vinyl.images');
    Route::post('/{id}/images', [VinylImageController::class, 'store'])->name('admin.vinyl.images.store');
    Route::delete('/{id}/images/{imageId}', [VinylImageController::class, 'destroy'])->name('admin.vinyl.images.destroy');
    Route::post('/update-field', [VinylController::class, 'updateField'])->name('admin.vinyls.updateField');

    Route::post('/{id}/fetch-discogs-image', [VinylController::class, 'fetchDiscogsImage'])->name('admin.vinyls.fetch-discogs-image');
    Route::post('/{id}/upload-image', [VinylController::class, 'uploadImage'])->name('admin.vinyls.upload-image');
    Route::delete('/{id}/remove-image', [VinylController::class, 'removeImage'])->name('admin.vinyls.remove-image');

    //faixas
    Route::get('/{id}/edit-tracks', [TrackController::class, 'editTracks'])->name('admin.vinyls.edit-tracks');
    Route::put('/{id}/update-tracks', [TrackController::class, 'updateTracks'])->name('admin.vinyls.update-tracks');
    

});

// YouTube API
Route::post('/youtube/search', [YouTubeController::class, 'search'])->name('youtube.search');

// Tracks (Faixas de áudio)
Route::resource('tracks', TrackController::class);
Route::post('vinyls/{vinyl}/tracks', [TrackController::class, 'storeForVinyl'])->name('admin.vinyls.tracks.store');
Route::put('vinyls/{vinyl}/tracks/reorder', [TrackController::class, 'reorderTracks'])->name('admin.vinyls.tracks.reorder');

// Categorias, Estilos e Lojas
Route::resource('categories', CatStyleShopController::class, ['as' => 'admin'])->parameters(['categories' => 'category']);
Route::resource('styles', CatStyleShopController::class, ['as' => 'admin'])->parameters(['styles' => 'style']);
Route::resource('shops', CatStyleShopController::class, ['as' => 'admin'])->parameters(['shops' => 'shop']);

// Equipamentos
Route::resource('equipment', EquipmentController::class);
Route::get('equipment/{equipment}/images', [EquipmentController::class, 'showImages'])->name('admin.equipment.images');
Route::post('equipment/{equipment}/images', [EquipmentController::class, 'storeImages'])->name('admin.equipment.images.store');

// Suppliers (Fornecedores)
Route::resource('suppliers', SupplierController::class);

// Media e Cover Status
Route::resource('media-status', MediaStatusController::class, ['as' => 'admin']);
Route::resource('cover-status', CoverStatusController::class, ['as' => 'admin']);

// Área do desenvolvedor
Route::middleware(['developer'])->prefix('developer')->group(function () {
    // Identidade Visual (Logo e Favicon)
    Route::get('/branding', [DeveloperController::class, 'showBranding'])->name('admin.developer.branding');
    Route::post('/branding', [DeveloperController::class, 'updateBranding'])->name('admin.developer.branding.update');
    
    // Informações da Loja
    Route::get('/store', [DeveloperController::class, 'showStoreInfo'])->name('admin.developer.store');
    Route::post('/store', [DeveloperController::class, 'updateStoreInfo'])->name('admin.developer.store.update');
});
