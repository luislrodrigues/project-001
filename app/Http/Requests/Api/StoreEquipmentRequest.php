<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('save equipment');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'tag' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'company_id' => 'required|integer|exists:companies,id',
            'process_id' => 'required|integer|exists:processes,id',
            'ubication_id' => 'required|integer|exists:ubications,id',
            'equipment_type_id' => 'required|integer|exists:equipment_types,id',
        ];
    }
}
