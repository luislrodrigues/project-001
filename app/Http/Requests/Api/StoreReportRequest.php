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

        if ($this->has('general_recommendations') && is_string($this->general_recommendations)) {
            $decoded = json_decode($this->general_recommendations, true);
            $this->merge([
                'general_recommendations' => is_array($decoded) ? $decoded : []
            ]);
        }

        // En multipart, los parámetros de medición llegan como JSON string
        if ($this->has('measurement_values') && is_string($this->measurement_values)) {
            $decoded = json_decode($this->measurement_values, true);
            $this->merge([
                'measurement_values' => is_array($decoded) ? $decoded : []
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'equipment_id' => 'required|integer|exists:equipment,id',
            'report_type' => 'nullable|string|in:vibration,thermography',
            'measurement_values' => 'nullable|array',
            'measurement_values.*' => 'nullable|array',
            'measurement_values.*.*' => 'nullable',
            'measurement_date' => 'required|date',
            'condition_id' => 'required|integer|exists:conditions,id',
            'condition_override_reason' => 'nullable|string|max:500',
            'mechanism_fail_id' => 'required|integer|exists:mechanism_fails,id',
            'component_fail_id' => 'required|integer|exists:component_fails,id',
            'priority_id' => 'required|integer|exists:priorities,id',
            'analist_id' => 'required|integer|exists:users,id',
            'ot_created' => 'nullable|string|max:255',
            'status_ot' => 'nullable|string|max:255',
            'execution_date' => 'nullable|date',
            'general_recommendations' => 'nullable|array',
            'general_recommendations.*' => 'nullable|string|max:65535',
            'diagnosis' => 'nullable|array',
            'recommendations' => 'nullable|array',
        ];
    }
}
