<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComponentTypeField extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'component_type_fields';

    protected $fillable = [
        'component_type_id', 
        'name',
        'slug',
        'type', 
        'scope',
        'options', // Mantener por compatibilidad, se deprecará
        'unit',
        'display_order',
        'is_required',
        'is_searchable',
        'default_value',
        'placeholder',
        'help_text',
        'validation_rules',
        'status'
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_required' => 'boolean',
        'is_searchable' => 'boolean',
        'validation_rules' => 'array',
    ];

    /**
     * Relación con el tipo de componente
     */
    public function typeComponent()
    {
        return $this->belongsTo(ComponentType::class, 'component_type_id');
    }

    /**
     * Relación con las opciones del campo (para select/multiselect)
     */
    public function options()
    {
        return $this->hasMany(ComponentTypeFieldOption::class, 'component_type_field_id')
            ->active()
            ->ordered();
    }

    /**
     * Todas las opciones incluyendo inactivas (sin scope)
     */
    public function allOptions()
    {
        return $this->hasMany(ComponentTypeFieldOption::class, 'component_type_field_id')
            ->ordered();
    }

    /**
     * Valores asignados a este campo
     */
    public function values()
    {
        return $this->hasMany(ComponentTypeValue::class, 'component_type_field_id');
    }

    /**
     * Obtener valor único (para relaciones hasOne)
     */
    public function value()
    {
        return $this->hasOne(ComponentTypeValue::class, 'component_type_field_id')
            ->where('status', 'active');
    }

    /**
     * Scope para campos ordenados
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }

    /**
     * Campos que se capturan en cada medición (reporte) en lugar de en el componente.
     */
    public function scopeMeasurement($query)
    {
        return $query->where('scope', 'measurement');
    }

    /**
     * Definiciones de campos listas para la API (incluye ámbito y opciones).
     */
    public static function definitions($fields): array
    {
        return collect($fields)
            ->filter(fn ($field) => ($field->status ?? 'active') === 'active')
            ->sortBy('display_order')
            ->values()
            ->map(fn ($field) => [
                'id' => $field->id,
                'name' => $field->name,
                'type' => $field->type,
                'unit' => $field->unit,
                'scope' => $field->scope ?? 'component',
                'is_required' => (bool) $field->is_required,
                'display_order' => (int) $field->display_order,
                'options' => $field->relationLoaded('allOptions')
                    ? $field->allOptions->map(fn ($option) => [
                        'id' => $option->id,
                        'label' => $option->label,
                        'value' => $option->value,
                    ])->values()->all()
                    : [],
            ])
            ->all();
    }

    /**
     * Scope para campos activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para campos requeridos
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope para campos buscables
     */
    public function scopeSearchable($query)
    {
        return $query->where('is_searchable', true);
    }

    /**
     * Verificar si el campo tiene opciones
     */
    public function hasOptions(): bool
    {
        return in_array($this->type, ['select', 'multiselect']) && $this->options()->count() > 0;
    }

    /**
     * Obtener opción por defecto
     */
    public function getDefaultOption()
    {
        return $this->options()->where('is_default', true)->first();
    }

    /**
     * Generar slug automáticamente si no existe
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($field) {
            if (empty($field->slug) && !empty($field->name)) {
                $field->slug = \Illuminate\Support\Str::slug($field->name);
            }
        });
    }
}
