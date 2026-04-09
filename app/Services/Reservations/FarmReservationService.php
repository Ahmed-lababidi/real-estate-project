<?php

namespace App\Services\Reservations;

use App\Enums\FarmStatus;
use App\Enums\ReservationStatus;
use App\Models\Admin;
use App\Models\Farm;
use App\Models\FarmReservation;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FarmReservationService
{
    public function reserve(Farm $farm, array $data): FarmReservation
    {
        return DB::transaction(function () use ($farm, $data) {
            $lockedFarm = Farm::query()
                ->whereKey($farm->id)
                ->lockForUpdate()
                ->firstOrFail();

            // تحقق من الحالة الحالية
            if ($lockedFarm->status !== FarmStatus::AVAILABLE) {
                throw new HttpException(422, 'This farm is not available for reservation.');
            }

            // تحقق من عدم وجود حجز فعّال
            $activeReservationExists = FarmReservation::query()
                ->where('farm_id', $lockedFarm->id)
                ->where('status', ReservationStatus::PENDING)
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->exists();

            if ($activeReservationExists) {
                throw new HttpException(422, 'This farm already has an active reservation.');
            }

            $reservation = FarmReservation::create([
                'reservation_code' => 'RES-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'farm_id' => $lockedFarm->id,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_national_id' => $data['customer_national_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => ReservationStatus::PENDING,
                'reserved_at' => now(),
                'expires_at' => now()->addHours(24),
            ]);

            $lockedFarm->update([
                'status' => FarmStatus::RESERVED,
            ]);

            return $reservation->fresh(['farm']);
        });
    }

    public function confirm(FarmReservation $reservation, Admin $admin): FarmReservation
    {
        return DB::transaction(function () use ($reservation, $admin) {
            $reservation = FarmReservation::query()
                ->whereKey($reservation->id)
                ->lockForUpdate()
                ->firstOrFail();

            $farm = Farm::query()
                ->whereKey($reservation->farm_id)
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

            $farm->update([
                'status' => FarmStatus::SOLD,
            ]);

            return $reservation->fresh(['farm', 'confirmedBy']);
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


    public function cancel(FarmReservation $reservation, ?Admin $admin = null): FarmReservation
{
    return DB::transaction(function () use ($reservation, $admin) {
        $reservation = FarmReservation::query()
            ->whereKey($reservation->id)
            ->lockForUpdate()
            ->firstOrFail();

        $farm = Farm::query()
            ->whereKey($reservation->farm_id)
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

        if ($farm->status === FarmStatus::RESERVED) {
            $farm->update([
                'status' => FarmStatus::AVAILABLE,
            ]);
        }

        return $reservation->fresh(['farm', 'cancelledBy']);
    });
}
    public function expirePendingReservations(): int
    {
        return DB::transaction(function () {
            $expiredReservations = FarmReservation::query()
                ->where('status', ReservationStatus::PENDING)
                ->where('expires_at', '<=', now())
                ->lockForUpdate()
                ->get();

            $count = 0;

            foreach ($expiredReservations as $reservation) {
                $farm = Farm::query()
                    ->whereKey($reservation->farm_id)
                    ->lockForUpdate()
                    ->first();

                $reservation->update([
                    'status' => ReservationStatus::EXPIRED,
                ]);

                if ($farm && $farm->status === FarmStatus::RESERVED) {
                    $farm->update([
                        'status' => FarmStatus::AVAILABLE,
                    ]);
                }

                $count++;
            }

            return $count;
        });
    }
}
