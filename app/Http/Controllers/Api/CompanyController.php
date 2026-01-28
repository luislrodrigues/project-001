<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCompanyRequest;
use App\Http\Requests\Api\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    /**
     * Listar empresas con paginación
     */
    public function index(Request $request): AnonymousResourceCollection
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

        return CompanyResource::collection($companies);
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

            return response()->json([
                'message' => 'Se guardó la empresa correctamente',
                'data' => new CompanyResource($company)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al guardar la empresa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar empresa específica
     */
    public function show(Company $company): JsonResponse
    {
        return response()->json([
            'data' => new CompanyResource($company)
        ]);
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

            return response()->json([
                'message' => 'Se actualizó la empresa correctamente',
                'data' => new CompanyResource($company)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la empresa',
                'error' => $e->getMessage()
            ], 500);
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

            return response()->json([
                'message' => 'La empresa se eliminó correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la empresa',
                'error' => $e->getMessage()
            ], 500);
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

        return response()->json([
            'data' => $companies
        ]);
    }
}
