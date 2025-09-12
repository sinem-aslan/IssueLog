<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Sayfayı direkt login sayfasına yönlendirir
Route::redirect('/', '/login')->name('login');

// Dashboard rotası, sadece doğrulanmış kullanıcılar erişebilir
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Kullanıcılar için rota tanımlamaları(sadece admin erişebilir)
Route::get('/users', function () {
    return view('users.index');
})->middleware('is_admin')->name('users.index');

// Birimler için rota tanımlamaları (sadece admin erişebilir)
Route::get('/departments', function () {
    return view('departments.index');
})->middleware('is_admin')->name('departments.index');



require __DIR__.'/auth.php';
