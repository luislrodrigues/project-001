<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanySegment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'company_segments';

    protected $fillable = [
        'id',
        'name',
        'status',
    ];

    public function companies()
    {
        return $this->hasMany(Company::class, 'company_segment_id');
    }
}
