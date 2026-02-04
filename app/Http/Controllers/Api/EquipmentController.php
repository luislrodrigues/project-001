<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Api\StoreEquipmentRequest;
use App\Http\Requests\Api\UpdateEquipmentRequest;
use App\Http\Resources\EquipmentResource;
use App\Models\Component;
use App\Models\Equipment;
use App\Models\EquipmentType;
use App\Models\Process;
use App\Models\Ubication;
use App\Models\ComponentTypeField;
use App\Models\ComponentTypeValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Equipment::with(['company', 'equipmentType', 'process', 'ubication']);

        // Filtrar por empresa si el usuario es Cliente
        if (auth()->user()->hasRole('Cliente') && auth()->user()->company_id) {
            $query->where('company_id', auth()->user()->company_id);
        }

        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', (int) $request->company_id);
        }

        if ($request->has('equipment_type_id')) {
            $query->where('equipment_type_id', $request->equipment_type_id);
        }

        $sortByRaw = $request->get('sort_by', 'id');
        $allowedSort = ['id', 'name', 'created_at'];
        $sortBy = (is_string($sortByRaw) && $sortByRaw !== '' && in_array($sortByRaw, $allowedSort, true))
            ? $sortByRaw
            : 'id';
        $sortOrderRaw = $request->get('sort_order', 'desc');
        $sortOrder = (is_string($sortOrderRaw) && in_array(strtolower($sortOrderRaw), ['asc', 'desc'], true))
            ? strtolower($sortOrderRaw)
            : 'desc';
        $equipments = $query->orderBy($sortBy, $sortOrder)->paginate($request->get('per_page', 10));

        return ApiResponse::success(EquipmentResource::collection($equipments));
    }

    public function store(StoreEquipmentRequest $request): JsonResponse
    {
        try {
            $data = [
                'name' => $request->name,
                'code' => $request->code,
                'tag' => $request->tag,
                'description' => $request->description,
                'company_id' => $request->company_id,
                'process_id' => $request->process_id,
                'ubication_id' => $request->ubication_id,
                'equipment_type_id' => $request->equipment_type_id,
                'status' => 'active',
            ];
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $data['image'] = $request->file('image')->store('images', 'public');
            }
            $equipment = Equipment::create($data);

            $componentsInput = $request->input('components');
            if (is_string($componentsInput)) {
                $componentsInput = json_decode($componentsInput, true);
            }
            if (is_array($componentsInput)) {
                foreach ($componentsInput as $item) {
                    $typeId = isset($item['component_type_id']) ? (int) $item['component_type_id'] : 0;
                    $brandId = isset($item['brand_id']) ? (int) $item['brand_id'] : 0;
                    if ($typeId <= 0 || $brandId <= 0) {
                        continue;
                    }
                    $component = $equipment->components()->create([
                        'component_type_id' => $typeId,
                        'brand_id' => $brandId,
                        'status' => 'active',
                    ]);
                    $fieldValues = $item['field_values'] ?? [];
                    if (is_array($fieldValues)) {
                        foreach ($fieldValues as $fieldId => $value) {
                            $fieldId = (int) $fieldId;
                            if ($fieldId <= 0 || $value === '' || $value === null) {
                                continue;
                            }
                            $field = ComponentTypeField::find($fieldId);
                            if (!$field) {
                                continue;
                            }
                            $optionId = null;
                            $storedValue = is_scalar($value) ? (string) $value : '';
                            if ($field->hasOptions()) {
                                $option = $field->options()->where('value', $storedValue)->first();
                                if ($option) {
                                    $optionId = $option->id;
                                    $storedValue = $option->value;
                                }
                            }
                            ComponentTypeValue::create([
                                'component_id' => $component->id,
                                'component_type_field_id' => $fieldId,
                                'option_id' => $optionId,
                                'value' => $storedValue,
                                'raw_value' => is_scalar($value) ? (string) $value : '',
                            ]);
                        }
                    }
                }
            }

            return ApiResponse::success(
                new EquipmentResource($equipment->load(['company', 'equipmentType', 'process', 'ubication', 'components.componentType', 'components.brand', 'components.fieldValues'])),
                'Se guardó el equipo correctamente',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Error al guardar el equipo', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    public function show(Equipment $equipment): JsonResponse
    {
        $equipment->load([
            'company',
            'equipmentType',
            'process',
            'ubication',
            'components.componentType',
            'components.brand',
            'components.fieldValues.componentTypeField',
            'components.fieldValues.option',
        ]);

        return ApiResponse::success(new EquipmentResource($equipment));
    }

    public function update(UpdateEquipmentRequest $request, Equipment $equipment): JsonResponse
    {
        try {
            $data = [
                'name' => $request->name,
                'code' => $request->code,
                'tag' => $request->tag,
                'description' => $request->description,
                'company_id' => $request->company_id,
                'process_id' => $request->process_id,
                'ubication_id' => $request->ubication_id,
                'equipment_type_id' => $request->equipment_type_id,
            ];
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                if ($equipment->image) {
                    Storage::disk('public')->delete($equipment->image);
                }
                $data['image'] = $request->file('image')->store('images', 'public');
            }
            $equipment->update($data);

            return ApiResponse::success(
                new EquipmentResource($equipment->load(['company', 'equipmentType', 'process', 'ubication'])),
                'Equipo actualizado correctamente'
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el equipo', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    public function destroy(Equipment $equipment): JsonResponse
    {
        if (!auth()->user()->can('delete equipment')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        try {
            $equipment->status = 'inactive';
            $equipment->save();
            $equipment->delete();

            return ApiResponse::success(null, 'El equipo se eliminó correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error('Error al eliminar el equipo', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    public function getEquipmentForSelect(): JsonResponse
    {
        $query = Equipment::with(['company', 'equipmentType'])
            ->where('status', 'active');

        if (auth()->user()->hasRole('Cliente') && auth()->user()->company_id) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $equipment = $query->orderBy('name')->get();

        return ApiResponse::success($equipment);
    }

    public function getEquipmentTypes(): JsonResponse
    {
        $types = EquipmentType::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name')
            ->get();

        return ApiResponse::success($types);
    }

    public function getProcesses(): JsonResponse
    {
        $processes = Process::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name')
            ->get();

        return ApiResponse::success($processes);
    }

    public function getUbications(): JsonResponse
    {
        $ubications = Ubication::where('status', 'active')
            ->select('id', 'name')
            ->get();

        return ApiResponse::success($ubications);
    }
}
