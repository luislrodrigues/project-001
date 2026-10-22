<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Priority extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'priorities';

    protected $fillable = [
        'name',
        'color',
        'text_color',
        'status',
    ];

    /**
     * El campo "Prioridad" por punto copia este catálogo; se sincroniza solo.
     */
    protected static function booted(): void
    {
        static::saved(fn () => \App\Support\ThermographyFieldTemplate::syncCatalog('priorities'));
        static::deleted(fn () => \App\Support\ThermographyFieldTemplate::syncCatalog('priorities'));
    }
}
