<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('priority_id')->nullable()->after('condition_id');
        });

        // Asegurar que existan prioridades con los nombres del enum actual
        $names = ['Alta', 'Media', 'Baja', 'Normal'];
        foreach ($names as $name) {
            $exists = DB::table('priorities')->where('name', $name)->whereNull('deleted_at')->exists();
            if (!$exists) {
                DB::table('priorities')->insert([
                    'name' => $name,
                    'color' => null,
                    'text_color' => null,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        DB::statement("
            UPDATE reports r
            INNER JOIN priorities p ON p.name = r.priority AND p.deleted_at IS NULL
            SET r.priority_id = p.id
        ");

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('priority');
        });

        DB::statement('ALTER TABLE reports MODIFY priority_id BIGINT UNSIGNED NOT NULL');
        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('priority_id')->references('id')->on('priorities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['priority_id']);
        });

        DB::statement("ALTER TABLE reports ADD priority VARCHAR(255) NULL AFTER condition_id");
        DB::statement("
            UPDATE reports r
            INNER JOIN priorities p ON p.id = r.priority_id
            SET r.priority = p.name
        ");
        DB::statement("ALTER TABLE reports MODIFY priority ENUM('Alta','Media','Baja','Normal') NOT NULL");
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('priority_id');
        });
    }
};
