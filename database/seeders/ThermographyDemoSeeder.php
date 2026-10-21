<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\ComponentFail;
use App\Models\ComponentType;
use App\Models\ComponentTypeField;
use App\Models\ComponentTypeValue;
use App\Models\Condition;
use App\Models\Equipment;
use App\Models\EquipmentType;
use App\Models\MechanismFail;
use App\Models\Priority;
use App\Models\Process;
use App\Models\Report;
use App\Models\Ubication;
use Illuminate\Database\Seeder;

/**
 * Datos de demostración de termografía (Cargill Caribe - Sub-Estación # 2).
 *
 * Es idempotente: se puede ejecutar varias veces sin duplicar registros.
 * Solo crea registros nuevos; no modifica equipos, reportes ni catálogos existentes.
 *
 * Ejecutar con: php artisan db:seed --class=ThermographyDemoSeeder
 */
class ThermographyDemoSeeder extends Seeder
{
    private const REPORT_TYPE = 'thermography';
    private const COMPANY_ID = 2;   // Cargill Caribe
    private const ANALIST_ID = 2;   // Álvaro Sánchez Maldonado
    private const PROCESS = 'Utilidades';
    private const UBICATION = 'Sub-Estación # 2';

    /** Campos que se capturan una vez, en el componente. */
    private const COMPONENT_FIELDS = [];

    /** Campos que se capturan en cada medición (reporte). */
    private const MEASUREMENT_FIELDS = [
        ['name' => 'Delta T°', 'type' => 'numeric', 'scope' => 'measurement', 'unit' => '°C'],
        ['name' => 'Emisividad', 'type' => 'numeric', 'scope' => 'measurement', 'unit' => null],
        ['name' => 'Distancia', 'type' => 'numeric', 'scope' => 'measurement', 'unit' => 'm'],
        ['name' => 'Temp. Ambiente', 'type' => 'numeric', 'scope' => 'measurement', 'unit' => '°C'],
    ];

    /** Tipos de equipo que se agregan al catálogo (solo si no existen). */
    private const EQUIPMENT_TYPES = ['Tablero Eléctrico', 'Banco de Condensadores'];

    /** Mecanismos de falla que se agregan al catálogo (solo si no existen). */
    private const MECHANISM_FAILS = ['Sin Identificar', 'Conexión Defectuosa', 'Falla de Condensador'];

    /** Componentes en falla que se agregan al catálogo (solo si no existen). */
    private const COMPONENT_FAILS = ['Conexión Eléctrica', 'Interruptor Principal', 'Condensador'];

    /** Tipos de componente de termografía: nombre => campos adicionales al estándar. */
    private const COMPONENT_TYPES = [
        'Barraje de Entrada' => [],
        'Breaker Principal' => [],
        'Totalizador Principal' => [],
        'Breaker Hidrolavadoras' => [],
        'Condensadores' => [
            ['name' => 'Capacidad', 'type' => 'numeric', 'scope' => 'component', 'unit' => 'kVAr'],
        ],
    ];

