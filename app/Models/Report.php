<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guard = 'web';

    protected $table = 'reports';
    
    Protected $fillable = [
        'id',
        'equipment_id',
        'measurement_date',
        'image_1',
        'image_2',
        'location',
        'process',
        'diagnosis',
        'condition',
        'failure_mechanism',
        'component_failure',
        'recommendations',
        'ot_created',
        'status_ot',
        'execution_date',
        'verification_recommendation',
        'analist',
        'status',
    ];

    protected $casts = [
        'recommendations' => 'array', // Indica que la columna recommendations es un array
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
