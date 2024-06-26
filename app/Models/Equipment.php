<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $guard = 'web';

    protected $table = 'equipment';
    
    Protected $fillable = [
        'id',
        'brand_id',
        'company_id',
        'equipment_type_id',
        'name',
        'code',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function equipmentType()
    {
        return $this->belongsTo(EquipmentType::class);
    }
    public function values()
    {
        return $this->hasMany(EquipmentTypeValue::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
