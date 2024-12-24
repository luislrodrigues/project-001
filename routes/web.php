<?php

use App\Livewire\Company\Company;
use App\Livewire\ComponentManager\ComponentManager;
use App\Livewire\ComponentType\ComponentType;
use App\Livewire\Equipment\Equipment;
use App\Livewire\Report\Report;
use App\Livewire\User\User;
use App\Livewire\UserLogin\UserLogin;
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

Route::middleware('guest')->get('/', function () {
    return view('layouts.app');
})->name('login');
Route::middleware('guest')->get('/login', function () {
    return view('layouts.app');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::post('/logoutUser', [UserLogin::class, 'logoutUser'])->name('logoutUser');
    Route::get('/report', Report::class)->name('report')->middleware('permission:consult report');
    Route::get('/company', Company::class)->name('company')->middleware('permission:consult company');
    Route::get('/component', ComponentManager::class)->name('component');
    Route::get('/equipment', Equipment::class)->name('equipment')->middleware('permission:consult equipment');
    Route::get('/user', User::class)->name('user')->middleware('permission:consult user');
    Route::get('/componentType', ComponentType::class)->name('componentType');
});

