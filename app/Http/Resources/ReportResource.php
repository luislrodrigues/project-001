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
            'diagnosis' => $this->formatDiagnosis($this->diagnosis),
            'recommendations' => $this->formatRecommendations($this->recommendations),
            'components' => $this->equipment ? ($this->equipment->components ?? []) : [],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function formatDiagnosis($diagnosis)
    {
        if (is_string($diagnosis)) {
            $decoded = json_decode($diagnosis, true);
        } else {
            $decoded = $diagnosis;
        }

        if (!is_array($decoded)) {
            return [];
        }

        // Si es un array indexado numéricamente (array de arrays), necesitamos mapearlo a los IDs de componentes
        // Primero verificar si es un array indexado numéricamente
        $isNumericArray = array_keys($decoded) === range(0, count($decoded) - 1);
        
        // Formatear diagnosis para incluir URLs completas de imágenes
        $formatted = [];
        
        if ($isNumericArray && $this->equipment && $this->equipment->components) {
            // Si viene como array indexado, mapearlo a los IDs de componentes
            $components = $this->equipment->components;
            foreach ($decoded as $index => $data) {
                if (isset($components[$index])) {
                    $componentId = (string)$components[$index]->id;
                    if (is_array($data) && isset($data['images'])) {
                        // Estructura nueva con imágenes
                        $formatted[$componentId] = [
                            'text' => $data['text'] ?? '',
                            'images' => array_map(function ($path) {
                                // Si ya es una URL completa, no agregar asset
                                if (filter_var($path, FILTER_VALIDATE_URL)) {
                                    return $path;
                                }
                                return asset('storage/' . $path);
                            }, $data['images'] ?? [])
                        ];
                    } else if (is_string($data)) {
                        // Estructura antigua solo texto
                        $formatted[$componentId] = $data;
                    } else {
                        $formatted[$componentId] = $data;
                    }
                }
            }
        } else {
            // Es un objeto asociativo con claves de componente
            foreach ($decoded as $componentId => $data) {
                $componentIdStr = (string)$componentId;
                if (is_array($data) && isset($data['images'])) {
                    // Estructura nueva con imágenes
                    $formatted[$componentIdStr] = [
                        'text' => $data['text'] ?? '',
                        'images' => array_map(function ($path) {
                            // Si ya es una URL completa, no agregar asset
                            if (filter_var($path, FILTER_VALIDATE_URL)) {
                                return $path;
                            }
                            return asset('storage/' . $path);
                        }, $data['images'] ?? [])
                    ];
                } else if (is_string($data)) {
                    // Estructura antigua solo texto
                    $formatted[$componentIdStr] = $data;
                } else {
                    $formatted[$componentIdStr] = $data;
                }
            }
        }

        return $formatted;
    }

    private function formatRecommendations($recommendations)
    {
        if (is_string($recommendations)) {
            $decoded = json_decode($recommendations, true);
        } else {
            $decoded = $recommendations;
        }

        if (!is_array($decoded)) {
            return [];
        }

        // Si es un array indexado numéricamente (array de arrays), necesitamos mapearlo a los IDs de componentes
        $isNumericArray = array_keys($decoded) === range(0, count($decoded) - 1);
        
        $formatted = [];
        
        if ($isNumericArray && $this->equipment && $this->equipment->components) {
            // Si viene como array indexado, mapearlo a los IDs de componentes
            $components = $this->equipment->components;
            foreach ($decoded as $index => $data) {
                if (isset($components[$index])) {
                    $componentId = (string)$components[$index]->id;
                    $formatted[$componentId] = is_array($data) ? $data : [];
                }
            }
        } else {
            // Es un objeto asociativo con claves de componente
            foreach ($decoded as $componentId => $data) {
                $componentIdStr = (string)$componentId;
                $formatted[$componentIdStr] = is_array($data) ? $data : [];
            }
        }

        return $formatted;
    }
}
