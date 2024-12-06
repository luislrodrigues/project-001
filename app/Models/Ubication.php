<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubication extends Model
{
    protected $table = 'ubications';

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
