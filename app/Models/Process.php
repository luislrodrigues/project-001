<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Process extends Model
{
    use HasFactory,SoftDeletes;

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
