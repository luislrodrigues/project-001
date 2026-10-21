<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportGroup extends Model
{
    use SoftDeletes;

    protected $table = 'report_groups';

    protected $fillable = [
        'code',
        'report_type',
        'company_id',
        'process_id',
        'ubication_id',
        'title',
        'subject',
        'period',
        'service_date',
        'summary',
        'responsible_name',
        'responsible_position',
        'responsible_certification',
        'status',
        'settings',
    ];

    protected $casts = [
        'service_date' => 'date',
        'settings' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function ubication()
    {
        return $this->belongsTo(Ubication::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'report_group_id');
    }

    /**
     * Número visible del informe: usa el código capturado o lo deriva del id.
     */
    public function getDisplayCodeAttribute(): string
    {
        $code = trim((string) ($this->code ?? ''));

        return $code !== '' ? $code : 'INF-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }
}
