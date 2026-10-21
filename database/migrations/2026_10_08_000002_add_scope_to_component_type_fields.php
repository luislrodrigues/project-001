<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('component_type_fields', 'scope')) {
            return;
        }

        Schema::table('component_type_fields', function (Blueprint $table) {
            $table->enum('scope', ['component', 'measurement'])->default('component')->after('type');
            $table->index(['component_type_id', 'scope'], 'idx_type_scope');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('component_type_fields', 'scope')) {
            return;
        }

        Schema::table('component_type_fields', function (Blueprint $table) {
            $table->dropIndex('idx_type_scope');
            $table->dropColumn('scope');
        });
    }
};
