<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tower extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'project_id',
        'tower_category_id',
        'name',
        'name_en',
        'slug',
        'description',
        'description_en',
        'number_of_floors',
        'location_within_project',
        'location_within_project_en',
        'is_active',
        'cover_image',
        'model_3d_path'
    ];

    protected $casts = [
        'number_of_floors' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $tower) {
            if (blank($tower->slug)) {
                $tower->slug = Str::slug($tower->name . '-' . uniqid());
            }
        });
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function category()
    {
        return $this->belongsTo(TowerCategory::class, 'tower_category_id');
    }

    public function images()
    {
        return $this->hasMany(TowerImage::class);
    }

    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }

    public function apartmentsAvailable()
    {
        return $this->hasMany(Apartment::class)->where('status', 'available');
    }

    public function apartmentsReserved()
    {
        return $this->hasMany(Apartment::class)->where('status', 'reserved');
    }

    public function apartmentsSold()
    {
        return $this->hasMany(Apartment::class)->where('status', 'sold');
    }
}
