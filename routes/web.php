<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\MembresiaController;


// Ruta principal - Index
Route::get('/', [InicioController::class, 'index'])->name('inicio');
Route::redirect('/inicio', '/');  // Redirecciona /inicio a la raíz

Route::get('/membresias', [MembresiaController::class, 'index'])->name('membresias');

Route::get('/sedes', function () {
    return view('sedes');
})->name('sedes');
Route::get('/contactanos', function () {
    return view('contactanos');
})->name('contactanos');

