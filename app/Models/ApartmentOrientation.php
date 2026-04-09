<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartmentOrientation extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'slug',
        'description',
        'description_en',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }
}
