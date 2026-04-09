<?php

namespace App\Models;

use App\Enums\LandStatus;
use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Land extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'name_en',
        'project_id',
        'description',
        'description_en',
        'area',
        'price',
        'type',
        'status',
        'location_within_project',
        'location_within_project_en',
        'cover_image',
        'is_active'
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'status' => LandStatus::class,
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function images()
    {
        return $this->hasMany(LandImage::class);
    }

    public function reservations()
    {
        return $this->hasMany(LandReservation::class);
    }

    public function activeReservation()
    {
        return $this->hasMany(LandReservation::class)
            ->where('status', ReservationStatus::PENDING)
            ->where('expires_at', '>', now());
    }
}
