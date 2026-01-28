<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComponentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $fieldValues = [];
        foreach ($this->fieldValues as $value) {
            $fieldValues[$value->component_type_field_id] = [
                'id' => $value->id,
                'field_id' => $value->component_type_field_id,
                'value' => $value->value,
                'option_id' => $value->option_id,
            ];
        }

        return [
            'id' => $this->id,
            'equipment_id' => $this->equipment_id,
            'equipment' => [
                'id' => $this->equipment->id ?? null,
                'name' => $this->equipment->name ?? null,
            ],
            'component_type_id' => $this->component_type_id,
            'component_type' => [
                'id' => $this->componentType->id ?? null,
                'name' => $this->componentType->name ?? null,
            ],
            'brand_id' => $this->brand_id,
            'brand' => [
                'id' => $this->brand->id ?? null,
                'name' => $this->brand->name ?? null,
            ],
            'field_values' => $fieldValues,
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
