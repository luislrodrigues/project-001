<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Ruta raíz para la aplicación Vue (SPA)
Route::get('/', function () {
    return view('app');
});

// Ruta catch-all para todas las demás rutas (debe ir al final)
// Esto permite que Vue Router maneje el enrutamiento del lado del cliente
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