    private const ASSETS = [
        [
            'name' => 'Tablero Transferencia y Distribución',
            'code' => '200-TDG-SE2',
            'tag' => '200-TDG-SE2 (ATS)',
            'description' => 'Tablero de transferencia y distribución del barraje de entrada al totalizador principal.',
            'equipment_type' => 'Tablero Eléctrico',
            'component_type' => 'Barraje de Entrada',
            'brand' => 'Sin Identificar',
            'component_values' => [],
            'reports' => [
                [
                    'measurement_date' => '2026-02-20',
                    'condition' => 'Normal',
                    'priority' => 'Baja',
                    'mechanism_fail' => 'No Aplica',
                    'component_fail' => 'No Aplica',
                    'values' => ['Delta T°' => 2.5, 'Temp. Ambiente' => 27, 'Emisividad' => 0.92, 'Distancia' => 1],
                    'diagnosis' => 'No se observan deltas de temperatura fuera de rango. En la inspección visual tampoco se visualizan condiciones anormales en el activo.',
                    'recommendations' => ['Continuar con plan de inspección visual y rutinas de termografía.'],
                ],
                [
                    'measurement_date' => '2026-04-15',
                    'condition' => 'Normal',
                    'priority' => 'Baja',
                    'mechanism_fail' => 'No Aplica',
                    'component_fail' => 'No Aplica',
                    'values' => ['Delta T°' => 1.8, 'Temp. Ambiente' => 29, 'Emisividad' => 0.92, 'Distancia' => 1],
                    'diagnosis' => 'Comportamiento térmico estable respecto a la medición anterior. Delta de temperatura dentro del rango normal.',
                    'recommendations' => ['Mantener frecuencia de inspección semestral.'],
                ],
            ],
        ],
        [
            'name' => 'Compresor 4',
            'code' => '207-COMPRESOR-4',
            'tag' => '207-COMPRESOR-4',
            'description' => 'Breaker principal del compresor 4, área de utilidades.',
            'equipment_type' => 'Compresor de Amoníaco',
            'component_type' => 'Breaker Principal',
            'brand' => 'Siemens',
            'component_values' => [],
            'reports' => [
                [
                    'measurement_date' => '2026-02-20',
                    'condition' => 'Normal',
                    'priority' => 'Baja',
                    'mechanism_fail' => 'No Aplica',
                    'component_fail' => 'No Aplica',
                    'values' => ['Delta T°' => 0.01, 'Temp. Ambiente' => 28, 'Emisividad' => 0.92, 'Distancia' => 1],
                    'diagnosis' => 'Se observa un delta de temperatura de 0,01 °C, catalogado en condición NORMAL de acuerdo con la norma. En la inspección visual no se evidencian condiciones anormales en el activo.',
                    'recommendations' => ['Continuar con plan de mantenimiento predictivo.'],
                ],
                [
                    'measurement_date' => '2026-04-15',
                    'condition' => 'Normal',
                    'priority' => 'Baja',
                    'mechanism_fail' => 'No Aplica',
                    'component_fail' => 'No Aplica',
                    'values' => ['Delta T°' => 0.9, 'Temp. Ambiente' => 30, 'Emisividad' => 0.92, 'Distancia' => 1],
                    'diagnosis' => 'Breaker principal sin incrementos de temperatura relevantes. Conexiones y barraje en buen estado.',
                    'recommendations' => ['Continuar con plan de mantenimiento predictivo.'],
                ],
            ],
        ],
        [
            'name' => 'Tablero Eléctrico General 1 - 440V/4.000 AMP',
            'code' => '210-TABLERO STORK',
            'tag' => '210-TABLERO STORK',
            'description' => 'Tablero eléctrico general 1, totalizador principal del tablero Stork.',
            'equipment_type' => 'Tablero Eléctrico',
            'component_type' => 'Totalizador Principal',
            'brand' => 'Sin Identificar',
            'component_values' => [],
            'reports' => [
                [
                    'measurement_date' => '2026-02-20',
                    'condition' => 'Alarma',
                    'priority' => 'Media',
                    'mechanism_fail' => 'Sin Identificar',
                    'component_fail' => 'Conexión Eléctrica',
                    'values' => ['Delta T°' => 5.1, 'Temp. Ambiente' => 28, 'Emisividad' => 0.92, 'Distancia' => 1],
                    'diagnosis' => 'Se observa un delta de temperatura de 5,1 °C, catalogado en condición de ALARMA de acuerdo con la norma, con mayor temperatura en la fase B (Sp2) a la salida del interruptor principal. En la inspección visual se evidencian focos de sulfatación y contaminación del barraje principal.',
                    'recommendations' => [
                        'Realizar mantenimiento preventivo al barraje: limpiar con desengrasante, pulir y retirar contaminación en uniones de conexiones. Ajustar tornillería con el torque recomendado.',
                        'Realizar seguimiento de variables operativas y evaluar temperatura en diferentes escenarios de carga.',
                        'Continuar con plan de mantenimiento predictivo.',
                    ],
                ],
                [
                    'measurement_date' => '2026-04-15',
                    'condition' => 'Normal',
                    'priority' => 'Baja',
                    'mechanism_fail' => 'No Aplica',
                    'component_fail' => 'No Aplica',
                    'values' => ['Delta T°' => 2.2, 'Temp. Ambiente' => 29, 'Emisividad' => 0.92, 'Distancia' => 1],
                    'diagnosis' => 'Luego del mantenimiento preventivo ejecutado en el barraje (limpieza y ajuste de tornillería) el delta de temperatura bajó a 2,2 °C, condición NORMAL de acuerdo con la norma. No se evidencian focos de sulfatación.',
                    'recommendations' => ['Continuar con plan de mantenimiento predictivo y verificar torque en la próxima parada.'],
                ],
            ],
        ],
        [
            'name' => 'BC-Sala Máquinas-160 kVAr',
            'code' => '101.5-BC.SALA MAQ',
            'tag' => '101.5-BC.SALA MÁQUINAS-160 KVAr',
            'description' => 'Banco de condensadores de la sala de máquinas, 160 kVAr.',
            'equipment_type' => 'Banco de Condensadores',
            'component_type' => 'Condensadores',
            'brand' => 'Sin Identificar',
            'component_values' => ['Capacidad' => 160],
            'reports' => [
                [
                    'measurement_date' => '2026-02-20',
                    'condition' => 'Alarma',
                    'priority' => 'Media',
                    'mechanism_fail' => 'Falla de Condensador',
                    'component_fail' => 'Condensador',
                    'values' => ['Temp. Ambiente' => 28, 'Emisividad' => 0.92, 'Distancia' => 1],
                    'diagnosis' => 'No se registra delta de temperatura (el banco se encontraba fuera de operación). En la inspección visual se evidencia condensador soplado asociado a falla del condensador y rastros de fluido sobre la superficie de soporte.',
                    'recommendations' => [
                        'Realizar el cambio del condensador en falla y el ajuste de conexiones eléctricas.',
                        'Validar condiciones de carga acorde al contexto operacional.',
                        'Continuar con plan de mantenimiento predictivo.',
                    ],
                ],
                [
                    'measurement_date' => '2026-04-15',
                    'condition' => 'Normal',
                    'priority' => 'Baja',
                    'mechanism_fail' => 'No Aplica',
                    'component_fail' => 'No Aplica',
                    'values' => ['Delta T°' => 0.8, 'Temp. Ambiente' => 29, 'Emisividad' => 0.92, 'Distancia' => 1],
                    'diagnosis' => 'Condensador reemplazado; el banco opera con delta de temperatura de 0,8 °C, condición NORMAL de acuerdo con la norma. No se evidencian rastros de fluido ni deformaciones.',
                    'recommendations' => ['Continuar con plan de mantenimiento predictivo.'],
                ],
            ],
        ],
        [
            'name' => 'Tablero Distribución 220V - 1.000 AMP',
            'code' => '205-TGD220V',
            'tag' => '205-TGD220V',
            'description' => 'Breaker del tablero 1 de hidrolavadoras, tablero de distribución 220V.',
            'equipment_type' => 'Tablero Eléctrico',
            'component_type' => 'Breaker Hidrolavadoras',
            'brand' => 'Sin Identificar',
            'component_values' => [],
            'reports' => [
                [
                    'measurement_date' => '2026-02-20',
                    'condition' => 'Peligro',
                    'priority' => 'Alta',
                    'mechanism_fail' => 'Conexión Defectuosa',
                    'component_fail' => 'Interruptor Principal',
                    'values' => ['Delta T°' => 14.2, 'Temp. Ambiente' => 28, 'Emisividad' => 0.92, 'Distancia' => 1],
                    'diagnosis' => 'Se observa un delta de temperatura de 14,2 °C, catalogado en condición de PELIGRO de acuerdo con la norma, con mayor temperatura en la fase B (negra) Sp2 en la conexión de salida del breaker principal de las hidrolavadoras. En la inspección visual NO se evidencian condiciones anormales en la conexión y el breaker; se evidencia contaminación general del barraje principal de conexiones.',
                    'recommendations' => [
                        'Realizar inspección y ajuste de tornillería en las conexiones de salida del breaker.',
                        'Realizar limpieza general del tablero como mantenimiento preventivo. Retirar contaminación.',
                        'Validar condiciones de carga acorde al contexto operacional.',
                        'Continuar con plan de mantenimiento predictivo.',
                    ],
                ],
                [
                    'measurement_date' => '2026-04-15',
                    'condition' => 'Alarma',
                    'priority' => 'Media',
                    'mechanism_fail' => 'Conexión Defectuosa',
                    'component_fail' => 'Interruptor Principal',
                    'values' => ['Delta T°' => 6.3, 'Temp. Ambiente' => 30, 'Emisividad' => 0.92, 'Distancia' => 1],
                    'diagnosis' => 'Después del ajuste de tornillería y la limpieza del tablero el delta de temperatura bajó de 14,2 °C a 6,3 °C, condición de ALARMA de acuerdo con la norma. Continúa el incremento en la fase B (negra) Sp2 a la salida del breaker.',
                    'recommendations' => [
                        'Programar reapriete de las conexiones de salida del breaker con torquímetro en la próxima parada.',
                        'Mantener seguimiento de temperatura en cada campaña de termografía.',
                    ],
                ],
            ],
        ],
        [
            'name' => 'Tablero Distribución Sub-Estación 2 - 1.250 AMP/460V',
            'code' => 'SE2-1250A',
            'tag' => 'SE2-1250A / 1.250 AMP - 460V',
            'description' => 'Breaker principal del tablero de distribución de la sub-estación # 2.',
            'equipment_type' => 'Tablero Eléctrico',
            'component_type' => 'Breaker Principal',
            'brand' => 'Sin Identificar',
            'component_values' => [],
            'reports' => [
                [
                    'measurement_date' => '2026-02-20',
                    'condition' => 'Alarma',
                    'priority' => 'Media',
                    'mechanism_fail' => 'Sin Identificar',
                    'component_fail' => 'Interruptor Principal',
                    'values' => ['Delta T°' => 8.1, 'Temp. Ambiente' => 28, 'Emisividad' => 0.92, 'Distancia' => 1],
                    'diagnosis' => 'Se observa un delta de temperatura de 8,1 °C, catalogado en condición de ALARMA de acuerdo con la norma, registrando mayor temperatura en las fases A y B a la salida del breaker principal. En la inspección visual no se evidencian condiciones anormales.',
                    'recommendations' => [
                        'Realizar inspección y ajuste de tornillería en las conexiones del breaker principal.',
                        'Validar condiciones de carga acorde al contexto operacional.',
                    ],
                ],
                [
                    'measurement_date' => '2026-04-15',
                    'condition' => 'Alarma',
                    'priority' => 'Media',
                    'mechanism_fail' => 'Sin Identificar',
                    'component_fail' => 'Interruptor Principal',
                    'values' => ['Delta T°' => 9.4, 'Temp. Ambiente' => 30, 'Emisividad' => 0.92, 'Distancia' => 1],
                    'diagnosis' => 'El delta de temperatura se mantiene en condición de ALARMA (9,4 °C) con una leve tendencia al aumento respecto a la campaña anterior. Mayor temperatura en las fases A y B a la salida del breaker principal.',
                    'recommendations' => [
                        'Programar intervención del breaker principal y verificar ajuste de conexiones.',
                        'Realizar seguimiento mensual de temperatura hasta normalizar la condición.',
                    ],
                    'general_recommendations' => [
                        'Continuar con el plan de inspección visual y las rutinas de termografía del área de utilidades.',
                        'Validar las condiciones de carga de la sub-estación # 2 en los escenarios de máxima demanda.',
                    ],
                ],
            ],
        ],
    ];

