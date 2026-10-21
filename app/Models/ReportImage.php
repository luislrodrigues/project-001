<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportImage extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const KIND_THERMAL = 'thermal';
    public const KIND_VISUAL = 'visual';

    public const KINDS = [
        self::KIND_THERMAL => 'Térmica',
        self::KIND_VISUAL => 'Visual',
    ];

    protected $table = 'report_images';

    protected $fillable = [
        'report_id',
        'component_id',
        'path',
        'kind',
        'caption',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function component()
    {
        return $this->belongsTo(Component::class);
    }

    public function getUrlAttribute(): string
    {
        if (filter_var($this->path, FILTER_VALIDATE_URL)) {
            return $this->path;
        }

        return asset('storage/' . ltrim((string) $this->path, '/'));
    }

    public function getKindLabelAttribute(): string
    {
        return self::KINDS[$this->kind] ?? ucfirst((string) $this->kind);
    }
}
