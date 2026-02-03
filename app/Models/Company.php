<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guard = 'web';

    protected $table = 'companies';

    protected $fillable = [
        'id',
        'name',
        'company_segment_id',
        'status',
    ];

    public function companySegment()
    {
        return $this->belongsTo(CompanySegment::class, 'company_segment_id');
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
