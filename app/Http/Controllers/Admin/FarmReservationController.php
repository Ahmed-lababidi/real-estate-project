<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CancelFarmReservationRequest;
use App\Http\Requests\Admin\ConfirmFarmReservationRequest;
use App\Http\Resources\FarmReservationResource;
use App\Models\FarmReservation;
use App\Services\Reservations\FarmReservationService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class FarmReservationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly FarmReservationService $reservationService
    ) {}

    public function index(Request $request)
    {
        $items = FarmReservation::query()
            ->with(['farm', 'confirmedBy', 'cancelledBy'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('farm_id'), fn($q) => $q->where('farm_id', $request->farm_id))
            ->when($request->filled('customer_phone'), fn($q) => $q->where('customer_phone', 'like', "%{$request->customer_phone}%"))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse([
            'items' => FarmReservationResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ], 'Reservations fetched successfully');
    }

    public function show(FarmReservation $reservation)
    {
        $reservation->load(['farm', 'confirmedBy', 'cancelledBy']);

        return $this->successResponse(
            new FarmReservationResource($reservation),
            'Reservation fetched successfully'
        );
    }

    public function confirm(ConfirmFarmReservationRequest $request, FarmReservation $reservation)
    {
        $reservation = $this->reservationService->confirm($reservation, $request->user());

        return $this->successResponse(
            new FarmReservationResource($reservation),
            'Reservation confirmed successfully'
        );
    }

    public function cancel(CancelFarmReservationRequest $request, FarmReservation $reservation)
    {
        $reservation = $this->reservationService->cancel($reservation, $request->user());

        return $this->successResponse(
            new FarmReservationResource($reservation),
            'Reservation cancelled successfully'
        );
    }
}
