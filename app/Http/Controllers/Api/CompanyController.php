<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Api\StoreCompanyRequest;
use App\Http\Requests\Api\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Listar empresas con paginación
     */
    public function index(Request $request): JsonResponse
    {
        $query = Company::query();

        // Búsqueda por nombre
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filtro por estado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $companies = $query->paginate($request->get('per_page', 10));

        return ApiResponse::success(CompanyResource::collection($companies));
    }

    /**
     * Crear nueva empresa
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        try {
            $company = Company::create([
                'name' => $request->name,
                'emails' => json_encode($request->emails),
                'status' => 'active',
            ]);

            return ApiResponse::success(new CompanyResource($company), 'Se guardó la empresa correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al guardar la empresa', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    /**
     * Mostrar empresa específica
     */
    public function show(Company $company): JsonResponse
    {
        return ApiResponse::success(new CompanyResource($company));
    }

    /**
     * Actualizar empresa
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        try {
            $company->update([
                'name' => $request->name,
                'emails' => json_encode($request->emails),
            ]);

            return ApiResponse::success(new CompanyResource($company), 'Se actualizó la empresa correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar la empresa', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    /**
     * Eliminar empresa (soft delete)
     */
    public function destroy(Company $company): JsonResponse
    {
        if (!$this->authorize('delete company')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        try {
            // Actualizar equipos relacionados
            $company->equipments()->update(['status' => 'inactive']);
            $company->equipments()->delete();

            // Eliminar empresa
            $company->status = 'inactive';
            $company->save();
            $company->delete();

            return ApiResponse::success(null, 'La empresa se eliminó correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error('Error al eliminar la empresa', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    /**
     * Obtener lista de empresas activas para catálogos
     */
    public function getCompanies(): JsonResponse
    {
        $companies = Company::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name')
            ->get();

        return ApiResponse::success($companies);
    }
}
