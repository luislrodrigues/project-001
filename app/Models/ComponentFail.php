<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComponentFail extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'component_fails';

    Protected $fillable = [
        'id',
        'name',
        'status',
    ];
}
