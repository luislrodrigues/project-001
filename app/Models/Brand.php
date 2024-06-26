<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'brands';

    Protected $fillable = [
        'id',
        'name',
        'status',
    ];
    
    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }
}
