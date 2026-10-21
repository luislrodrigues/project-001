<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportComponentValue extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'report_component_values';

    protected $fillable = [
        'report_id',
        'component_id',
        'component_type_field_id',
        'field_name',
        'field_unit',
        'field_order',
        'value',
        'status',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function component()
    {
        return $this->belongsTo(Component::class);
    }

    public function field()
    {
        return $this->belongsTo(ComponentTypeField::class, 'component_type_field_id');
    }
}
