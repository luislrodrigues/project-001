<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComponentTypeFieldOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'component_type_field_options';

    protected $fillable = [
        'component_type_field_id',
        'label',
        'value',
        'display_order',
        'is_default',
        'color',
        'icon',
        'metadata',
        'status',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_default' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Relación con el campo al que pertenece
     */
    public function field()
    {
        return $this->belongsTo(ComponentTypeField::class, 'component_type_field_id');
    }

    /**
     * Valores de componentes que usan esta opción
     */
    public function componentValues()
    {
        return $this->hasMany(ComponentTypeValue::class, 'option_id');
    }

    /**
     * Scope para opciones activas ordenadas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para ordenar por display_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }

    /**
     * Verificar si la opción está en uso
     */
    public function isInUse(): bool
    {
        return $this->componentValues()->exists();
    }
}
