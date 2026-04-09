<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreApartmentOrientationRequest;
use App\Http\Requests\Admin\UpdateApartmentOrientationRequest;
use App\Http\Resources\ApartmentOrientationResource;
use App\Models\ApartmentOrientation;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ApartmentOrientationController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $items = ApartmentOrientation::query()
            ->withCount('apartments')
            ->when($request->filled('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse([
            'items' => ApartmentOrientationResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ], 'Apartment orientations fetched successfully');
    }

    public function store(StoreApartmentOrientationRequest $request)
    {
        $item = ApartmentOrientation::create($request->validated());

        return $this->successResponse(
            new ApartmentOrientationResource($item->loadCount('apartments')),
            'Apartment orientation created successfully',
            201
        );
    }

    public function show(ApartmentOrientation $apartmentOrientation)
    {
        $apartmentOrientation->loadCount('apartments');

        return $this->successResponse(
            new ApartmentOrientationResource($apartmentOrientation),
            'Apartment orientation fetched successfully'
        );
    }

    public function update(UpdateApartmentOrientationRequest $request, ApartmentOrientation $apartmentOrientation)
    {
        $apartmentOrientation->update($request->validated());

        return $this->successResponse(
            new ApartmentOrientationResource($apartmentOrientation->fresh()->loadCount('apartments')),
            'Apartment orientation updated successfully'
        );
    }

    public function destroy(ApartmentOrientation $apartmentOrientation)
    {
        if ($apartmentOrientation->apartments()->exists()) {
            return $this->errorResponse(
                'Cannot delete this orientation because it has related apartments.',
                422
            );
        }

        $apartmentOrientation->delete();

        return $this->successResponse(null, 'Apartment orientation deleted successfully');
    }
}
