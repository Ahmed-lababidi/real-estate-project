<?php

namespace App\Console\Commands;

use App\Services\Reservations\ApartmentReservationService;
use Illuminate\Console\Command;

class ExpireApartmentReservations extends Command
{
    protected $signature = 'reservations:expire-apartments';
    protected $description = 'Expire pending apartment reservations that passed their expiration time';

    public function handle(ApartmentReservationService $service): int
    {
        $count = $service->expirePendingReservations();

        $this->info("Expired reservations processed: {$count}");

        return self::SUCCESS;
    }
}
