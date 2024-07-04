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
        'location',
        'process',
        'diagnosis',
        'condition',
        'failure_mechanism',
        'component_failure',
        'recommendation',
        'ot_created',
        'status_ot',
        'execution_date',
        'verification_recommendation',
        'analist',
        'status',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
