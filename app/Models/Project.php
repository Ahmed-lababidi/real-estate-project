<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'project_category_id',
        'name',
        'name_en',
        'slug',
        'code',
        'description',
        'description_en',
        'location_text',
        'location_text_en',
        'latitude',
        'longitude',
        'starting_price',
        'delivery_date',
        'is_featured',
        'is_active',
        'cover_image',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'starting_price' => 'decimal:2',
        'delivery_date' => 'date',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $project) {
            if (blank($project->slug)) {
                $project->slug = Str::slug($project->name . '-' . uniqid());
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class);
    }

    public function towers()
    {
        return $this->hasMany(Tower::class);
    }

    public function lands()
    {
        return $this->hasMany(Land::class);
    }

    public function landsAvailable()
    {
        return $this->hasMany(Land::class)->where('status', 'available');
    }
    public function landsReserved()
    {
        return $this->hasMany(Land::class)->where('status', 'reserved');
    }
    public function landsSold()
    {
        return $this->hasMany(Land::class)->where('status', 'sold');
    }

        public function farms()
    {
        return $this->hasMany(Farm::class);
    }

    public function farmsAvailable()
    {
        return $this->hasMany(Farm::class)->where('status', 'available');
    }
    public function farmsReserved()
    {
        return $this->hasMany(Farm::class)->where('status', 'reserved');
    }
    public function farmsSold()
    {
        return $this->hasMany(Farm::class)->where('status', 'sold');
    }

    public function facilities()
    {
        return $this->hasMany(Facility::class);
    }
}
