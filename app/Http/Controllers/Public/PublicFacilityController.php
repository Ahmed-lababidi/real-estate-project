<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use App\Traits\ApiResponse;

class PublicFacilityController extends Controller
{
        use ApiResponse;

    public function index(Request $request)
    {
        $facilities = Facility::query()
            ->where('is_active', true)
            ->with(['project', 'images'])
            ->when($request->filled('project_id'), fn ($q) =>
                $q->where('project_id', $request->project_id)
            )
            ->when($request->filled('project_id'), fn ($q) => $q->where('project_id', $request->project_id))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('min_area'), fn ($q) => $q->where('area', '>=', $request->min_area))
            ->when($request->filled('max_area'), fn ($q) => $q->where('area', '<=', $request->max_area))
            ->when($request->filled('search'), fn ($q) =>
                $q->where(function ($qq) use ($request) {
                    $qq->where('name', 'like', "%{$request->search}%")
                        ->orWhere('name_en', 'like', "%{$request->search}%")
                        ->orWhere('location_within_project', 'like', "%{$request->search}%")
                        ->orWhere('location_within_project_en', 'like', "%{$request->search}%");
                })
            )
            ->latest()
            ->paginate($request->integer('per_page', 12));

        return $this->successResponse([
            'items' => FacilityResource::collection($facilities->items()),
            'pagination' => [
                'current_page' => $facilities->currentPage(),
                'last_page' => $facilities->lastPage(),
                'per_page' => $facilities->perPage(),
                'total' => $facilities->total(),
            ],
        ], 'Facilities fetched successfully');
    }

    public function show(Facility $facility)
    {
        abort_unless($facility->is_active, 404);

        $facility->load(['project', 'images']);

        return $this->successResponse(
            new FacilityResource($facility),
            'Facility fetched successfully'
        );
    }
}
