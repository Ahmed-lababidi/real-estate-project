<?php

namespace App\Services\Reservations;

use App\Enums\LandStatus;
use App\Enums\ReservationStatus;
use App\Models\Admin;
use App\Models\Land;
use App\Models\LandReservation;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LandReservationService
{
    public function reserve(Land $land, array $data): LandReservation
    {
        return DB::transaction(function () use ($land, $data) {
            $lockedLand = Land::query()
                ->whereKey($land->id)
                ->lockForUpdate()
                ->firstOrFail();

            // تحقق من الحالة الحالية
            if ($lockedLand->status !== LandStatus::AVAILABLE) {
                throw new HttpException(422, 'This land is not available for reservation.');
            }

            // تحقق من عدم وجود حجز فعّال
            $activeReservationExists = LandReservation::query()
                ->where('land_id', $lockedLand->id)
                ->where('status', ReservationStatus::PENDING)
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->exists();

            if ($activeReservationExists) {
                throw new HttpException(422, 'This land already has an active reservation.');
            }

            $reservation = LandReservation::create([
                'reservation_code' => 'RES-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'land_id' => $lockedLand->id,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_national_id' => $data['customer_national_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => ReservationStatus::PENDING,
                'reserved_at' => now(),
                'expires_at' => now()->addHours(24),
            ]);

            $lockedLand->update([
                'status' => LandStatus::RESERVED,
            ]);

            return $reservation->fresh(['land']);
        });
    }

    public function confirm(LandReservation $reservation, Admin $admin): LandReservation
    {
        return DB::transaction(function () use ($reservation, $admin) {
            $reservation = LandReservation::query()
                ->whereKey($reservation->id)
                ->lockForUpdate()
                ->firstOrFail();

            $land = Land::query()
                ->whereKey($reservation->land_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($reservation->status !== ReservationStatus::PENDING) {
                throw new HttpException(422, 'Only pending reservations can be confirmed.');
            }

            if ($reservation->expires_at && $reservation->expires_at->isPast()) {
                throw new HttpException(422, 'This reservation has already expired.');
            }

            $reservation->update([
                'status' => ReservationStatus::CONFIRMED,
                'confirmed_at' => now(),
                'confirmed_by_admin_id' => $admin->id,
            ]);

            $land->update([
                'status' => LandStatus::SOLD,
            ]);

            return $reservation->fresh(['land', 'confirmedBy']);
        });
    }

    // public function cancel(Reservation $reservation, ?Admin $admin = null): Reservation
    // {
    //     return DB::transaction(function () use ($reservation, $admin) {
    //         $reservation = Reservation::query()
    //             ->whereKey($reservation->id)
    //             ->lockForUpdate()
    //             ->firstOrFail();

    //         $apartment = Apartment::query()
    //             ->whereKey($reservation->apartment_id)
    //             ->lockForUpdate()
    //             ->firstOrFail();

    //         if (! in_array($reservation->status, [
    //             ReservationStatus::PENDING,
    //             ReservationStatus::CONFIRMED,
    //         ])) {
    //             throw new HttpException(422, 'This reservation cannot be cancelled.');
    //         }

    //         $reservation->update([
    //             'status' => ReservationStatus::CANCELLED,
    //             'cancelled_at' => now(),
    //             'cancelled_by_admin_id' => $admin?->id,
    //         ]);

    //         if ($apartment->status !== ApartmentStatus::SOLD || $reservation->status === ReservationStatus::PENDING) {
    //             $apartment->update([
    //                 'status' => ApartmentStatus::AVAILABLE,
    //             ]);
    //         }

    //         return $reservation->fresh(['apartment', 'cancelledBy']);
    //     });
    // }


    public function cancel(LandReservation $reservation, ?Admin $admin = null): LandReservation
    {
        return DB::transaction(function () use ($reservation, $admin) {
            $reservation = LandReservation::query()
                ->whereKey($reservation->id)
                ->lockForUpdate()
                ->firstOrFail();

            $land = Land::query()
                ->whereKey($reservation->land_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($reservation->status !== ReservationStatus::PENDING) {
                throw new HttpException(422, 'Only pending reservations can be cancelled.');
            }

            $reservation->update([
                'status' => ReservationStatus::CANCELLED,
                'cancelled_at' => now(),
                'cancelled_by_admin_id' => $admin?->id,
            ]);

            if ($land->status === LandStatus::RESERVED) {
                $land->update([
                    'status' => LandStatus::AVAILABLE,
                ]);
            }

            return $reservation->fresh(['land', 'cancelledBy']);
        });
    }
    public function expirePendingReservations(): int
    {
        return DB::transaction(function () {
            $expiredReservations = LandReservation::query()
                ->where('status', ReservationStatus::PENDING)
                ->where('expires_at', '<=', now())
                ->lockForUpdate()
                ->get();

            $count = 0;

            foreach ($expiredReservations as $reservation) {
                $land = Land::query()
                    ->whereKey($reservation->land_id)
                    ->lockForUpdate()
                    ->first();

                $reservation->update([
                    'status' => ReservationStatus::EXPIRED,
                ]);

                if ($land && $land->status === LandStatus::RESERVED) {
                    $land->update([
                        'status' => LandStatus::AVAILABLE,
                    ]);
                }

                $count++;
            }

            return $count;
        });
    }
}
