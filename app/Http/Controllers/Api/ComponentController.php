<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Api\StoreComponentRequest;
use App\Http\Requests\Api\UpdateComponentRequest;
use App\Http\Resources\ComponentResource;
use App\Models\Brand;
use App\Models\Component;
use App\Models\ComponentTypeField;
use App\Models\ComponentTypeValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Component::with(['equipment.company', 'componentType', 'brand', 'fieldValues']);

        // Filtrar por empresa si el usuario es Cliente
        if (auth()->user()->hasRole('Cliente') && auth()->user()->company_id) {
            $query->whereHas('equipment', function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            });
        }

        if ($request->has('equipment_id')) {
            $query->where('equipment_id', $request->equipment_id);
        }

        if ($request->has('component_type_id')) {
            $query->where('component_type_id', $request->component_type_id);
        }

        if ($request->filled('company_id')) {
            $companyId = (int) $request->company_id;
            $query->whereHas('equipment', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        $sortByRaw = $request->get('sort_by', 'id');
        $allowedSort = ['id', 'created_at'];
        $sortBy = (is_string($sortByRaw) && $sortByRaw !== '' && in_array($sortByRaw, $allowedSort, true))
            ? $sortByRaw
            : 'id';
        $sortOrderRaw = $request->get('sort_order', 'desc');
        $sortOrder = (is_string($sortOrderRaw) && in_array(strtolower($sortOrderRaw), ['asc', 'desc'], true))
            ? strtolower($sortOrderRaw)
            : 'desc';
        $components = $query->orderBy($sortBy, $sortOrder)->paginate($request->get('per_page', 10));

        return ApiResponse::success(ComponentResource::collection($components));
    }

    public function store(StoreComponentRequest $request): JsonResponse
    {
        try {
            $component = Component::create([
                'equipment_id' => $request->equipment_id,
                'component_type_id' => $request->component_type_id,
                'brand_id' => $request->brand_id,
            ]);

            if ($request->has('field_values') && is_array($request->field_values)) {
                foreach ($request->field_values as $fieldId => $value) {
                    $fieldId = (int) $fieldId;
                    if ($fieldId <= 0 || $value === '' || $value === null) {
                        continue;
                    }

                    $field = ComponentTypeField::find($fieldId);
                    if (!$field) {
                        continue;
                    }

                    $optionId = null;
                    $storedValue = $value;

                    if ($field->hasOptions()) {
                        $option = $field->options()->where('value', $value)->first();
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
                        'raw_value' => $value,
                    ]);
                }
            }

            return ApiResponse::success(
                new ComponentResource($component->load(['equipment', 'componentType', 'brand', 'fieldValues'])),
                'Se guardó el componente correctamente',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Error al guardar el componente', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    public function show(Component $component): JsonResponse
    {
        return ApiResponse::success(
            new ComponentResource($component->load(['equipment', 'componentType', 'brand', 'fieldValues']))
        );
    }

    public function update(UpdateComponentRequest $request, Component $component): JsonResponse
    {
        try {
            $component->update([
                'equipment_id' => $request->equipment_id,
                'component_type_id' => $request->component_type_id,
                'brand_id' => $request->brand_id,
            ]);

            ComponentTypeValue::where('component_id', $component->id)->forceDelete();

            $fieldValues = $request->input('field_values');
            if (is_string($fieldValues)) {
                $fieldValues = json_decode($fieldValues, true);
            }
            if (is_array($fieldValues)) {
                $items = $this->normalizeFieldValuesForUpdate($fieldValues);
                foreach ($items as $item) {
                    $fieldId = (int) $item['field_id'];
                    $value = $item['value'] ?? '';
                    if ($fieldId <= 0) {
                        continue;
                    }
                    if ($value === '' || $value === null) {
                        continue;
                    }
                    $value = is_string($value) ? trim($value) : (string) $value;

                    $field = ComponentTypeField::find($fieldId);
                    if (! $field) {
                        continue;
                    }

                    $optionId = null;
                    $storedValue = $value;

                    if ($field->hasOptions()) {
                        $option = $field->options()->where('value', (string) $value)->first();
                        if ($option) {
                            $optionId = $option->id;
                            $storedValue = $option->value;
                        }
                    }

                    ComponentTypeValue::create([
                        'component_id' => $component->id,
                        'component_type_field_id' => $fieldId,
                        'option_id' => $optionId,
                        'value' => (string) $storedValue,
                        'raw_value' => $value,
                    ]);
                }
            }

            return ApiResponse::success(
                new ComponentResource($component->load(['equipment', 'componentType', 'brand', 'fieldValues'])),
                'Se actualizó el componente correctamente'
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el componente', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    /**
     * Normaliza field_values: acepta array de { field_id, value } o objeto { "1": "x", "2": "y" }.
     * Siempre devuelve lista de [ 'field_id' => int, 'value' => string ] para evitar cruce de campos.
     */
    private function normalizeFieldValuesForUpdate(array $fieldValues): array
    {
        $first = reset($fieldValues);
        $isListFormat = is_array($first) && array_key_exists('field_id', $first);

        if ($isListFormat) {
            $items = [];
            foreach ($fieldValues as $item) {
                if (is_array($item) && isset($item['field_id'])) {
                    $items[] = [
                        'field_id' => (int) $item['field_id'],
                        'value' => $item['value'] ?? '',
                    ];
                }
            }
            return $items;
        }

        $items = [];
        foreach ($fieldValues as $fieldId => $value) {
            $items[] = [
                'field_id' => (int) $fieldId,
                'value' => $value,
            ];
        }
        return $items;
    }

    public function destroy(Component $component): JsonResponse
    {
        if (!auth()->user()->can('delete equipment')) {
            abort(403, 'No tienes los permisos requeridos.');
        }

        try {
            $component->status = 'inactive';
            $component->save();
            $component->delete();

            return ApiResponse::success(null, 'El componente se eliminó correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error('Error al eliminar el componente', 500, ['detail' => [$e->getMessage()]]);
        }
    }

    public function getBrands(): JsonResponse
    {
        $brands = Brand::where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'name')
            ->get();

        return ApiResponse::success($brands);
    }

    public function getComponentTypeFields($componentTypeId): JsonResponse
    {
        $fields = ComponentTypeField::with('options')
            ->where('component_type_id', $componentTypeId)
            ->ordered()
            ->get();

        $fieldsData = $fields->map(function ($field) {
            $fieldData = [
                'id' => $field->id,
                'name' => $field->name,
                'type' => $field->type,
                'unit' => $field->unit,
                'is_required' => $field->is_required ?? false,
                'placeholder' => $field->placeholder,
                'help_text' => $field->help_text,
            ];

            $optionsRelation = $field->relationLoaded('options') ? $field->getRelation('options') : $field->options()->get();
            if (in_array($field->type, ['select', 'multiselect']) && $optionsRelation->count() > 0) {
                $fieldData['options'] = $optionsRelation->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'value' => $option->value,
                        'label' => $option->label ?? $option->value,
                    ];
                })->toArray();
            }

            return $fieldData;
        });

        return ApiResponse::success($fieldsData);
    }
}
