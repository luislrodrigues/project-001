<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ComponentController;
use App\Http\Controllers\Api\ComponentTypeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas públicas de autenticación
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Usuario autenticado
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Empresas (POST para update evita problemas de parsing PUT)
    Route::post('companies/{company}', [CompanyController::class, 'update'])->middleware('permission:consult company');
    Route::apiResource('companies', CompanyController::class)->middleware('permission:consult company');

    // Equipos (POST para update permite FormData con imagen; PUT no parsea multipart en PHP)
    Route::post('equipment/{equipment}', [EquipmentController::class, 'update'])->middleware('permission:consult equipment');
    Route::apiResource('equipment', EquipmentController::class)->middleware('permission:consult equipment');

    // Reportes (POST para update permite FormData con imágenes; PUT no parsea multipart en PHP)
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->middleware('permission:consult report');
        Route::post('/', [ReportController::class, 'store'])->middleware('permission:save report');
        Route::get('/{id}', [ReportController::class, 'show'])->middleware('permission:consult report');
        Route::post('/{id}', [ReportController::class, 'update'])->middleware('permission:edit report');
        Route::put('/{id}', [ReportController::class, 'update'])->middleware('permission:edit report');
        Route::delete('/{id}', [ReportController::class, 'destroy'])->middleware('permission:delete report');
        Route::get('/{id}/pdf', [ReportController::class, 'downloadPdf'])->middleware('permission:consult report');
        Route::post('/export', [ReportController::class, 'export'])->middleware('permission:consult report');
        Route::post('/{id}/send-email', [ReportController::class, 'sendEmail'])->middleware('permission:consult report');
    });

    // Componentes (POST para update evita problemas de parsing PUT)
    Route::post('components/{component}', [ComponentController::class, 'update'])->middleware('permission:consult equipment');
    Route::apiResource('components', ComponentController::class)->middleware('permission:consult equipment');
    Route::get('/components/type/{componentTypeId}/fields', [ComponentController::class, 'getComponentTypeFields']);

    // Tipos de Componentes (POST para update evita problemas de parsing PUT)
    Route::post('component-types/{component_type}', [ComponentTypeController::class, 'update'])->middleware('permission:consult equipment');
    Route::apiResource('component-types', ComponentTypeController::class)->middleware('permission:consult equipment');

    // Usuarios (POST para update evita problemas de parsing PUT)
    Route::post('users/{user}', [UserController::class, 'update'])->middleware('permission:consult user');
    Route::apiResource('users', UserController::class)->middleware('permission:consult user');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->middleware('permission:inactive user');
    Route::get('/catalogs/roles', [UserController::class, 'getRoles']);

    // Catálogos y opciones
    Route::get('/catalogs/conditions', [ReportController::class, 'getConditions']);
    Route::get('/catalogs/mechanism-fails', [ReportController::class, 'getMechanismFails']);
    Route::get('/catalogs/component-fails', [ReportController::class, 'getComponentFails']);
    Route::get('/catalogs/equipment-types', [EquipmentController::class, 'getEquipmentTypes']);
    Route::get('/catalogs/processes', [EquipmentController::class, 'getProcesses']);
    Route::get('/catalogs/ubications', [EquipmentController::class, 'getUbications']);
    Route::get('/catalogs/brands', [ComponentController::class, 'getBrands']);
    Route::get('/catalogs/companies', [CompanyController::class, 'getCompanies']);
    Route::get('/catalogs/users', [UserController::class, 'getUsers']);

    // CRUD para catálogos
    Route::prefix('catalogs')->group(function () {
        Route::post('/{catalog}', [\App\Http\Controllers\Api\CatalogController::class, 'store']);
        Route::put('/{catalog}/{id}', [\App\Http\Controllers\Api\CatalogController::class, 'update']);
        Route::delete('/{catalog}/{id}', [\App\Http\Controllers\Api\CatalogController::class, 'destroy']);
    });
});
