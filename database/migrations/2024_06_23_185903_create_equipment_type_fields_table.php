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
        Schema::create('equipment_type_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('equipment_type_id')->constrained('equipment_types')->onDelete('cascade');
            $table->string('name');
            $table->string('unit')->nullable();
            $table->text('options')->nullable();
            $table->enum('type',['text','select','numeric'])->default('text');
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
        Schema::dropIfExists('equipment_type_fields');
    }
};
