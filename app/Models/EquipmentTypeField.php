<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentTypeField extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'equipment_type_fields';

    protected $fillable = [
        'id',
        'equipment_type_id', 
        'name', 
        'type', 
        'options', 
        'unit',
        'status'
    ];

    public function typeEquipment()
    {
        return $this->belongsTo(EquipmentType::class);
    }
}
