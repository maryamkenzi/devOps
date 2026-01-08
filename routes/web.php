<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomRegisterController;

/*
|--------------------------------------------------------------------------
| Custom Routes (DevOps Project)
|--------------------------------------------------------------------------
*/

// Page Index personnalisée
Route::get('/index-custom', function () {
    return view('index_custom');
})->name('index.custom');

// Page Register personnalisée (GET)
Route::get('/register-custom', [CustomRegisterController::class, 'showRegistrationForm'])
    ->name('register.custom');

// Submit Register (POST)
Route::post('/register-custom', [CustomRegisterController::class, 'register'])
    ->name('register.custom.submit');

/*
|--------------------------------------------------------------------------
| Original Akaunting Installer Routes (NE PAS TOUCHER)
|--------------------------------------------------------------------------
*/
Route::get('/install', 'Install\Requirements@show');