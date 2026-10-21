<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Un "informe" agrupa varios reportes/puntos de una misma inspección
 * (misma planta, área y período) para emitirlos en un solo documento.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('report_groups')) {
            return;
        }

        Schema::create('report_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable()->unique();
            $table->string('report_type')->default('thermography');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('process_id')->nullable()->constrained('processes')->nullOnDelete();
            $table->foreignId('ubication_id')->nullable()->constrained('ubications')->nullOnDelete();
            $table->string('title');
            $table->string('subject')->nullable();
            $table->string('period')->nullable();
            $table->date('service_date')->nullable();
            $table->text('summary')->nullable();
            $table->string('responsible_name');
            $table->string('responsible_position')->nullable();
            $table->string('responsible_certification')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('settings')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['report_type', 'service_date'], 'idx_groups_type_service_date');
            $table->index(['company_id', 'status'], 'idx_groups_company_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_groups');
    }
};
