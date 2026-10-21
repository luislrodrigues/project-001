<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Las imágenes dejan de vivir dentro del JSON `diagnosis` y pasan a ser una
 * entidad propia, con tipo (térmica / visual), pie de foto y orden.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('report_images')) {
            Schema::create('report_images', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
                $table->foreignId('component_id')->nullable()->constrained('components')->nullOnDelete();
                $table->string('path');
                $table->enum('kind', ['thermal', 'visual'])->default('thermal');
                $table->string('caption', 255)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->softDeletes();
                $table->timestamps();

                $table->index(['report_id', 'component_id', 'sort_order'], 'idx_report_images_order');
            });
        }

        $this->migrateFromDiagnosisJson();
    }

    /**
     * Copia las imágenes que estaban en diagnosis[componentId][images] a la tabla.
     */
    private function migrateFromDiagnosisJson(): void
    {
        if (!Schema::hasTable('report_images') || !Schema::hasColumn('reports', 'diagnosis')) {
            return;
        }

        DB::table('reports')
            ->whereNotNull('diagnosis')
            ->orderBy('id')
            ->select(['id', 'diagnosis', 'report_type'])
            ->chunk(200, function ($reports) {
                foreach ($reports as $report) {
                    $kind = ($report->report_type ?? '') === 'thermography'
                        ? 'thermal'
                        : 'visual';
                    $diagnosis = json_decode((string) $report->diagnosis, true);
                    if (!is_array($diagnosis)) {
                        continue;
                    }

                    foreach ($diagnosis as $componentId => $data) {
                        if (!is_array($data) || empty($data['images']) || !is_array($data['images'])) {
                            continue;
                        }

                        foreach (array_values($data['images']) as $index => $path) {
                            if (!is_string($path) || trim($path) === '') {
                                continue;
                            }

                            $exists = DB::table('report_images')
                                ->where('report_id', $report->id)
                                ->where('path', $this->normalizePath($path))
                                ->exists();

                            if ($exists) {
                                continue;
                            }

                            DB::table('report_images')->insert([
                                'report_id' => $report->id,
                                'component_id' => is_numeric($componentId) ? (int) $componentId : null,
                                'path' => $this->normalizePath($path),
                                'kind' => $kind,
                                'caption' => null,
                                'sort_order' => $index,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            });
    }

    private function normalizePath(string $path): string
    {
        if (preg_match('#/storage/(.+)$#', $path, $matches) === 1) {
            return $matches[1];
        }

        return ltrim($path, '/');
    }

    public function down(): void
    {
        Schema::dropIfExists('report_images');
    }
};
