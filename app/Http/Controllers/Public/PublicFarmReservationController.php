<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreFarmReservationRequest;
use App\Http\Resources\FarmReservationResource;
use App\Models\Farm;
use App\Services\Reservations\FarmReservationService;
use App\Traits\ApiResponse;

class PublicFarmReservationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly FarmReservationService $reservationService
    ) {}

    public function store(StoreFarmReservationRequest $request, Farm $farm)
    {
        if (! $farm->is_active) {
            return $this->errorResponse('This farm is not available.', 404);
        }

        $reservation = $this->reservationService->reserve(
            $farm,
            $request->validated()
        );

        return $this->successResponse(
            new FarmReservationResource($reservation),
            'Farm reserved successfully for 24 hours.',
            201
        );
    }
}
