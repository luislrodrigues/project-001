<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComponentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit equipment');
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('field_values') && is_string($this->field_values)) {
            $this->merge([
                'field_values' => json_decode($this->field_values, true) ?? [],
            ]);
        }
    }

    public function rules(): array
    {
        $rules = [
            'equipment_id' => 'required|integer|exists:equipment,id',
            'component_type_id' => 'required|integer|exists:component_types,id',
            'brand_id' => 'required|integer|exists:brands,id',
            'field_values' => 'nullable|array',
        ];

        return $rules;
    }
}
