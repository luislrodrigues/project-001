<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComponentTypeValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'component_type_values';

    protected $fillable = [
        'component_id', // Corregido: era id_component
        'component_type_field_id',
        'option_id', // Nueva: referencia a la opción si aplica
        'value', // Valor almacenado
        'raw_value', // Valor original (útil para migración)
        'status'
    ];

    /**
     * Relación con el componente
     */
    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id');
    }

    /**
     * Relación con el campo del tipo de componente
     */
    public function componentTypeField()
    {
        return $this->belongsTo(ComponentTypeField::class, 'component_type_field_id');
    }

    /**
     * Relación con la opción seleccionada (si aplica)
     */
    public function option()
    {
        return $this->belongsTo(ComponentTypeFieldOption::class, 'option_id');
    }

    /**
     * Obtener el valor mostrable (label de opción o valor directo)
     */
    public function getDisplayValueAttribute()
    {
        if ($this->option) {
            return $this->option->label;
        }
        return $this->value;
    }

    /**
     * Scope para valores activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
