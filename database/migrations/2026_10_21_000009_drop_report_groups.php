<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Se retira la funcionalidad de informes agrupados: la columna en reports
 * y la tabla report_groups.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('reports') && Schema::hasColumn('reports', 'report_group_id')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->dropForeign(['report_group_id']);
            });

            Schema::table('reports', function (Blueprint $table) {
                $table->dropColumn('report_group_id');
            });
        }

        Schema::dropIfExists('report_groups');
    }

    public function down(): void
    {
        if (!Schema::hasTable('report_groups')) {
            Schema::create('report_groups', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('code', 50)->nullable();
                $table->string('title');
                $table->text('summary')->nullable();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->foreignId('process_id')->nullable()->constrained('processes')->nullOnDelete();
                $table->foreignId('ubication_id')->nullable()->constrained('ubications')->nullOnDelete();
                $table->date('service_date')->nullable();
                $table->string('period', 50)->nullable();
                $table->string('elaborated_by')->nullable();
                $table->string('elaborated_role')->nullable();
                $table->string('approved_by')->nullable();
                $table->string('approved_role')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('reports') && !Schema::hasColumn('reports', 'report_group_id')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->foreignId('report_group_id')->nullable()->constrained('report_groups')->nullOnDelete();
            });
        }
    }
};
