<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea tabla para opciones individuales de campos tipo select/multiselect
     * Esto reemplaza el almacenamiento en JSON por registros normalizados
     */
    public function up(): void
    {
        Schema::create('component_type_field_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('component_type_field_id')
                ->constrained('component_type_fields')
                ->onDelete('cascade');
            $table->string('label'); // Texto visible para el usuario
            $table->string('value'); // Valor que se almacena
            $table->integer('display_order')->default(0); // Orden de visualización
            $table->boolean('is_default')->default(false); // Es opción por defecto
            $table->string('color', 7)->nullable(); // Color para UI (hex: #FFFFFF)
            $table->string('icon', 50)->nullable(); // Icono opcional
            $table->json('metadata')->nullable(); // Datos adicionales (especificaciones, etc)
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para mejor performance
            $table->index(['component_type_field_id', 'display_order'], 'idx_field_order');
            $table->index(['component_type_field_id', 'status'], 'idx_field_status');
            
            // Unicidad: mismo valor no puede repetirse en el mismo campo
            // (permitir soft deletes)
            $table->unique(['component_type_field_id', 'value', 'deleted_at'], 'unique_field_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_type_field_options');
    }
};
