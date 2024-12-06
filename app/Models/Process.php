<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $table = 'processes';

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
