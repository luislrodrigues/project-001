<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\ReportNumberConverter;

class Report extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guard = 'web';

    protected $table = 'reports';

    protected $fillable = [
        'equipment_id',
        'report_type',
        'measurement_date',
        'emissivity',
        'distance_m',
        'ambient_temp',
        'diagnosis',
        'condition_id',
        'condition_override_reason',
        'priority_id',
        'mechanism_fail_id',
        'component_fail_id',
        'recommendations',
        'ot_created',
        'status_ot',
        'execution_date',
        'general_recommendations',
        'analist_id',
        'status',
    ];

    protected $casts = [
        'diagnosis' => 'array',
        'recommendations' => 'array',
        'general_recommendations' => 'array',
        'measurement_date' => 'date',
        'execution_date' => 'date',
        'emissivity' => 'float',
        'distance_m' => 'float',
        'ambient_temp' => 'float',
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

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function measurementValues()
    {
        return $this->hasMany(ReportComponentValue::class);
    }

    public function images()
    {
        return $this->hasMany(ReportImage::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Valores de medición como lista [{component_id, field_id, value}].
     * No se usa un mapa con claves numéricas porque el pipeline de JsonResource
     * las reindexa a un array plano al serializar.
     */
    public function measurementValuesList(): array
    {
        $values = $this->relationLoaded('measurementValues')
            ? $this->measurementValues
            : $this->measurementValues()->get();

        return $values->map(fn ($value) => [
            'component_id' => (int) $value->component_id,
            'field_id' => (int) $value->component_type_field_id,
            // El snapshot manda: refleja la etiqueta con la que se capturó el dato.
            'name' => $value->field_name ?? $value->field->name ?? '',
            'unit' => $value->field_unit ?? $value->field->unit ?? '',
            'value' => $value->value,
        ])->values()->all();
    }

    /**
     * Consecutivo (nomenclatura) derived from id via ReportNumberConverter.
     */
    public function getConsecutivoAttribute(): string
    {
        return ReportNumberConverter::fromIdForReportType((int) $this->id, $this->report_type);
    }
}
