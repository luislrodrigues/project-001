<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentTypeValue extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'equipment_type_values';

    protected $fillable = [
        'id',
        'equipment_type_field_id', 
        'id_equipment',
        'value', 
        'status'
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function equipmentType()
    {
        return $this->belongsTo(EquipmentTypeField::class, 'equipment_type_field_id');
    }
}
