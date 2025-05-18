<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware([
    'auth',
])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});
