<?php

namespace Database\Seeders;

use App\Support\ThermographyFieldTemplate;
use Illuminate\Database\Seeder;

/**
 * Crea/actualiza los campos por punto de todos los tipos de componente de
 * termografía (Condición, Mecanismo de Falla, Delta Temperatura, Prioridad,
 * Imagen Térmica). Idempotente.
 *
 * Ejecutar con: php artisan db:seed --class=ThermographyFieldTemplateSeeder
 */
class ThermographyFieldTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $result = ThermographyFieldTemplate::applyToAll();

        $this->command?->info(sprintf(
            'Plantilla de termografía aplicada a %d tipos (creados: %d, actualizados: %d, retirados: %d).',
            $result['types'],
            $result['created'],
            $result['updated'],
            $result['removed']
        ));
    }
}
