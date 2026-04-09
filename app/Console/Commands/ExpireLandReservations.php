<?php

namespace App\Console\Commands;

use App\Services\Reservations\LandReservationService;
use Illuminate\Console\Command;

class ExpireLandReservations extends Command
{
    protected $signature = 'reservations:expire-lands';
    protected $description = 'Expire pending land reservations that passed their expiration time';

    public function handle(LandReservationService $service): int
    {
        $count = $service->expirePendingReservations();

        $this->info("Expired reservations processed: {$count}");

        return self::SUCCESS;
    }
}
