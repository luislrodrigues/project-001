<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComponentTypeValue extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'component_type_values';

    protected $fillable = [
        'id',
        'id_component',
        'component_type_field_id',
        'value', 
        'status'
    ];

    public function component()
    {
        return $this->belongsTo(Component::class);
    }

    public function componentTypeField()
    {
        return $this->belongsTo(ComponentTypeField::class, 'component_type_field_id');
    }
}
