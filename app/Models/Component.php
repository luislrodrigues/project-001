<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Component extends Model
{
    use HasFactory;
    use SoftDeletes;

    Protected $fillable = [
        'id',
        'equipment_id',
        'brand_id',
        'component_type_id',
        'display_order',
        'status',
    ];
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
    public function componentType()
    {
        return $this->belongsTo(ComponentType::class);
    }
    public function values()
    {
        return $this->hasMany(ComponentTypeValue::class, 'component_id');
    }
    /**
     * Campos del tipo de componente (no son directos del componente)
     */
    public function fields()
    {
        return $this->hasMany(ComponentTypeField::class, 'component_type_id', 'component_type_id');
    }

    /**
     * Valores de los campos dinámicos del componente
     */
    public function fieldValues()
    {
        return $this->hasMany(ComponentTypeValue::class, 'component_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
