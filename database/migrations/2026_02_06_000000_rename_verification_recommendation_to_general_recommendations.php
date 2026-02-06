<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Renombra verification_recommendation a general_recommendations (JSON array de strings).
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->json('general_recommendations')->nullable()->after('execution_date');
        });

        DB::table('reports')->orderBy('id')->chunk(100, function ($reports) {
            foreach ($reports as $report) {
                $old = $report->verification_recommendation ?? null;
                $new = null;
                if ($old !== null && trim((string) $old) !== '') {
                    $new = json_encode([trim((string) $old)]);
                }
                DB::table('reports')->where('id', $report->id)->update(['general_recommendations' => $new]);
            }
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('verification_recommendation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->text('verification_recommendation')->nullable()->after('execution_date');
        });

        DB::table('reports')->orderBy('id')->chunk(100, function ($reports) {
            foreach ($reports as $report) {
                $arr = $report->general_recommendations ? json_decode($report->general_recommendations, true) : null;
                $text = null;
                if (is_array($arr) && count($arr) > 0) {
                    $text = implode("\n", $arr);
                }
                DB::table('reports')->where('id', $report->id)->update(['verification_recommendation' => $text]);
            }
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('general_recommendations');
        });
    }
};
