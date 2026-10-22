<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Condition extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'conditions';

    protected $fillable = [
        'id',
        'name',
        'color',
        'text_color',
        'status',
    ];

    /**
     * Las opciones del campo "Condición" por punto salen de este catálogo:
     * al crear, renombrar, recolorear o borrar, se sincronizan solas.
     */
    protected static function booted(): void
    {
        static::saved(fn () => \App\Support\ThermographyFieldTemplate::syncCatalog('conditions'));
        static::deleted(fn () => \App\Support\ThermographyFieldTemplate::syncCatalog('conditions'));
    }
}
