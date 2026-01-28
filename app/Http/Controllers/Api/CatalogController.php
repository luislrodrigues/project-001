<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
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
            return response()->json(['message' => 'Catálogo no encontrado'], 404);
        }

        // Construir regla unique según si el modelo usa SoftDeletes
        $uniqueRule = 'required|string|max:255|unique:' . $this->getTableName($catalog) . ',name';
        if ($catalog !== 'ubications') {
            $uniqueRule .= ',NULL,id,deleted_at,NULL';
        }

        $validator = Validator::make($request->all(), [
            'name' => $uniqueRule,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $modelClass = $this->modelMap[$catalog];
            $data = ['name' => ucfirst(trim($request->name))];
            
            // Solo agregar status si el modelo lo tiene
            if (in_array('status', (new $modelClass)->getFillable())) {
                $data['status'] = 'active';
            }
            
            $item = $modelClass::create($data);

            return response()->json([
                'message' => 'Registro creado correctamente',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el registro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $catalog, int $id): JsonResponse
    {
        if (!isset($this->modelMap[$catalog])) {
            return response()->json(['message' => 'Catálogo no encontrado'], 404);
        }

        // Construir regla unique según si el modelo usa SoftDeletes
        $uniqueRule = 'required|string|max:255|unique:' . $this->getTableName($catalog) . ',name,' . $id;
        if ($catalog !== 'ubications') {
            $uniqueRule .= ',id,deleted_at,NULL';
        }

        $validator = Validator::make($request->all(), [
            'name' => $uniqueRule,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $modelClass = $this->modelMap[$catalog];
            $item = $modelClass::findOrFail($id);
            $item->update([
                'name' => ucfirst(trim($request->name)),
            ]);

            return response()->json([
                'message' => 'Registro actualizado correctamente',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el registro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $catalog, int $id): JsonResponse
    {
        if (!isset($this->modelMap[$catalog])) {
            return response()->json(['message' => 'Catálogo no encontrado'], 404);
        }

        try {
            $modelClass = $this->modelMap[$catalog];
            $item = $modelClass::findOrFail($id);
            
            // Verificar si el modelo usa SoftDeletes
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
                    // Si no tiene status, no permitir eliminación
                    return response()->json([
                        'message' => 'No se puede eliminar este registro'
                    ], 400);
                }
            }

            return response()->json([
                'message' => 'Registro eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el registro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getTableName(string $catalog): string
    {
        $tableMap = [
            'brands' => 'brands',
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
