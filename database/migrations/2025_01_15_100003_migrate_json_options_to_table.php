<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migra las opciones almacenadas en JSON a la nueva tabla normalizada
     */
    public function up(): void
    {
        // Obtener todos los campos que tienen opciones en JSON
        $fields = DB::table('component_type_fields')
            ->where('type', 'select')
            ->whereNotNull('options')
            ->where('options', '!=', '')
            ->get();
        
        foreach ($fields as $field) {
            try {
                // Decodificar JSON
                $options = json_decode($field->options, true);
                
                // Si no es array, intentar como string separado por ;
                if (!is_array($options)) {
                    $options = explode(';', $field->options);
                    $options = array_map('trim', $options);
                    $options = array_filter($options); // Eliminar vacíos
                }
                
                // Insertar cada opción como registro individual
                $displayOrder = 1;
                foreach ($options as $index => $option) {
                    if (empty(trim($option))) {
                        continue;
                    }
                    
                    DB::table('component_type_field_options')->insert([
                        'component_type_field_id' => $field->id,
                        'label' => trim($option),
                        'value' => trim($option),
                        'display_order' => $displayOrder++,
                        'is_default' => $index === 0, // Primera opción como default
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                // Log error pero continuar con siguiente campo
                \Log::warning("Error migrando opciones del campo {$field->id}: " . $e->getMessage());
            }
        }
        
        // NOTA: No eliminamos la columna 'options' por ahora para mantener compatibilidad
        // Se puede eliminar en una migración futura después de verificar que todo funciona
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar todas las opciones migradas
        // Las opciones se restaurarían desde backup si es necesario
        DB::table('component_type_field_options')->truncate();
    }
};
