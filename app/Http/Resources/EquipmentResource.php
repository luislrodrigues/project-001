<?php

namespace App\Http\Resources;

use App\Models\ComponentTypeField;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $image = $this->getAttribute('image');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'tag' => $this->tag,
            'description' => $this->description,
            'report_type' => $this->report_type ?? 'vibration',
            'image' => $image,
            'image_url' => !empty($image) ? asset('storage/' . ltrim($image, '/')) : null,
            'status' => $this->status,
            'company' => $this->relationLoaded('company') && $this->company ? [
                'id' => $this->company->id,
                'name' => $this->company->name,
            ] : null,
            'equipment_type' => $this->relationLoaded('equipmentType') && $this->equipmentType ? [
                'id' => $this->equipmentType->id,
                'name' => $this->equipmentType->name,
            ] : null,
            'process' => $this->relationLoaded('process') && $this->process ? [
                'id' => $this->process->id,
                'name' => $this->process->name,
            ] : null,
            'ubication' => $this->relationLoaded('ubication') && $this->ubication ? [
                'id' => $this->ubication->id,
                'name' => $this->ubication->name,
            ] : null,
            'components' => $this->whenLoaded('components', function () {
                return $this->components->map(function ($component) {
                    $attributes = [];
                    $fieldValues = [];
                    if ($component->relationLoaded('fieldValues')) {
                        foreach ($component->fieldValues as $fv) {
                            $fieldId = $fv->component_type_field_id;
                            $attributes[] = [
                                'name' => $fv->componentTypeField->name ?? '',
                                'value' => $fv->display_value ?? $fv->value ?? '',
                                'unit' => $fv->componentTypeField->unit ?? '',
                            ];
                            $fieldValues[(string) $fieldId] = $fv->value ?? $fv->raw_value ?? '';
                        }
                    }
                    return [
                        'id' => $component->id,
                        'component_type_id' => $component->component_type_id ?? ($component->componentType->id ?? null),
                        'brand_id' => $component->brand_id ?? ($component->brand->id ?? null),
                        'component_type' => [
                            'id' => $component->componentType->id ?? null,
                            'name' => $component->componentType->name ?? null,
                            'fields' => ($component->componentType && $component->componentType->relationLoaded('componentTypeFields'))
                                ? ComponentTypeField::definitions($component->componentType->componentTypeFields)
                                : [],
                        ],
                        'brand' => [
                            'id' => $component->brand->id ?? null,
                            'name' => $component->brand->name ?? null,
                        ],
                        'attributes' => $attributes,
                        'field_values' => $fieldValues,
                    ];
                });
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
