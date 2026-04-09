<?php

namespace App\Console\Commands;

use App\Services\Reservations\FarmReservationService;
use Illuminate\Console\Command;

class ExpireFarmReservations extends Command
{
    protected $signature = 'reservations:expire-farms';
    protected $description = 'Expire pending farm reservations that passed their expiration time';

    public function handle(FarmReservationService $service): int
    {
        $count = $service->expirePendingReservations();

        $this->info("Expired reservations processed: {$count}");

        return self::SUCCESS;
    }
}
