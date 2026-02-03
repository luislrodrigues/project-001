<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Api\StoreComponentTypeRequest;
use App\Http\Requests\Api\UpdateComponentTypeRequest;
use App\Http\Resources\ComponentTypeResource;
use App\Models\ComponentType;
use App\Models\ComponentTypeField;
use App\Models\ComponentTypeFieldOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComponentTypeController extends Controller
{
    public function getComponentTypesForSelect(): JsonResponse
    {
        $types = ComponentType::where('status', 'active')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return ApiResponse::success($types);
    }

    public function index(Request $request): JsonResponse
    {
        if (!auth()->user()->can('consult equipment')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        $query = ComponentType::where('status', 'active');

        if ($request->has('search') && $request->search !== '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sortBy = in_array($request->get('sort_by', 'id'), ['id', 'name', 'created_at']) ? $request->sort_by : 'id';
        $sortOrder = in_array(strtolower($request->get('sort_order', 'desc')), ['asc', 'desc']) ? strtolower($request->sort_order) : 'desc';
        $types = $query->orderBy($sortBy, $sortOrder)->paginate($request->get('per_page', 10));

        return ApiResponse::success(ComponentTypeResource::collection($types));
    }

    public function store(StoreComponentTypeRequest $request): JsonResponse
    {
        try {
            $componentType = ComponentType::create([
                'name' => $request->name,
                'status' => 'active',
            ]);

            foreach ($request->fields as $index => $fieldData) {
                $field = $componentType->componentTypeFields()->create([
                    'name' => $fieldData['name'],
                    'type' => $fieldData['type'],
                    'unit' => $fieldData['unit'] ?? null,
                    'display_order' => $index + 1,
                    'is_required' => $fieldData['is_required'] ?? false,
                ]);

                if (in_array($fieldData['type'], ['select', 'multiselect']) && !empty($fieldData['options'])) {
                    foreach ($fieldData['options'] as $order => $optionData) {
                        $field->options()->create([
                            'label' => $optionData['label'],
                            'value' => $optionData['value'],
                            'display_order' => $optionData['display_order'] ?? ($order + 1),
                            'is_default' => $optionData['is_default'] ?? false,
                            'status' => 'active',
                        ]);
                    }
                }
            }

            return ApiResponse::success(
                new ComponentTypeResource($componentType->load('componentTypeFields.allOptions')),
                'Tipo de componente creado correctamente',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Error al crear el tipo de componente', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    public function show(ComponentType $componentType): JsonResponse
    {
        if (!auth()->user()->can('consult equipment')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        return ApiResponse::success(new ComponentTypeResource($componentType->load('componentTypeFields.allOptions')));
    }

    public function update(UpdateComponentTypeRequest $request, ComponentType $componentType): JsonResponse
    {
        try {
            $componentType->update([
                'name' => $request->name,
            ]);

            $existingFieldIds = $componentType->componentTypeFields()->pluck('id')->toArray();
            $requestedFieldIds = [];

            foreach ($request->fields as $index => $fieldData) {
                if (isset($fieldData['id'])) {
                    // Actualizar campo existente
                    $requestedFieldIds[] = $fieldData['id'];
                    $field = $componentType->componentTypeFields()->find($fieldData['id']);

                    if ($field) {
                        $field->update([
                            'name' => $fieldData['name'],
                            'type' => $fieldData['type'],
                            'unit' => $fieldData['unit'] ?? null,
                            'display_order' => $index + 1,
                            'is_required' => $fieldData['is_required'] ?? false,
                        ]);

                        if (in_array($fieldData['type'], ['select', 'multiselect'])) {
                            $existingOptionIds = $field->allOptions()->pluck('id')->toArray();
                            $requestedOptionIds = [];

                            if (!empty($fieldData['options'])) {
                                foreach ($fieldData['options'] as $order => $optionData) {
                                    if (isset($optionData['id'])) {
                                        // Actualizar opción existente
                                        $requestedOptionIds[] = $optionData['id'];
                                        $option = $field->allOptions()->find($optionData['id']);
                                        if ($option) {
                                            $option->update([
                                                'label' => $optionData['label'],
                                                'value' => $optionData['value'],
                                                'display_order' => $optionData['display_order'] ?? ($order + 1),
                                                'is_default' => $optionData['is_default'] ?? false,
                                            ]);
                                        }
                                    } else {
                                        $newOption = $field->options()->create([
                                            'label' => $optionData['label'],
                                            'value' => $optionData['value'],
                                            'display_order' => $optionData['display_order'] ?? ($order + 1),
                                            'is_default' => $optionData['is_default'] ?? false,
                                            'status' => 'active',
                                        ]);
                                        $requestedOptionIds[] = $newOption->id;
                                    }
                                }
                            }

                            $optionsToDelete = array_diff($existingOptionIds, $requestedOptionIds);
                            if (!empty($optionsToDelete)) {
                                $field->allOptions()
                                    ->whereIn('id', $optionsToDelete)
                                    ->whereDoesntHave('componentValues')
                                    ->delete();
                            }
                        } else {
                            // Si cambió de select/multiselect a otro tipo, eliminar opciones
                            $field->allOptions()->delete();
                        }
                    }
                } else {
                    $field = $componentType->componentTypeFields()->create([
                        'name' => $fieldData['name'],
                        'type' => $fieldData['type'],
                        'unit' => $fieldData['unit'] ?? null,
                        'display_order' => $index + 1,
                        'is_required' => $fieldData['is_required'] ?? false,
                    ]);

                    // Crear opciones si es select/multiselect
                    if (in_array($fieldData['type'], ['select', 'multiselect']) && !empty($fieldData['options'])) {
                        foreach ($fieldData['options'] as $order => $optionData) {
                            $field->options()->create([
                                'label' => $optionData['label'],
                                'value' => $optionData['value'],
                                'display_order' => $optionData['display_order'] ?? ($order + 1),
                                'is_default' => $optionData['is_default'] ?? false,
                                'status' => 'active',
                            ]);
                        }
                    }
                }
            }

            // Eliminar campos que ya no están (solo si no tienen valores asociados)
            $fieldsToDelete = array_diff($existingFieldIds, $requestedFieldIds);
            if (!empty($fieldsToDelete)) {
                foreach ($fieldsToDelete as $fieldId) {
                    $field = ComponentTypeField::find($fieldId);
                    if ($field && $field->values()->count() === 0) {
                        $field->allOptions()->delete();
                        $field->delete();
                    }
                }
            }

            return ApiResponse::success(
                new ComponentTypeResource($componentType->fresh()->load('componentTypeFields.allOptions')),
                'Tipo de componente actualizado correctamente'
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el tipo de componente', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    public function destroy(ComponentType $componentType): JsonResponse
    {
        if (!auth()->user()->can('delete equipment')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        try {
            $componentType->status = 'inactive';
            $componentType->save();
            $componentType->delete();

            return ApiResponse::success(null, 'Tipo de componente eliminado correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error('Error al eliminar el tipo de componente', 500, ['detail' => [$e->getMessage()]]);
        }
    }
}
