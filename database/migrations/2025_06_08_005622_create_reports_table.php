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
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();
            $table->json('diagnosis')->nullable();
            $table->foreignId('condition_id')->constrained('conditions')->onDelete('cascade');
            $table->foreignId('component_fail_id')->constrained('component_fails')->onDelete('cascade');
            $table->foreignId('mechanism_fail_id')->constrained('mechanism_fails')->onDelete('cascade');
            $table->json('recommendations')->nullable();
            $table->enum('priority',['Alta','Media']);
            $table->string('ot_created')->nullable();
            $table->string('status_ot')->nullable();
            $table->date('execution_date')->nullable();
            $table->text('verification_recommendation')->nullable();
            $table->foreignId('analist_id')->constrained('users')->onDelete('cascade');
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
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('image_1');
            $table->dropColumn('image_2');
        });
    }
};
