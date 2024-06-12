<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentCharacteristic extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'equipment_id', 
        'name', 
        'value', 
        'unit', 
        'status',
    ];


    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
