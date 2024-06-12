<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'equipments';
    
    Protected $fillable = [
        'id',
        'name',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function characteristics()
    {
        return $this->hasMany(EquipmentCharacteristic::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
