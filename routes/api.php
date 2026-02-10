<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ComponentController;
use App\Http\Controllers\Api\ComponentTypeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\IndicadoresController;
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
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Usuario autenticado
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Indicadores estadísticos (datos existentes)
    Route::get('/indicadores', [IndicadoresController::class, 'index'])->middleware('permission:consult indicators');

    // Empresas (POST para update evita problemas de parsing PUT; show requiere detalle company)
    Route::get('companies', [CompanyController::class, 'index'])->middleware('permission:consult company');
    Route::get('companies/{company}', [CompanyController::class, 'show'])->middleware('permission:detalle company');
    Route::post('companies', [CompanyController::class, 'store'])->middleware('permission:save company');
    Route::post('companies/{company}', [CompanyController::class, 'update'])->middleware('permission:edit company');
    Route::delete('companies/{company}', [CompanyController::class, 'destroy'])->middleware('permission:delete company');

    // Equipos (POST para update permite FormData con imagen; show requiere detalle equipment)
    Route::get('equipment', [EquipmentController::class, 'index'])->middleware('permission:consult equipment');
    Route::get('equipment/{equipment}', [EquipmentController::class, 'show'])->middleware('permission:detalle equipment');
    Route::post('equipment', [EquipmentController::class, 'store'])->middleware('permission:save equipment');
    Route::post('equipment/{equipment}', [EquipmentController::class, 'update'])->middleware('permission:edit equipment');
    Route::delete('equipment/{equipment}', [EquipmentController::class, 'destroy'])->middleware('permission:delete equipment');

    // Reportes (POST para update permite FormData con imágenes; show requiere detalle report)
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->middleware('permission:consult report');
        Route::post('/', [ReportController::class, 'store'])->middleware('permission:save report');
        Route::get('/{id}', [ReportController::class, 'show'])->middleware('permission:detalle report');
        Route::post('/{id}', [ReportController::class, 'update'])->middleware('permission:edit report');
        Route::put('/{id}', [ReportController::class, 'update'])->middleware('permission:edit report');
        Route::delete('/{id}', [ReportController::class, 'destroy'])->middleware('permission:delete report');
        Route::get('/{id}/pdf', [ReportController::class, 'downloadPdf'])->middleware('permission:download pdf report');
        Route::get('/{id}/email-recipients', [ReportController::class, 'getEmailRecipients'])->middleware('permission:send email report');
        Route::post('/export', [ReportController::class, 'export'])->middleware('permission:consult report');
        Route::post('/{id}/send-email', [ReportController::class, 'sendEmail'])->middleware('permission:send email report');
    });

    // Componentes (comentado - sin página de listado)
    // Route::post('components/{component}', [ComponentController::class, 'update'])->middleware('permission:consult equipment');
    // Route::apiResource('components', ComponentController::class)->middleware('permission:consult equipment');
    Route::get('/components/type/{componentTypeId}/fields', [ComponentController::class, 'getComponentTypeFields']);

    // Tipos de Componentes (POST para update evita problemas de parsing PUT; show requiere detalle component type)
    Route::get('component-types', [ComponentTypeController::class, 'index'])->middleware('permission:consult component type');
    Route::get('component-types/{component_type}', [ComponentTypeController::class, 'show'])->middleware('permission:detalle component type');
    Route::post('component-types', [ComponentTypeController::class, 'store'])->middleware('permission:save component type');
    Route::post('component-types/{component_type}', [ComponentTypeController::class, 'update'])->middleware('permission:edit component type');
    Route::delete('component-types/{component_type}', [ComponentTypeController::class, 'destroy'])->middleware('permission:delete component type');

    // Usuarios (POST para update evita problemas de parsing PUT; show requiere detalle user)
    Route::get('users', [UserController::class, 'index'])->middleware('permission:consult user');
    Route::get('users/{user}', [UserController::class, 'show'])->middleware('permission:detalle user');
    Route::post('users', [UserController::class, 'store'])->middleware('permission:save user');
    Route::post('users/{user}', [UserController::class, 'update'])->middleware('permission:edit user');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->middleware('permission:delete user');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->middleware('permission:inactive user');
    Route::get('/catalogs/roles', [UserController::class, 'getRoles']);

    // Roles y permisos
    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:consult role');
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:save role');
    Route::get('/roles/{id}', [RoleController::class, 'show'])->middleware('permission:consult role');
    Route::get('/permissions', [RoleController::class, 'permissions'])->middleware('permission:consult role');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->middleware('permission:edit role');
    Route::post('/roles/{id}', [RoleController::class, 'update'])->middleware('permission:edit role');

    // Catálogos y opciones (algunos con permission.or para formularios de creación)
    Route::get('/catalogs/equipment', [EquipmentController::class, 'getEquipmentForSelect'])
        ->middleware('permission.or:consult equipment,save equipment,consult report,save report');
    Route::get('/catalogs/component-types', [ComponentTypeController::class, 'getComponentTypesForSelect'])
        ->middleware('permission.or:consult equipment,save equipment,consult component type,save component type');
    Route::get('/catalogs/conditions', [ReportController::class, 'getConditions']);
    Route::get('/catalogs/priorities', [ReportController::class, 'getPriorities']);
    Route::get('/catalogs/mechanism-fails', [ReportController::class, 'getMechanismFails']);
    Route::get('/catalogs/component-fails', [ReportController::class, 'getComponentFails']);
    Route::get('/catalogs/equipment-types', [EquipmentController::class, 'getEquipmentTypes']);
    Route::get('/catalogs/processes', [EquipmentController::class, 'getProcesses']);
    Route::get('/catalogs/ubications', [EquipmentController::class, 'getUbications']);
    Route::get('/catalogs/brands', [ComponentController::class, 'getBrands']);
    Route::get('/catalogs/companies', [CompanyController::class, 'getCompanies']);
    Route::get('/catalogs/company-segments', [CompanyController::class, 'getCompanySegments']);
    Route::get('/catalogs/users', [UserController::class, 'getUsers']);
    Route::get('/catalogs/analists', [UserController::class, 'getAnalists']);

    // CRUD para catálogos
    Route::prefix('catalogs')->group(function () {
        Route::post('/{catalog}', [\App\Http\Controllers\Api\CatalogController::class, 'store']);
        Route::put('/{catalog}/{id}', [\App\Http\Controllers\Api\CatalogController::class, 'update']);
        Route::delete('/{catalog}/{id}', [\App\Http\Controllers\Api\CatalogController::class, 'destroy']);
    });
});
