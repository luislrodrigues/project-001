<?php

namespace App\Support;

use App\Models\ComponentType;
use App\Models\ComponentTypeField;
use App\Models\ComponentTypeFieldOption;
use App\Models\Condition;
use App\Models\MechanismFail;
use App\Models\Priority;

/**
 * Campos por punto que todo tipo de componente de termografía debe tener.
 *
 * Se aplican automáticamente al crear un tipo nuevo y mediante el seeder
 * ThermographyFieldTemplateSeeder para los tipos existentes. El analista no
 * tiene que crearlos a mano.
 */
class ThermographyFieldTemplate
{
    public const REPORT_TYPE = 'thermography';

    /**
     * Definición de los campos que se capturan en cada informe, por componente.
     *
     * @return array<int, array{name: string, slug: string, type: string, unit: ?string, is_required: bool, display_order: int, source: ?string}>
     */
    public static function fields(): array
    {
        return [
            [
                'name' => 'Condición',
                'slug' => 'condicion',
                'type' => 'select',
                'unit' => null,
                'is_required' => true,
                'display_order' => 1,
                'source' => 'conditions',
            ],
            [
                'name' => 'Mecanismo de Falla',
                'slug' => 'mecanismo-falla',
                'type' => 'select',
                'unit' => null,
                'is_required' => true,
                'display_order' => 2,
                'source' => 'mechanism_fails',
            ],
            [
                'name' => 'Delta Temperatura',
                'slug' => 'delta-t',
                'type' => 'numeric',
                'unit' => '°C',
                'is_required' => true,
                'display_order' => 3,
                'source' => null,
            ],
            [
                'name' => 'Prioridad',
                'slug' => 'prioridad',
                'type' => 'select',
                'unit' => null,
                'is_required' => true,
                'display_order' => 4,
                'source' => 'priorities',
            ],
            [
                'name' => 'Imagen Térmica',
                'slug' => 'imagen-termica',
                'type' => 'numeric',
                'unit' => null,
                'is_required' => false,
                'display_order' => 5,
                'source' => null,
            ],
        ];
    }

    /**
     * Campos que dejaron de ser por punto: ahora son datos generales del informe.
     */
    public static function deprecatedSlugs(): array
    {
        return ['emisividad', 'distancia', 'temp-ambiente'];
    }

    /**
     * Aplica la plantilla a un tipo de componente. Idempotente.
     *
     * @return array{created: int, updated: int, removed: int}
     */
    public static function applyTo(ComponentType $componentType): array
    {
        $created = 0;
        $updated = 0;
        $removed = 0;

        foreach (self::fields() as $definition) {
            $field = ComponentTypeField::withTrashed()
                ->where('component_type_id', $componentType->id)
                ->where('slug', $definition['slug'])
                ->first();

            if (!$field) {
                // Puede existir con un slug autogenerado distinto (p. ej. "mecanismo-de-falla").
                $field = ComponentTypeField::withTrashed()
                    ->where('component_type_id', $componentType->id)
                    ->where('name', $definition['name'])
                    ->first();
            }

            $attributes = [
                'name' => $definition['name'],
                'slug' => $definition['slug'],
                'type' => $definition['type'],
                'scope' => 'measurement',
                'unit' => $definition['unit'],
                'display_order' => $definition['display_order'],
                'is_required' => $definition['is_required'],
                'status' => 'active',
            ];

            if ($field) {
                if ($field->trashed()) {
                    $field->restore();
                }
                $field->fill($attributes)->save();
                $updated++;
            } else {
                $field = ComponentTypeField::create($attributes + [
                    'component_type_id' => $componentType->id,
                ]);
                $created++;
            }

            if ($definition['source'] !== null) {
                self::syncOptions($field, $definition['source']);
            }
        }

        foreach (self::deprecatedSlugs() as $slug) {
            $fields = ComponentTypeField::where('component_type_id', $componentType->id)
                ->where('slug', $slug)
                ->get();

            foreach ($fields as $field) {
                $field->delete();
                $removed++;
            }
        }

        return ['created' => $created, 'updated' => $updated, 'removed' => $removed];
    }

    /**
     * @return array{types: int, created: int, updated: int, removed: int}
     */
    public static function applyToAll(): array
    {
        $totals = ['types' => 0, 'created' => 0, 'updated' => 0, 'removed' => 0];

        $types = ComponentType::where('report_type', self::REPORT_TYPE)->get();

        foreach ($types as $componentType) {
            $result = self::applyTo($componentType);
            $totals['types']++;
            $totals['created'] += $result['created'];
            $totals['updated'] += $result['updated'];
            $totals['removed'] += $result['removed'];
        }

        return $totals;
    }

    /**
     * Copia las opciones del catálogo al campo, manteniéndolas sincronizadas.
     */
    private static function syncOptions(ComponentTypeField $field, string $source): void
    {
        $catalog = self::catalogOptions($source);
        $values = array_column($catalog, 'value');

        foreach ($catalog as $index => $option) {
            $existing = ComponentTypeFieldOption::withTrashed()
                ->where('component_type_field_id', $field->id)
                ->where('value', $option['value'])
                ->first();

            $attributes = [
                'label' => $option['label'],
                'display_order' => $index + 1,
                'color' => $option['color'],
                'status' => 'active',
            ];

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                $existing->fill($attributes)->save();
            } else {
                $field->options()->create($attributes + ['value' => $option['value']]);
            }
        }

        ComponentTypeFieldOption::where('component_type_field_id', $field->id)
            ->when(!empty($values), fn ($query) => $query->whereNotIn('value', $values))
            ->delete();
    }

    /**
     * @return array<int, array{label: string, value: string, color: ?string}>
     */
    private static function catalogOptions(string $source): array
    {
        return match ($source) {
            'conditions' => Condition::orderBy('id')->get()
                ->map(fn ($condition) => [
                    'label' => $condition->name,
                    'value' => $condition->name,
                    'color' => $condition->color,
                ])->all(),
            'mechanism_fails' => MechanismFail::orderBy('name')->get()
                ->map(fn ($fail) => [
                    'label' => $fail->name,
                    'value' => $fail->name,
                    'color' => null,
                ])->all(),
            'priorities' => Priority::orderBy('name')->get()
                ->map(fn ($priority) => [
                    'label' => $priority->name,
                    'value' => $priority->name,
                    'color' => $priority->color,
                ])->all(),
            default => [],
        };
    }
}
