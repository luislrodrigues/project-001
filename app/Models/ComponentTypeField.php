<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComponentTypeField extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'component_type_fields';

    protected $fillable = [
        'id',
        'component_type_id', 
        'name', 
        'type', 
        'options', 
        'unit',
        'status'
    ];

    public function typeComponent()
    {
        return $this->belongsTo(ComponentType::class);
    }
    public function value()
    {
        return $this->hasOne(ComponentTypeValue::class,'component_type_field_id')->where('status','active');
    }
}
