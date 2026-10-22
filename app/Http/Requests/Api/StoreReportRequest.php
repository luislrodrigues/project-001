<?php

namespace App\Http\Requests\Api;

use App\Models\Equipment;
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

        $this->resolveReportType();
    }

    /**
     * El tipo de informe puede venir del formulario o del equipo seleccionado;
     * se resuelve antes de validar porque las reglas dependen de él.
     */
    private function resolveReportType(): void
    {
        $reportType = $this->input('report_type');

        if (!is_string($reportType) || !in_array($reportType, ['vibration', 'thermography'], true)) {
            $reportType = $this->filled('equipment_id')
                ? Equipment::whereKey((int) $this->input('equipment_id'))->value('report_type')
                : null;
        }

        if ($reportType !== null) {
            $this->merge(['report_type' => $reportType]);
        }
    }

    public function rules(): array
    {
        $isThermography = $this->input('report_type') === 'thermography';

        return [
            'equipment_id' => 'required|integer|exists:equipment,id',
            'report_type' => 'nullable|string|in:vibration,thermography',
            'measurement_values' => 'nullable|array',
            'measurement_values.*' => 'nullable|array',
            'measurement_values.*.*' => 'nullable',
            'measurement_date' => 'required|date',
            // En termografía la condición, el mecanismo y la prioridad se capturan
            // por punto y la cabecera se deriva del punto más crítico.
            'condition_id' => ($isThermography ? 'nullable' : 'required') . '|integer|exists:conditions,id',
            'condition_override_reason' => 'nullable|string|max:500',
            'mechanism_fail_id' => ($isThermography ? 'nullable' : 'required') . '|integer|exists:mechanism_fails,id',
            'component_fail_id' => ($isThermography ? 'nullable' : 'required') . '|integer|exists:component_fails,id',
            'priority_id' => ($isThermography ? 'nullable' : 'required') . '|integer|exists:priorities,id',
            'emissivity' => ($isThermography ? 'required' : 'nullable') . '|numeric|between:0,1',
            'distance_m' => ($isThermography ? 'required' : 'nullable') . '|numeric|between:0,9999',
            'ambient_temp' => ($isThermography ? 'required' : 'nullable') . '|numeric|between:-50,200',
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
