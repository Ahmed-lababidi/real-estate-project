<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApartmentResource;
use App\Models\Apartment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PublicApartmentController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $apartments = Apartment::query()
            ->where('is_active', true)
            ->with(['tower.project', 'orientation', 'images', 'reservations'])
            ->when($request->filled('project_id'), fn ($q) =>
                $q->whereHas('tower', fn ($qq) => $qq->where('project_id', $request->project_id))
            )
            ->when($request->filled('tower_id'), fn ($q) => $q->where('tower_id', $request->tower_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('orientation_id'), fn ($q) => $q->where('apartment_orientation_id', $request->orientation_id))
            ->when($request->filled('floor_number'), fn ($q) => $q->where('floor_number', $request->floor_number))
            ->when($request->filled('bedrooms'), fn ($q) => $q->where('bedrooms', $request->bedrooms))
            ->when($request->filled('bathrooms'), fn ($q) => $q->where('bathrooms', $request->bathrooms))
            ->when($request->filled('rooms_number'), fn ($q) => $q->where('rooms_number', $request->rooms_number))
            ->when($request->filled('min_price'), fn ($q) => $q->where('price', '>=', $request->min_price))
            ->when($request->filled('max_price'), fn ($q) => $q->where('price', '<=', $request->max_price))
            ->when($request->filled('min_area'), fn ($q) => $q->where('area', '>=', $request->min_area))
            ->when($request->filled('max_area'), fn ($q) => $q->where('area', '<=', $request->max_area))
            ->when($request->filled('is_featured'), fn ($q) => $q->where('is_featured', $request->boolean('is_featured')))
            ->when($request->filled('search'), fn ($q) =>
                $q->where(function ($qq) use ($request) {
                    $qq->where('name', 'like', "%{$request->search}%")
                        ->orWhere('name_en', 'like', "%{$request->search}%")
                        ->orWhere('unit_number', 'like', "%{$request->search}%")
                        ->orWhere('code', 'like', "%{$request->search}%");
                })
            )
            ->latest()
            ->paginate($request->integer('per_page', 12));

        return $this->successResponse([
            'items' => ApartmentResource::collection($apartments->items()),
            'pagination' => [
                'current_page' => $apartments->currentPage(),
                'last_page' => $apartments->lastPage(),
                'per_page' => $apartments->perPage(),
                'total' => $apartments->total(),
            ],
        ], 'Apartments fetched successfully');
    }

    public function show(Apartment $apartment)
    {
        abort_unless($apartment->is_active, 404);

        $apartment->load(['tower.project', 'orientation', 'images', 'reservations']);

        return $this->successResponse(
            new ApartmentResource($apartment),
            'Apartment fetched successfully'
        );
    }
}
