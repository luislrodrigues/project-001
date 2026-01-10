<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Corrige el nombre de la foreign key de id_component a component_id
     * y agrega soporte para opciones
     */
    public function up(): void
    {
        // Primero eliminar foreign key si existe
        Schema::table('component_type_values', function (Blueprint $table) {
            $table->dropForeign(['id_component']);
        });

        // Renombrar columna (Laravel no soporta renameColumn directamente, usar raw SQL)
        DB::statement('ALTER TABLE `component_type_values` CHANGE `id_component` `component_id` BIGINT UNSIGNED NOT NULL');
        
        Schema::table('component_type_values', function (Blueprint $table) {
            // Recrear foreign key con nuevo nombre
            $table->foreign('component_id')
                ->references('id')
                ->on('components')
                ->onDelete('cascade');
            
            // Agregar relación con opciones si aplica (después de crear la tabla)
            if (Schema::hasTable('component_type_field_options')) {
                $table->foreignId('option_id')
                    ->nullable()
                    ->after('component_type_field_id')
                    ->constrained('component_type_field_options')
                    ->onDelete('set null');
            }
            
            // Guardar valor original (útil para migración)
            $table->text('raw_value')->nullable()->after('value');
            
            // Índices mejorados
            $table->index(['component_id', 'component_type_field_id'], 'idx_component_field');
            if (Schema::hasTable('component_type_field_options')) {
                $table->index(['component_id', 'option_id'], 'idx_component_option');
            }
            $table->index('component_type_field_id', 'idx_field');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('component_type_values', function (Blueprint $table) {
            $table->dropIndex('idx_component_field');
            if (Schema::hasColumn('component_type_values', 'option_id')) {
                $table->dropIndex('idx_component_option');
                $table->dropForeign(['option_id']);
                $table->dropColumn('option_id');
            }
            $table->dropIndex('idx_field');
            
            if (Schema::hasColumn('component_type_values', 'raw_value')) {
                $table->dropColumn('raw_value');
            }
            
            $table->dropForeign(['component_id']);
        });

        // Renombrar columna de vuelta
        DB::statement('ALTER TABLE `component_type_values` CHANGE `component_id` `id_component` BIGINT UNSIGNED NOT NULL');
        
        Schema::table('component_type_values', function (Blueprint $table) {
            $table->foreign('id_component')
                ->references('id')
                ->on('components')
                ->onDelete('cascade');
        });
    }
};
