<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->display_code,
            'report_type' => $this->report_type,
            'title' => $this->title,
            'subject' => $this->subject,
            'period' => $this->period,
            'service_date' => $this->service_date?->format('Y-m-d'),
            'summary' => $this->summary,
            'company' => $this->company ? [
                'id' => $this->company->id,
                'name' => $this->company->name,
            ] : null,
            'process' => $this->process ? [
                'id' => $this->process->id,
                'name' => $this->process->name,
            ] : null,
            'ubication' => $this->ubication ? [
                'id' => $this->ubication->id,
                'name' => $this->ubication->name,
            ] : null,
            'responsible_name' => $this->responsible_name,
            'responsible_position' => $this->responsible_position,
            'responsible_certification' => $this->responsible_certification,
            'status' => $this->status,
            'reports_count' => $this->whenCounted('reports'),
            'reports' => $this->whenLoaded('reports', fn () => $this->reports->map(fn ($report) => [
                'id' => $report->id,
                'consecutivo' => $report->consecutivo,
                'measurement_date' => $report->measurement_date?->format('Y-m-d'),
                'equipment' => $report->equipment ? [
                    'id' => $report->equipment->id,
                    'name' => $report->equipment->name,
                    'code' => $report->equipment->code,
                ] : null,
                'condition' => $report->condition ? [
                    'id' => $report->condition->id,
                    'name' => $report->condition->name,
                ] : null,
                'measurement_values' => $report->relationLoaded('measurementValues')
                    ? $report->measurementValuesList()
                    : [],
            ])->values()),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
