<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComponentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit equipment');
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
