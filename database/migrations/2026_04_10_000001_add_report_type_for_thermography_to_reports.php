<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('reports', 'report_type')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->string('report_type')->default('vibration');
                $table->index('report_type');
            });
        }

        DB::table('reports')->update(['report_type' => 'vibration']);
    }

    public function down(): void
    {
        if (!Schema::hasColumn('reports', 'report_type')) {
            return;
        }

        try {
            Schema::table('reports', function (Blueprint $table) {
                $table->dropIndex(['report_type']);
            });
        } catch (\Throwable $e) {
        }

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('report_type');
        });
    }
};
