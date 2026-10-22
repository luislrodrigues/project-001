<?php

namespace App\Support;

use App\Models\Report;

/**
 * Clasificación automática de severidad termográfica según ANSI/NETA MTS
 * (diferencia de temperatura entre el punto y la referencia).
 *
 * La escala es la referencia técnica; el analista puede sobrescribirla,
 * pero el sistema deja constancia de la discrepancia.
 */
class ThermographyClassifier
{
    /**
     * Rangos de ΔT en °C tal como se imprimen en el informe
     * (ANSI/NETA AST-2013, Tabla 100.18). `max` nulo = sin límite superior.
     */
    public const SCALE = [
        [
            'max' => 1.0,
            'condition' => 'Normal',
            'severity' => 0,
            'range' => '< 1 °C',
            'action' => 'Sin diferencia térmica apreciable.',
        ],
        [
            'max' => 4.0,
            'condition' => 'Normal',
            'severity' => 0,
            'range' => '1 °C – 3 °C',
            'action' => 'Posible deficiencia. Merece investigación.',
        ],
        [
            'max' => 15.0,
            'condition' => 'Alarma',
            'severity' => 2,
            'range' => '4 °C – 15 °C',
            'action' => 'Indica deficiencia probable; reparar cuando sea posible.',
        ],
        [
            'max' => null,
            'condition' => 'Peligro',
            'severity' => 3,
            'range' => '> 15 °C',
            'action' => 'Gran discrepancia, reparar de inmediato.',
        ],
    ];

    /**
     * @return array{condition: string, severity: int, range: string, note: string, delta_t: float}|null
     */
    public static function classify(?float $deltaT): ?array
    {
        if ($deltaT === null || !is_finite($deltaT)) {
            return null;
        }

        foreach (self::SCALE as $range) {
            if ($range['max'] === null || $deltaT < $range['max']) {
                return $range + ['delta_t' => round($deltaT, 1)];
            }
        }

        return null;
    }

    /**
     * ΔT del reporte, leído de los valores de medición (campo "Delta T").
     * En termografía hay un ΔT por punto: se toma el mayor.
     */
    public static function deltaTForReport(Report $report): ?float
    {
        $values = $report->relationLoaded('measurementValues')
            ? $report->measurementValues
            : $report->measurementValues()->get();

        $max = null;

        foreach ($values as $value) {
            $name = $value->field_name ?? $value->field->name ?? '';
            if (preg_match('/delta\s*t|Δ\s*t/i', (string) $name) !== 1) {
                continue;
            }

            $raw = trim((string) $value->value);
            if ($raw === '') {
                continue;
            }

            // Tolera "14,2" (formato es-CO) y "14.2 °C".
            $raw = str_replace(',', '.', $raw);
            $raw = preg_replace('/[^0-9.\-]/', '', $raw);

            if ($raw === '' || !is_numeric($raw)) {
                continue;
            }

            $number = (float) $raw;
            $max = $max === null ? $number : max($max, $number);
        }

        return $max;
    }

    /**
     * Sugerencia para el reporte + aviso si la condición guardada no coincide.
     *
     * @return array{classification: ?array, declared: ?string, matches: bool, warning: ?string}
     */
    public static function evaluate(Report $report): array
    {
        $deltaT = self::deltaTForReport($report);
        $classification = self::classify($deltaT);
        $declared = $report->condition->name ?? null;
        $declaredNorm = $declared !== null ? self::normalize($declared) : null;
        $suggestedNorm = $classification !== null ? self::normalize($classification['condition']) : null;

        $matches = $suggestedNorm === null
            || $declaredNorm === null
            || $suggestedNorm === $declaredNorm
            || in_array($declaredNorm, self::EXEMPT_CONDITIONS, true);

        $overrideReason = trim((string) ($report->condition_override_reason ?? ''));

        $warning = null;
        if (!$matches && $overrideReason === '') {
            $warning = sprintf(
                'ΔT %s °C corresponde a «%s» según ANSI/NETA, pero el reporte está en «%s».',
                number_format($classification['delta_t'], 1, ',', '.'),
                $classification['condition'],
                $declared
            );
        }

        return [
            'classification' => $classification,
            'declared' => $declared,
            'matches' => $matches || $overrideReason !== '',
            'overridden' => !$matches && $overrideReason !== '',
            'override_reason' => $overrideReason !== '' ? $overrideReason : null,
            'warning' => $warning,
        ];
    }

    /**
     * Condiciones que no siguen la escala de ΔT (decisión operativa del analista).
     */
    private const EXEMPT_CONDITIONS = ['fuera de servicio', 'fueradeservicio'];

    private static function normalize(string $condition): string
    {
        return strtolower(str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', ' '],
            ['a', 'e', 'i', 'o', 'u', 'n', ''],
            $condition
        ));
    }
}
