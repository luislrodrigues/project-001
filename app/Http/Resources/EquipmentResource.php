<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'tag' => $this->tag,
            'description' => $this->description,
            'status' => $this->status,
            'company' => [
                'id' => $this->company->id ?? null,
                'name' => $this->company->name ?? null,
            ],
            'equipment_type' => [
                'id' => $this->equipmentType->id ?? null,
                'name' => $this->equipmentType->name ?? null,
            ],
            'process' => [
                'id' => $this->process->id ?? null,
                'name' => $this->process->name ?? null,
            ],
            'ubication' => [
                'id' => $this->ubication->id ?? null,
                'name' => $this->ubication->name ?? null,
            ],
            'components' => $this->whenLoaded('components', function () {
                return $this->components->map(function ($component) {
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
                    ];
                });
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
