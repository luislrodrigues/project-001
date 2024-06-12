<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reports';
    
    Protected $fillable = [
        'id',
        'name',
        'status',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
