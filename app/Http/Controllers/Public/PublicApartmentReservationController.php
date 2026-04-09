<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreApartmentReservationRequest;
use App\Http\Resources\ApartmentReservationResource;
use App\Models\Apartment;
use App\Services\Reservations\ApartmentReservationService;
use App\Traits\ApiResponse;

class PublicApartmentReservationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ApartmentReservationService $reservationService
    ) {}

    public function store(StoreApartmentReservationRequest $request, Apartment $apartment)
    {
        if (! $apartment->is_active) {
            return $this->errorResponse('This apartment is not available.', 404);
        }

        $reservation = $this->reservationService->reserve(
            $apartment,
            $request->validated()
        );

        return $this->successResponse(
            new ApartmentReservationResource($reservation),
            'Apartment reserved successfully for 24 hours.',
            201
        );
    }
}
