<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';

Route::resource('categorias', App\Http\Controllers\CategoriaController::class); // gerado automaticamente pela biblioteca

Route::resource('produtos', App\Http\Controllers\ProdutoController::class); // gerado automaticamente pela biblioteca

Route::resource('posts', App\Http\Controllers\PostController::class); // gerado automaticamente pela biblioteca

Route::resource('tags', App\Http\Controllers\TagController::class); // gerado automaticamente pela biblioteca

Route::resource('posts', App\Http\Controllers\PostController::class); // gerado automaticamente pela biblioteca

Route::resource('posts', App\Http\Controllers\PostController::class); // gerado automaticamente pela biblioteca

Route::resource('posts', App\Http\Controllers\PostController::class); // gerado automaticamente pela biblioteca

Route::resource('posts', App\Http\Controllers\PostController::class); // gerado automaticamente pela biblioteca

Route::resource('posts', App\Http\Controllers\PostController::class); // gerado automaticamente pela biblioteca

Route::resource('tags', App\Http\Controllers\TagController::class); // gerado automaticamente pela biblioteca

Route::resource('tags', App\Http\Controllers\TagController::class); // gerado automaticamente pela biblioteca
