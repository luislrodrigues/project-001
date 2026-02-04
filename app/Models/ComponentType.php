<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComponentType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'component_types';

    Protected $fillable = [
        'id',
        'name',
        'status',
    ];

    public function components()
    {
        return $this->hasMany(Component::class);
    }
    public function componentTypeFields()
    {
        return $this->hasMany(ComponentTypeField::class)->ordered();
    }
}
