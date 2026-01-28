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
        'diagnosis',
        'condition_id',
        'mechanism_fail_id',
        'component_fail_id',
        'recommendations',
        'ot_created',
        'status_ot',
        'execution_date',
        'verification_recommendation',
        'analist_id',
        'status',
    ];

    protected $casts = [
        'recommendations' => 'array', // Indica que la columna recommendations es un array
        'measurement_date' => 'date',
        'execution_date' => 'date',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
    public function analist()
    {
        return $this->belongsTo(User::class);
    }
    public function componentFail()
    {
        return $this->belongsTo(ComponentFail::class);
    }
    public function mechanismFail()
    {
        return $this->belongsTo(MechanismFail::class);
    }
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }
}
