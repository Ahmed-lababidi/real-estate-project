<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CancelLandReservationRequest;
use App\Http\Requests\Admin\ConfirmLandReservationRequest;
use App\Http\Resources\LandReservationResource;
use App\Models\LandReservation;
use App\Services\Reservations\LandReservationService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class LandReservationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly LandReservationService $reservationService
    ) {}

    public function index(Request $request)
    {
        $items = LandReservation::query()
            ->with(['land', 'confirmedBy', 'cancelledBy'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('land_id'), fn($q) => $q->where('land_id', $request->land_id))
            ->when($request->filled('customer_phone'), fn($q) => $q->where('customer_phone', 'like', "%{$request->customer_phone}%"))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse([
            'items' => LandReservationResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ], 'Reservations fetched successfully');
    }

    public function show(LandReservation $reservation)
    {
        $reservation->load(['land', 'confirmedBy', 'cancelledBy']);

        return $this->successResponse(
            new LandReservationResource($reservation),
            'Reservation fetched successfully'
        );
    }

    public function confirm(ConfirmLandReservationRequest $request, LandReservation $reservation)
    {
        $reservation = $this->reservationService->confirm($reservation, $request->user());

        return $this->successResponse(
            new LandReservationResource($reservation),
            'Reservation confirmed successfully'
        );
    }

    public function cancel(CancelLandReservationRequest $request, LandReservation $reservation)
    {
        $reservation = $this->reservationService->cancel($reservation, $request->user());

        return $this->successResponse(
            new LandReservationResource($reservation),
            'Reservation cancelled successfully'
        );
    }
}
