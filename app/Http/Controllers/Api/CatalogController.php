<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Models\Brand;
use App\Models\CompanySegment;
use App\Models\ComponentFail;
use App\Models\Condition;
use App\Models\EquipmentType;
use App\Models\MechanismFail;
use App\Models\Process;
use App\Models\Ubication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CatalogController extends Controller
{
    private $modelMap = [
        'brands' => Brand::class,
        'company-segments' => CompanySegment::class,
        'equipment-types' => EquipmentType::class,
        'processes' => Process::class,
        'ubications' => Ubication::class,
        'conditions' => Condition::class,
        'component-fails' => ComponentFail::class,
        'mechanism-fails' => MechanismFail::class,
    ];

    public function store(Request $request, string $catalog): JsonResponse
    {
        if (!isset($this->modelMap[$catalog])) {
            return ApiResponse::notFound('Catálogo no encontrado');
        }

        $uniqueRule = 'required|string|max:255|unique:' . $this->getTableName($catalog) . ',name';
        if ($catalog !== 'ubications') {
            $uniqueRule .= ',NULL,id,deleted_at,NULL';
        }

        $rules = ['name' => $uniqueRule];
        if ($catalog === 'conditions') {
            $rules['color'] = 'nullable|string|max:50';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        try {
            $modelClass = $this->modelMap[$catalog];
            $data = ['name' => ucfirst(trim($request->name))];
            if ($catalog === 'conditions' && $request->has('color')) {
                $data['color'] = $request->color ? trim($request->color) : null;
            }
            if (in_array('status', (new $modelClass)->getFillable())) {
                $data['status'] = 'active';
            }

            $item = $modelClass::create($data);

            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al crear el registro', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    public function update(Request $request, string $catalog, int $id): JsonResponse
    {
        if (!isset($this->modelMap[$catalog])) {
            return ApiResponse::notFound('Catálogo no encontrado');
        }

        $uniqueRule = 'required|string|max:255|unique:' . $this->getTableName($catalog) . ',name,' . $id;
        if ($catalog !== 'ubications') {
            $uniqueRule .= ',id,deleted_at,NULL';
        }

        $rules = ['name' => $uniqueRule];
        if ($catalog === 'conditions') {
            $rules['color'] = 'nullable|string|max:50';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        try {
            $modelClass = $this->modelMap[$catalog];
            $item = $modelClass::findOrFail($id);
            $updateData = ['name' => ucfirst(trim($request->name))];
            if ($catalog === 'conditions') {
                $updateData['color'] = $request->color ? trim($request->color) : null;
            }
            $item->update($updateData);

            return ApiResponse::success($item, 'Registro actualizado correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el registro', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    public function destroy(string $catalog, int $id): JsonResponse
    {
        if (!isset($this->modelMap[$catalog])) {
            return ApiResponse::notFound('Catálogo no encontrado');
        }

        try {
            $modelClass = $this->modelMap[$catalog];
            $item = $modelClass::findOrFail($id);

            $traits = class_uses_recursive($modelClass);
            $usesSoftDeletes = in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, $traits);

            if ($usesSoftDeletes) {
                $item->delete();
            } else {
                // Para modelos sin SoftDeletes (como Ubication), actualizar status
                $fillable = (new $modelClass)->getFillable();
                if (in_array('status', $fillable)) {
                    $item->update(['status' => 'inactive']);
                } else {
                    return ApiResponse::error('No se puede eliminar este registro', 400);
                }
            }

            return ApiResponse::success(null, 'Registro eliminado correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error('Error al eliminar el registro', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    private function getTableName(string $catalog): string
    {
        $tableMap = [
            'brands' => 'brands',
            'company-segments' => 'company_segments',
            'equipment-types' => 'equipment_types',
            'processes' => 'processes',
            'ubications' => 'ubications',
            'conditions' => 'conditions',
            'component-fails' => 'component_fails',
            'mechanism-fails' => 'mechanism_fails',
        ];
        return $tableMap[$catalog] ?? 'unknown';
    }
}
