<?php

namespace Database\Seeders;

use App\Models\ReportType;
use Illuminate\Database\Seeder;

class ReportTypeSeeder extends Seeder
{
    /**
     * Los códigos deben coincidir con los valores ya almacenados en
     * reports.report_type, equipment.report_type y component_types.report_type.
     */
    public function run(): void
    {
        $types = [
            [
                'code' => 'vibration',
                'name' => 'Vibraciones',
                'consecutivo_prefix' => 'SS',
                'icon' => 'fas fa-wave-square',
                'color' => '#3b82f6',
                'pdf_view' => 'reports.report-pdf',
                'display_order' => 1,
            ],
            [
                'code' => 'thermography',
                'name' => 'Termografía',
                'consecutivo_prefix' => 'TH',
                'icon' => 'fas fa-thermometer-half',
                'color' => '#f43f5e',
                'pdf_view' => 'reports.thermography-pdf',
                'display_order' => 2,
            ],
        ];

        foreach ($types as $data) {
            $type = ReportType::withTrashed()->firstOrNew(['code' => $data['code']]);
            $type->fill($data + ['status' => 'active']);
            $type->deleted_at = null;
            $type->save();
        }
    }
}