    public function run(): void
    {
        $process = Process::firstOrCreate(['name' => self::PROCESS], ['status' => 'active']);
        $ubication = Ubication::firstOrCreate(['name' => self::UBICATION], ['status' => 'active']);

        $equipmentTypes = [];
        foreach (self::EQUIPMENT_TYPES as $name) {
            $equipmentTypes[$name] = EquipmentType::firstOrCreate(['name' => $name], ['status' => 'active']);
        }
        // Tipos que ya existían en el catálogo y se reutilizan
        $equipmentTypes['Compresor de Amoníaco'] = EquipmentType::where('name', 'Compresor de Amoníaco')->first();

        $mechanismFails = [];
        foreach (self::MECHANISM_FAILS as $name) {
            $mechanismFails[$name] = MechanismFail::firstOrCreate(['name' => $name], ['status' => 'active']);
        }
        $mechanismFails['No Aplica'] = MechanismFail::where('name', 'No Aplica')->first();

        $componentFails = [];
        foreach (self::COMPONENT_FAILS as $name) {
            $componentFails[$name] = ComponentFail::firstOrCreate(['name' => $name], ['status' => 'active']);
        }
        $componentFails['No Aplica'] = ComponentFail::where('name', 'No Aplica')->first();

        $conditions = Condition::pluck('id', 'name');
        $priorities = Priority::pluck('id', 'name');
        $brands = Brand::pluck('id', 'name');

        $created = ['component_types' => 0, 'equipments' => 0, 'reports' => 0];

        foreach (self::ASSETS as $asset) {
            $componentType = ComponentType::firstOrCreate(
                ['name' => $asset['component_type'], 'report_type' => self::REPORT_TYPE],
                ['status' => 'active']
            );
            if ($componentType->wasRecentlyCreated) {
                $created['component_types']++;
                $this->createFields($componentType);
            }

            $equipment = Equipment::firstOrCreate(
                ['name' => $asset['name'], 'report_type' => self::REPORT_TYPE],
                [
                    'code' => $asset['code'],
                    'tag' => $asset['tag'],
                    'description' => $asset['description'],
                    'company_id' => self::COMPANY_ID,
                    'process_id' => $process->id,
                    'ubication_id' => $ubication->id,
                    'equipment_type_id' => $equipmentTypes[$asset['equipment_type']]->id ?? null,
                    'status' => 'active',
                ]
            );

            $component = $equipment->components()->firstOrCreate(
                ['component_type_id' => $componentType->id],
                [
                    'brand_id' => $brands[$asset['brand']] ?? null,
                    'display_order' => 1,
                    'status' => 'active',
                ]
            );

            if ($equipment->wasRecentlyCreated) {
                $created['equipments']++;
            }

            $this->syncComponentValues($component->id, $componentType, $asset['component_values']);

            foreach ($asset['reports'] as $reportData) {
                $exists = Report::where('equipment_id', $equipment->id)
                    ->where('measurement_date', $reportData['measurement_date'])
                    ->where('report_type', self::REPORT_TYPE)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $report = Report::create([
                    'equipment_id' => $equipment->id,
                    'report_type' => self::REPORT_TYPE,
                    'measurement_date' => $reportData['measurement_date'],
                    'condition_id' => $conditions[$reportData['condition']] ?? null,
                    'priority_id' => $priorities[$reportData['priority']] ?? null,
                    'mechanism_fail_id' => $mechanismFails[$reportData['mechanism_fail']]->id ?? null,
                    'component_fail_id' => $componentFails[$reportData['component_fail']]->id ?? null,
                    'diagnosis' => [(string) $component->id => ['text' => $reportData['diagnosis']]],
                    'recommendations' => [(string) $component->id => $reportData['recommendations']],
                    'general_recommendations' => $reportData['general_recommendations'] ?? [],
                    'analist_id' => self::ANALIST_ID,
                    'status' => 'active',
                ]);

                $this->syncMeasurementValues($report, $component->id, $componentType, $reportData['values']);
                $created['reports']++;
            }
        }

        $this->command?->info(sprintf(
            'Termografía (demo): %d tipos de componente, %d equipos, %d reportes creados.',
            $created['component_types'],
            $created['equipments'],
            $created['reports']
        ));
    }

