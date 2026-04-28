<?php

namespace App\Models;

use App\Enums\FarmStatus;
use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Farm extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'name_en',
        'project_id',
        'description',
        'description_en',
        'area',
        'rooms_number',
        'bathrooms',
        'has_parking',
        'has_pool',
        'has_garden',
        'price',
        'location_within_project',
        'location_within_project_en',
        'cover_image',
        'status',
        'is_active'
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'status' => FarmStatus::class,
        'rooms_number' => 'integer',
        'bathrooms' => 'integer',
        'has_parking' => 'boolean',
        'has_pool' => 'boolean',
        'has_garden' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function images()
    {
        return $this->hasMany(FarmImage::class);
    }

    public function reservations()
    {
        return $this->hasMany(FarmReservation::class);
    }

    public function activeReservation()
    {
        return $this->hasMany(FarmReservation::class)
            ->where('status', ReservationStatus::PENDING)
            ->where('expires_at', '>', now());
    }
}
