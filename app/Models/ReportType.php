<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'report_types';

    protected $fillable = [
        'code',
        'name',
        'consecutivo_prefix',
        'icon',
        'color',
        'pdf_view',
        'display_order',
        'status',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'display_order' => 'integer',
    ];
}
