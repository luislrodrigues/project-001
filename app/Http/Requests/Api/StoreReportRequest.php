<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('save report');
    }

    protected function prepareForValidation(): void
    {
        // Convertir strings JSON a arrays si vienen como strings
        if ($this->has('diagnosis') && is_string($this->diagnosis)) {
            $this->merge([
                'diagnosis' => json_decode($this->diagnosis, true) ?? []
            ]);
        }

        if ($this->has('recommendations') && is_string($this->recommendations)) {
            $this->merge([
                'recommendations' => json_decode($this->recommendations, true) ?? []
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'equipment_id' => 'required|integer|exists:equipment,id',
            'measurement_date' => 'required|date',
            'image_1' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_2' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'condition_id' => 'required|integer|exists:conditions,id',
            'mechanism_fail_id' => 'required|integer|exists:mechanism_fails,id',
            'component_fail_id' => 'required|integer|exists:component_fails,id',
            'priority' => 'required|in:Alta,Media,Baja',
            'analist_id' => 'required|integer|exists:users,id',
            'ot_created' => 'nullable|string|max:255',
            'status_ot' => 'nullable|string|max:255',
            'execution_date' => 'nullable|date',
            'verification_recommendation' => 'nullable|string',
            'diagnosis' => 'nullable|array',
            'recommendations' => 'nullable|array',
        ];
    }
}
