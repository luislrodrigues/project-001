<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->date('measurement_date');
            $table->string('location');
            $table->string('process');
            $table->text('diagnosis');
            $table->enum('condition',['Critico','Seguimiento','Alarma','Peligro']);
            $table->string('failure_mechanism');
            $table->string('component_failure');
            $table->text('recommendation');
            $table->enum('priority',['Alta','Media']);
            $table->string('ot_created')->nullable();
            $table->string('status_ot')->nullable();
            $table->date('execution_date')->nullable();
            $table->text('verification_recommendation')->nullable();
            $table->string('analist');
            $table->enum('status',['active','inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
