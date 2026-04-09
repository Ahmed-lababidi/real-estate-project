<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Model;

class FarmReservation extends Model
{
    protected $fillable = [
        'reservation_code',
        'farm_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_national_id',
        'notes',
        'status',
        'reserved_at',
        'expires_at',
        'confirmed_at',
        'cancelled_at',
        'confirmed_by_admin_id',
        'cancelled_by_admin_id',
    ];

    protected $casts = [
        'status' => ReservationStatus::class,
        'reserved_at' => 'datetime',
        'expires_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(Admin::class, 'confirmed_by_admin_id');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(Admin::class, 'cancelled_by_admin_id');
    }
}