    /**
     * Campos de un tipo de componente de termografía: los del componente y los de la medición.
     */
    private function createFields(ComponentType $componentType): void
    {
        $extra = self::COMPONENT_TYPES[$componentType->name] ?? [];
        $fields = array_merge(self::COMPONENT_FIELDS, $extra, self::MEASUREMENT_FIELDS);

        foreach ($fields as $index => $field) {
            $componentType->componentTypeFields()->create([
                'name' => $field['name'],
                'type' => $field['type'],
                'scope' => $field['scope'],
                'unit' => $field['unit'],
                'display_order' => $index + 1,
                'is_required' => false,
                'is_searchable' => false,
                'status' => 'active',
            ]);
        }
    }

    /**
     * Valores de los campos de ámbito componente (emisividad, distancia, capacidad...).
     */
    private function syncComponentValues(int $componentId, ComponentType $componentType, array $values): void
    {
        $fields = ComponentTypeField::where('component_type_id', $componentType->id)
            ->where('scope', 'component')
            ->pluck('id', 'name');

        foreach ($values as $name => $value) {
            if (!isset($fields[$name])) {
                continue;
            }

            ComponentTypeValue::firstOrCreate(
                ['component_id' => $componentId, 'component_type_field_id' => $fields[$name]],
                ['value' => (string) $value, 'raw_value' => (string) $value, 'status' => 'active']
            );
        }
    }

    /**
     * Valores capturados en la medición (delta T°, emisividad, distancia, temperatura ambiente).
     */
    private function syncMeasurementValues(Report $report, int $componentId, ComponentType $componentType, array $values): void
    {
        $fields = ComponentTypeField::where('component_type_id', $componentType->id)
            ->where('scope', 'measurement')
            ->pluck('id', 'name');

        foreach ($values as $name => $value) {
            if (!isset($fields[$name])) {
                continue;
            }

            $report->measurementValues()->create([
                'component_id' => $componentId,
                'component_type_field_id' => $fields[$name],
                'value' => (string) $value,
                'status' => 'active',
            ]);
        }
    }
}
