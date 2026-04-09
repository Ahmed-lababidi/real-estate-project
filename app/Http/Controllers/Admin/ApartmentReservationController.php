<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CancelApartmentReservationRequest;
use App\Http\Requests\Admin\ConfirmApartmentReservationRequest;
use App\Http\Resources\ApartmentReservationResource;
use App\Models\Reservation;
use App\Services\Reservations\ApartmentReservationService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ApartmentReservationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ApartmentReservationService $reservationService
    ) {}

    public function index(Request $request)
    {
        $items = Reservation::query()
            ->with(['apartment.tower.project', 'confirmedBy', 'cancelledBy'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('apartment_id'), fn ($q) => $q->where('apartment_id', $request->apartment_id))
            ->when($request->filled('customer_phone'), fn ($q) => $q->where('customer_phone', 'like', "%{$request->customer_phone}%"))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse([
            'items' => ApartmentReservationResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ], 'Reservations fetched successfully');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['apartment.tower.project', 'confirmedBy', 'cancelledBy']);

        return $this->successResponse(
            new ApartmentReservationResource($reservation),
            'Reservation fetched successfully'
        );
    }

    public function confirm(ConfirmApartmentReservationRequest $request, Reservation $reservation)
    {
        $reservation = $this->reservationService->confirm($reservation, $request->user());

        return $this->successResponse(
            new ApartmentReservationResource($reservation),
            'Reservation confirmed successfully'
        );
    }

    public function cancel(CancelApartmentReservationRequest $request, Reservation $reservation)
    {
        $reservation = $this->reservationService->cancel($reservation, $request->user());

        return $this->successResponse(
            new ApartmentReservationResource($reservation),
            'Reservation cancelled successfully'
        );
    }
}
