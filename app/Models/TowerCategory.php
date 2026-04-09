<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TowerCategory extends Model
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

    public function towers()
    {
        return $this->hasMany(Tower::class);
    }
}
