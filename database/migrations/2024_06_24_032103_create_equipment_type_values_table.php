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
        Schema::create('equipment_type_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('id_equipment')->constrained('equipment');
            $table->foreignId('equipment_type_field_id')->constrained('equipment_type_fields');
            $table->string('value');
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
        Schema::dropIfExists('equipment_type_values');
    }
};
