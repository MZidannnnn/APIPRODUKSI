<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisiController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Route::get('/dashboard', [AuthController::class, 'index1'])->name('dashboard'); ini route tes
});


Route::resource('divisi', DivisiController::class);

Route::get('/', function () {
    return view('welcome');
});