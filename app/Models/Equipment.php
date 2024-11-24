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

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
    public function components()
    {
        return $this->hasMany(Component::class);
    }
}
