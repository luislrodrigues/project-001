<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('reports', 'report_group_id')) {
            return;
        }

        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('report_group_id')
                ->nullable()
                ->after('equipment_id')
                ->constrained('report_groups')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('reports', 'report_group_id')) {
            return;
        }

        Schema::table('reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('report_group_id');
        });
    }
};
