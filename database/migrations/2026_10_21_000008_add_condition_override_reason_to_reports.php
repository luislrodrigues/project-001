<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El analista puede apartarse de la clasificación ANSI/NETA (ΔT → condición),
 * pero debe justificarlo. Sin justificación, el sistema sigue avisando.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reports') || Schema::hasColumn('reports', 'condition_override_reason')) {
            return;
        }

        Schema::table('reports', function (Blueprint $table) {
            $table->string('condition_override_reason', 500)->nullable()->after('condition_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('reports') || !Schema::hasColumn('reports', 'condition_override_reason')) {
            return;
        }

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('condition_override_reason');
        });
    }
};
