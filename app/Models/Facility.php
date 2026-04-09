<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Facility extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'name_en',
        'project_id',
        'description',
        'description_en',
        'area',
        'cover_image',
        'location_within_project',
        'location_within_project_en',
        'type',
        'is_active',
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function images()
    {
        return $this->hasMany(FacilityImage::class);
    }
}
