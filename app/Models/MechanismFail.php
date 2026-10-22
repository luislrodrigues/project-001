<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MechanismFail extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'mechanism_fails';

    Protected $fillable = [
        'id',
        'name',
        'status',
    ];

    /**
     * El campo "Mecanismo de Falla" por punto copia este catálogo; se sincroniza solo.
     */
    protected static function booted(): void
    {
        static::saved(fn () => \App\Support\ThermographyFieldTemplate::syncCatalog('mechanism_fails'));
        static::deleted(fn () => \App\Support\ThermographyFieldTemplate::syncCatalog('mechanism_fails'));
    }
}
