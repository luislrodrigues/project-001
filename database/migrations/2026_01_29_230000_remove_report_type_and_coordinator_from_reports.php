<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('reports', 'report_type') && !Schema::hasColumn('reports', 'coordinator_id')) {
            return;
        }
        if (Schema::hasColumn('reports', 'coordinator_id')) {
            try {
                Schema::table('reports', function (Blueprint $table) {
                    $table->dropForeign(['coordinator_id']);
                });
            } catch (\Throwable $e) {
                // FK puede no existir
            }
        }
        $columnsToDrop = array_filter(
            ['report_type', 'coordinator_id'],
            fn ($col) => Schema::hasColumn('reports', $col)
        );
        if (!empty($columnsToDrop)) {
            Schema::table('reports', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('report_type')->default('acta_externa');
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }
};
