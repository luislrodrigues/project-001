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
        'company_id',
        'process_id',
        'ubication_id',
        'equipment_type_id',
        'name',
        'code',
        'tag',
        'description',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function equipmentType()
    {
        return $this->belongsTo(EquipmentType::class);
    }
    public function process()
    {
        return $this->belongsTo(Process::class);
    }
    public function ubication()
    {
        return $this->belongsTo(Ubication::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
    public function components()
    {
        return $this->hasMany(Component::class);
    }
}
