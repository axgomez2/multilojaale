<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleSocialiteController;
use Illuminate\Support\Facades\Route;

// Mostrar formulário de login (sobrescreve a rota existente para exibir nosso formulário customizado)
Route::get('login', [LoginController::class, 'show'])
    ->middleware(['guest'])
    ->name('login');

// Processar login por email/senha
Route::post('login/standard', [LoginController::class, 'login'])
    ->middleware(['guest'])
    ->name('login.standard');
    
// Login com Google via Socialite
Route::get('login/google', [GoogleSocialiteController::class, 'redirectToGoogle'])
    ->middleware(['guest'])
    ->name('login.google');

// Callback do Google após autenticação
Route::get('auth/google/callback', [GoogleSocialiteController::class, 'handleGoogleCallback'])
    ->middleware(['guest'])
    ->name('login.google.callback');
    
// Rotas para registro de usuários
Route::get('register', [RegisterController::class, 'showRegistrationForm'])
    ->middleware(['guest'])
    ->name('register');
    
Route::post('register', [RegisterController::class, 'register'])
    ->middleware(['guest']);

// Logout para ambos os tipos de autenticação
Route::post('logout', [LoginController::class, 'logout'])
    ->middleware(['auth'])
    ->name('logout');
