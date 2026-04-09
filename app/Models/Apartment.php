<?php

namespace App\Models;

use App\Enums\ApartmentStatus;
use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Apartment extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'tower_id',
        'apartment_orientation_id',
        'name',
        'name_en',
        'slug',
        'unit_number',
        'code',
        'description',
        'description_en',
        'floor_number',
        'bedrooms',
        'bathrooms',
        'rooms_number',
        'area',
        'price',
        'status',
        // 'reservation_expires_at',
        // 'has_balcony',
        // 'has_parking',
        'is_active',
        'is_featured',
        'cover_image',
    ];

    protected $casts = [
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'rooms_number' => 'integer',
        'floor_number' => 'integer',
        'area' => 'decimal:2',
        'price' => 'decimal:2',
        'status' => ApartmentStatus::class,
        // 'reservation_expires_at' => 'datetime',
        // 'has_balcony' => 'boolean',
        // 'has_parking' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function tower()
    {
        return $this->belongsTo(Tower::class);
    }

    public function orientation()
    {
        return $this->belongsTo(ApartmentOrientation::class, 'apartment_orientation_id');
    }

    public function images()
    {
        return $this->hasMany(ApartmentImage::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function activeReservation()
    {
        return $this->hasMany(Reservation::class)
            ->where('status', ReservationStatus::PENDING)
            ->where('expires_at', '>', now());
    }
}
