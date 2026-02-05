<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE reports MODIFY COLUMN priority ENUM('Alta','Media','Baja','Normal') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE reports MODIFY COLUMN priority ENUM('Alta','Media','Baja') NOT NULL");
    }
};
