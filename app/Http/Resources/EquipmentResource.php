<?php

namespace App\Http\Resources;

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
                    if ($component->relationLoaded('fieldValues')) {
                        foreach ($component->fieldValues as $fv) {
                            $attributes[] = [
                                'name' => $fv->componentTypeField->name ?? '',
                                'value' => $fv->display_value ?? $fv->value ?? '',
                                'unit' => $fv->componentTypeField->unit ?? '',
                            ];
                        }
                    }
                    return [
                        'id' => $component->id,
                        'component_type' => [
                            'id' => $component->componentType->id ?? null,
                            'name' => $component->componentType->name ?? null,
                        ],
                        'brand' => [
                            'id' => $component->brand->id ?? null,
                            'name' => $component->brand->name ?? null,
                        ],
                        'attributes' => $attributes,
                    ];
                });
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
