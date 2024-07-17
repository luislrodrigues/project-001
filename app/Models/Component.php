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
        return $this->hasMany(ComponentTypeValue::class,'id_component');
    }
    public function fields()
    {
        return $this->hasMany(ComponentTypeField::class,'component_type_id','component_type_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
