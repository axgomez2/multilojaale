<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\VinylController;
use App\Http\Controllers\Admin\CatStyleShopController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeveloperController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\TrackController;
use App\Http\Controllers\Admin\VinylImageController;
use App\Http\Controllers\YouTubeController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\MidiaStatusController;
use App\Http\Controllers\Admin\CoverStatusController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\PosSalesController;  

// Todas as rotas neste arquivo já estão com prefixo 'admin' e middleware 'auth' e 'admin'
// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

// Gerenciamento de Discos
Route::prefix('discos')->group(function () {
    // Listagem e operações básicas
    Route::get('/', [VinylController::class, 'index'])->name('admin.vinyls.index');
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

// YouTube API - acessível sem middleware admin
Route::match(['get', 'post'], '/youtube/search', [YouTubeController::class, 'search'])->name('youtube.search')->withoutMiddleware(['admin']);

// Gerenciamento de categorias de disco
Route::prefix('categorias')->group(function () {
    Route::get('/', [CatStyleShopController::class, 'index'])->name('admin.cat-style-shop.index');
    Route::get('/create', [CatStyleShopController::class, 'create'])->name('admin.cat-style-shop.create');
    Route::post('/', [CatStyleShopController::class, 'store'])->name('admin.cat-style-shop.store');
    Route::get('/{catStyleShop}/edit', [CatStyleShopController::class, 'edit'])->name('admin.cat-style-shop.edit');
    Route::put('/{catStyleShop}', [CatStyleShopController::class, 'update'])->name('admin.cat-style-shop.update');
    Route::delete('/{catStyleShop}', [CatStyleShopController::class, 'destroy'])->name('admin.cat-style-shop.destroy');
});

// Gerenciamento de status de mídia
Route::prefix('midia-status')->group(function () {
    Route::get('/', [MidiaStatusController::class, 'index'])->name('admin.midia-status.index');
    Route::get('/create', [MidiaStatusController::class, 'create'])->name('admin.midia-status.create');
    Route::post('/', [MidiaStatusController::class, 'store'])->name('admin.midia-status.store');
    Route::get('/{midiaStatus}/edit', [MidiaStatusController::class, 'edit'])->name('admin.midia-status.edit');
    Route::put('/{midiaStatus}', [MidiaStatusController::class, 'update'])->name('admin.midia-status.update');
    Route::delete('/{midiaStatus}', [MidiaStatusController::class, 'destroy'])->name('admin.midia-status.destroy');
});

// Gerenciamento de status de capa
Route::prefix('cover-status')->group(function () {
    Route::get('/', [CoverStatusController::class, 'index'])->name('admin.cover-status.index');
    Route::get('/create', [CoverStatusController::class, 'create'])->name('admin.cover-status.create');
    Route::post('/', [CoverStatusController::class, 'store'])->name('admin.cover-status.store');
    Route::get('/{coverStatus}/edit', [CoverStatusController::class, 'edit'])->name('admin.cover-status.edit');
    Route::put('/{coverStatus}', [CoverStatusController::class, 'update'])->name('admin.cover-status.update');
    Route::delete('/{coverStatus}', [CoverStatusController::class, 'destroy'])->name('admin.cover-status.destroy');
});

// Relatórios
Route::prefix('relatorios')->group(function () {
    Route::get('/', [ReportsController::class, 'index'])->name('admin.reports.index');
    Route::get('/discos', [ReportsController::class, 'vinyl'])->name('admin.reports.vinyl');
});

// PDV - Point of Sale (Vendas Diretas)
Route::prefix('pdv')->group(function () {
    Route::get('/', [PosSalesController::class, 'index'])->name('admin.pos.index');
    Route::get('/nova-venda', [PosSalesController::class, 'create'])->name('admin.pos.create');
    Route::post('/venda', [PosSalesController::class, 'store'])->name('admin.pos.store');
    Route::get('/venda/{posSale}', [PosSalesController::class, 'show'])->name('admin.pos.show');
    Route::get('/vendas', [PosSalesController::class, 'list'])->name('admin.pos.list');
    
    // API para autocompletar
    Route::get('/buscar-usuarios', [PosSalesController::class, 'searchUsers'])->name('admin.pos.search-users');
    Route::get('/buscar-discos', [PosSalesController::class, 'searchVinyls'])->name('admin.pos.search-vinyls');
});

// Gerenciamento de fornecedores
Route::prefix('fornecedores')->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('admin.suppliers.index');
    Route::get('/create', [SupplierController::class, 'create'])->name('admin.suppliers.create');
    Route::post('/', [SupplierController::class, 'store'])->name('admin.suppliers.store');
    Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('admin.suppliers.edit');
    Route::put('/{supplier}', [SupplierController::class, 'update'])->name('admin.suppliers.update');
    Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('admin.suppliers.destroy');
});

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

// Media e Cover Status - Ambos já estão definidos acima com rotas individuais
// Removido Route::resource para evitar conflitos de nomes de rotas

// Área do desenvolvedor
Route::middleware(['developer'])->prefix('developer')->group(function () {
    // Identidade Visual (Logo e Favicon)
    Route::get('/branding', [DeveloperController::class, 'showBranding'])->name('admin.developer.branding');
    Route::post('/branding', [DeveloperController::class, 'updateBranding'])->name('admin.developer.branding.update');
    
    // Informações da Loja
    Route::get('/store', [DeveloperController::class, 'showStoreInfo'])->name('admin.developer.store');
    Route::post('/store', [DeveloperController::class, 'updateStoreInfo'])->name('admin.developer.store.update');
});
