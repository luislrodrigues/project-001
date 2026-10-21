<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Los listados filtran por tipo de informe y rango de fecha de medición.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->index(['report_type', 'measurement_date'], 'idx_reports_type_measurement_date');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('idx_reports_type_measurement_date');
        });
    }
};
