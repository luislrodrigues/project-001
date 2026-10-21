<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('equipment', 'report_type')) {
            Schema::table('equipment', function (Blueprint $table) {
                $table->string('report_type')->default('vibration');
                $table->index('report_type');
            });
        }

        if (!Schema::hasColumn('component_types', 'report_type')) {
            Schema::table('component_types', function (Blueprint $table) {
                $table->string('report_type')->default('vibration');
                $table->index('report_type');
            });
        }

        DB::table('equipment')->update(['report_type' => 'vibration']);
        DB::table('component_types')->update(['report_type' => 'vibration']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('equipment', 'report_type')) {
            try {
                Schema::table('equipment', function (Blueprint $table) {
                    $table->dropIndex(['report_type']);
                });
            } catch (\Throwable $e) {
            }

            Schema::table('equipment', function (Blueprint $table) {
                $table->dropColumn('report_type');
            });
        }

        if (Schema::hasColumn('component_types', 'report_type')) {
            try {
                Schema::table('component_types', function (Blueprint $table) {
                    $table->dropIndex(['report_type']);
                });
            } catch (\Throwable $e) {
            }

            Schema::table('component_types', function (Blueprint $table) {
                $table->dropColumn('report_type');
            });
        }
    }
};

