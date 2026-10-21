<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreComponentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('save equipment');
    }

    public function rules(): array
    {
        return [
            'report_type' => ['nullable', 'string', 'in:vibration,thermography'],
            'name' => ['required', 'string', 'max:255'],
            'fields' => ['required', 'array', 'min:1'],
            'fields.*.name' => ['required', 'string', 'max:255'],
            'fields.*.type' => ['required', 'string', 'in:text,number,numeric,textarea,select,multiselect,date,datetime,boolean'],
            'fields.*.scope' => ['nullable', 'string', 'in:component,measurement'],
            'fields.*.unit' => ['nullable', 'string', 'max:50'],
            'fields.*.is_required' => ['nullable', 'boolean'],
            'fields.*.options' => ['nullable', 'array'],
            'fields.*.options.*.label' => ['required_with:fields.*.options', 'string', 'max:255'],
            'fields.*.options.*.value' => ['required_with:fields.*.options', 'string', 'max:255'],
            'fields.*.options.*.is_default' => ['nullable', 'boolean'],
            'fields.*.options.*.display_order' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'fields.required' => 'Debes agregar al menos un campo.',
            'fields.min' => 'Debes agregar al menos un campo.',
            'fields.*.name.required' => 'El nombre del campo es requerido.',
            'fields.*.type.required' => 'El tipo del campo es requerido.',
            'fields.*.options.*.label.required_with' => 'El label de la opción es requerido.',
            'fields.*.options.*.value.required_with' => 'El valor de la opción es requerido.',
        ];
    }
}
