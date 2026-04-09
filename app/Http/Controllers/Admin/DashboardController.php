<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApartmentStatus;
use App\Enums\FarmStatus;
use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Reservation;
use App\Models\Banner;
use App\Models\ContactRequest;
use App\Models\Farm;
use App\Models\FarmReservation;
use App\Models\Project;
use App\Models\Tower;
use App\Traits\ApiResponse;

class DashboardController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $stats = [
            'projects_count' => Project::count(),
            'towers_count' => Tower::count(),
            'apartments_count' => Apartment::count(),
            'farms_count' => Farm::count(),

            'available_apartments_count' => Apartment::where('status', ApartmentStatus::AVAILABLE)->count(),
            'reserved_apartments_count' => Apartment::where('status', ApartmentStatus::RESERVED)->count(),
            'sold_apartments_count' => Apartment::where('status', ApartmentStatus::SOLD)->count(),

            'available_farms_count' => Farm::where('status', FarmStatus::AVAILABLE)->count(),
            'reserved_farms_count' => Farm::where('status', FarmStatus::RESERVED)->count(),
            'sold_farms_count' => Farm::where('status', FarmStatus::SOLD)->count(),

            'pending_reservations_count' => Reservation::where('status', ReservationStatus::PENDING)->count(),
            'confirmed_reservations_count' => Reservation::where('status', ReservationStatus::CONFIRMED)->count(),
            'expired_reservations_count' => Reservation::where('status', ReservationStatus::EXPIRED)->count(),
            'cancelled_reservations_count' => Reservation::where('status', ReservationStatus::CANCELLED)->count(),

            'pending_farm_reservations_count' => FarmReservation::where('status', ReservationStatus::PENDING)->count(),
            'confirmed_farm_reservations_count' => FarmReservation::where('status', ReservationStatus::CONFIRMED)->count(),
            'expired_farm_reservations_count' => FarmReservation::where('status', ReservationStatus::EXPIRED)->count(),
            'cancelled_farm_reservations_count' => FarmReservation::where('status', ReservationStatus::CANCELLED)->count(),

            'new_leads_count' => ContactRequest::where('status', 'new')->count(),
            'contacted_leads_count' => ContactRequest::where('status', 'contacted')->count(),
            'closed_leads_count' => ContactRequest::where('status', 'closed')->count(),

            'active_banners_count' => Banner::where('is_active', true)->count(),
        ];

        $latestReservations = Reservation::query()
            ->with(['apartment.tower.project'])
            ->latest()
            ->take(10)
            ->get();

        $latestFarmReservations = FarmReservation::query()
            ->with(['farm'])
            ->latest()
            ->take(10)
            ->get();

        $latestLeads = ContactRequest::query()
            ->with(['project', 'tower', 'apartment', 'farm'])
            ->latest()
            ->take(10)
            ->get();

        return $this->successResponse([
            'stats' => $stats,
            'latest_reservations' => \App\Http\Resources\ApartmentReservationResource::collection($latestReservations),
            'latest_farm_reservations' => \App\Http\Resources\FarmReservationResource::collection($latestFarmReservations),
            'latest_leads' => \App\Http\Resources\ContactRequestResource::collection($latestLeads),
        ], 'Dashboard data fetched successfully');
    }
}
