<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'equipment_id' => $this->equipment_id,
            'equipment' => $this->equipment ? [
                'id' => $this->equipment->id ?? null,
                'name' => $this->equipment->name ?? null,
                'code' => $this->equipment->code ?? null,
                'tag' => $this->equipment->tag ?? null,
                'equipment_type' => ($this->equipment->equipmentType && $this->equipment->equipmentType->name) ? $this->equipment->equipmentType->name : null,
                'location' => ($this->equipment->ubication && $this->equipment->ubication->name) ? $this->equipment->ubication->name : null,
                'process' => ($this->equipment->process && $this->equipment->process->name) ? $this->equipment->process->name : null,
                'company' => $this->equipment->company ? [
                    'id' => $this->equipment->company->id ?? null,
                    'name' => $this->equipment->company->name ?? null,
                ] : null,
            ] : null,
            'measurement_date' => $this->measurement_date?->format('Y-m-d'),
            'image_1' => $this->image_1 ? asset('storage/' . $this->image_1) : null,
            'image_2' => $this->image_2 ? asset('storage/' . $this->image_2) : null,
            'condition' => $this->condition ? [
                'id' => $this->condition->id ?? null,
                'name' => $this->condition->name ?? null,
            ] : null,
            'mechanism_fail' => $this->mechanismFail ? [
                'id' => $this->mechanismFail->id ?? null,
                'name' => $this->mechanismFail->name ?? null,
            ] : null,
            'component_fail' => $this->componentFail ? [
                'id' => $this->componentFail->id ?? null,
                'name' => $this->componentFail->name ?? null,
            ] : null,
            'priority' => $this->priority,
            'analist' => $this->analist ? [
                'id' => $this->analist->id ?? null,
                'name' => $this->analist->name ?? null,
            ] : null,
            'ot_created' => $this->ot_created,
            'status_ot' => $this->status_ot,
            'execution_date' => $this->execution_date?->format('Y-m-d'),
            'verification_recommendation' => $this->verification_recommendation,
            'diagnosis' => is_string($this->diagnosis) ? json_decode($this->diagnosis, true) : ($this->diagnosis ?? []),
            'recommendations' => is_array($this->recommendations) ? $this->recommendations : (is_string($this->recommendations) ? json_decode($this->recommendations, true) : []),
            'components' => $this->equipment ? ($this->equipment->components ?? []) : [],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
