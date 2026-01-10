<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mejora la tabla component_type_fields agregando campos adicionales
     * y mejorando la estructura para mejor mantenibilidad
     */
    public function up(): void
    {
        Schema::table('component_type_fields', function (Blueprint $table) {
            // Agregar nuevos campos para mejor gestión
            $table->string('slug')->nullable()->after('name');
            $table->integer('display_order')->default(0)->after('unit');
            $table->boolean('is_required')->default(false)->after('display_order');
            $table->boolean('is_searchable')->default(true)->after('is_required');
            $table->text('default_value')->nullable()->after('is_searchable');
            $table->string('placeholder')->nullable()->after('default_value');
            $table->text('help_text')->nullable()->after('placeholder');
            $table->json('validation_rules')->nullable()->after('help_text');
            
            // Expandir tipos de campo disponibles
            $table->enum('type', [
                'text',
                'textarea',
                'numeric',
                'select',
                'multiselect',
                'date',
                'datetime',
                'boolean',
                'file',
                'url',
                'email'
            ])->default('text')->change();
            
            // Índices para mejor performance
            $table->index(['component_type_id', 'display_order'], 'idx_type_order');
            $table->index(['component_type_id', 'status'], 'idx_type_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('component_type_fields', function (Blueprint $table) {
            $table->dropIndex('idx_type_order');
            $table->dropIndex('idx_type_status');
            
            $table->dropColumn([
                'slug',
                'display_order',
                'is_required',
                'is_searchable',
                'default_value',
                'placeholder',
                'help_text',
                'validation_rules'
            ]);
            
            // Revertir tipo a valores originales
            $table->enum('type', ['text', 'select', 'numeric'])->default('text')->change();
        });
    }
};
