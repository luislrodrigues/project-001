<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('report_component_values')) {
            return;
        }

        Schema::create('report_component_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('component_id')->constrained('components')->onDelete('cascade');
            $table->foreignId('component_type_field_id')->constrained('component_type_fields')->onDelete('cascade');
            $table->text('value')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['report_id', 'component_id', 'component_type_field_id'], 'idx_report_component_field');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_component_values');
    }
};
