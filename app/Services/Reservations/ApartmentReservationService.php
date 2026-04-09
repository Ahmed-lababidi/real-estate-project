<?php

namespace App\Services\Reservations;

use App\Enums\ApartmentStatus;
use App\Enums\ReservationStatus;
use App\Models\Admin;
use App\Models\Apartment;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApartmentReservationService
{
    public function reserve(Apartment $apartment, array $data): Reservation
    {
        return DB::transaction(function () use ($apartment, $data) {
            $lockedApartment = Apartment::query()
                ->whereKey($apartment->id)
                ->lockForUpdate()
                ->firstOrFail();

            // تحقق من الحالة الحالية
            if ($lockedApartment->status !== ApartmentStatus::AVAILABLE) {
                throw new HttpException(422, 'This apartment is not available for reservation.');
            }

            // تحقق من عدم وجود حجز فعّال
            $activeReservationExists = Reservation::query()
                ->where('apartment_id', $lockedApartment->id)
                ->where('status', ReservationStatus::PENDING)
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->exists();

            if ($activeReservationExists) {
                throw new HttpException(422, 'This apartment already has an active reservation.');
            }

            $reservation = Reservation::create([
                'reservation_code' => 'RES-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'apartment_id' => $lockedApartment->id,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_national_id' => $data['customer_national_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => ReservationStatus::PENDING,
                'reserved_at' => now(),
                'expires_at' => now()->addHours(24),
            ]);

            $lockedApartment->update([
                'status' => ApartmentStatus::RESERVED,
            ]);

            return $reservation->fresh(['apartment']);
        });
    }

    public function confirm(Reservation $reservation, Admin $admin): Reservation
    {
        return DB::transaction(function () use ($reservation, $admin) {
            $reservation = Reservation::query()
                ->whereKey($reservation->id)
                ->lockForUpdate()
                ->firstOrFail();

            $apartment = Apartment::query()
                ->whereKey($reservation->apartment_id)
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

            $apartment->update([
                'status' => ApartmentStatus::SOLD,
            ]);

            return $reservation->fresh(['apartment', 'confirmedBy']);
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


    public function cancel(Reservation $reservation, ?Admin $admin = null): Reservation
{
    return DB::transaction(function () use ($reservation, $admin) {
        $reservation = Reservation::query()
            ->whereKey($reservation->id)
            ->lockForUpdate()
            ->firstOrFail();

        $apartment = Apartment::query()
            ->whereKey($reservation->apartment_id)
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

        if ($apartment->status === ApartmentStatus::RESERVED) {
            $apartment->update([
                'status' => ApartmentStatus::AVAILABLE,
            ]);
        }

        return $reservation->fresh(['apartment', 'cancelledBy']);
    });
}
    public function expirePendingReservations(): int
    {
        return DB::transaction(function () {
            $expiredReservations = Reservation::query()
                ->where('status', ReservationStatus::PENDING)
                ->where('expires_at', '<=', now())
                ->lockForUpdate()
                ->get();

            $count = 0;

            foreach ($expiredReservations as $reservation) {
                $apartment = Apartment::query()
                    ->whereKey($reservation->apartment_id)
                    ->lockForUpdate()
                    ->first();

                $reservation->update([
                    'status' => ReservationStatus::EXPIRED,
                ]);

                if ($apartment && $apartment->status === ApartmentStatus::RESERVED) {
                    $apartment->update([
                        'status' => ApartmentStatus::AVAILABLE,
                    ]);
                }

                $count++;
            }

            return $count;
        });
    }
}
