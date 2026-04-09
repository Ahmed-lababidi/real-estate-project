<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreLandReservationRequest;
use App\Http\Resources\LandReservationResource;
use App\Models\Land;
use App\Services\Reservations\LandReservationService;
use App\Traits\ApiResponse;

class PublicLandReservationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly LandReservationService $reservationService
    ) {}

    public function store(StoreLandReservationRequest $request, Land $land)
    {
        if (! $land->is_active) {
            return $this->errorResponse('This land is not available.', 404);
        }

        $reservation = $this->reservationService->reserve(
            $land,
            $request->validated()
        );

        return $this->successResponse(
            new LandReservationResource($reservation),
            'Land reserved successfully for 24 hours.',
            201
        );
    }
}
