<?php

use App\Http\Controllers\DivisiController;
use Illuminate\Support\Facades\Route;

Route::resource('divisi', DivisiController::class);

Route::get('/', function () {
    return view('welcome');
});


