<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\LandResource;
use App\Models\Land;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PublicLandController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $lands = Land::query()
            ->where('is_active', true)
            ->with(['project', 'images', 'reservations'])
            ->when(
                $request->filled('project_id'),
                fn($q) =>
                $q->whereHas('tower', fn($qq) => $qq->where('project_id', $request->project_id))
            )
            ->when($request->filled('project_id'), fn($q) => $q->where('project_id', $request->project_id))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('min_price'), fn($q) => $q->where('price', '>=', $request->min_price))
            ->when($request->filled('max_price'), fn($q) => $q->where('price', '<=', $request->max_price))
            ->when($request->filled('min_area'), fn($q) => $q->where('area', '>=', $request->min_area))
            ->when($request->filled('max_area'), fn($q) => $q->where('area', '<=', $request->max_area))
            ->when(
                $request->filled('search'),
                fn($q) =>
                $q->where(function ($qq) use ($request) {
                    $qq->where('name', 'like', "%{$request->search}%")
                        ->orWhere('name_en', 'like', "%{$request->search}%")
                        ->orWhere('type', 'like', "%{$request->search}%");
                })
            )
            ->latest()
            ->paginate($request->integer('per_page', 12));

        return $this->successResponse([
            'items' => LandResource::collection($lands->items()),
            'pagination' => [
                'current_page' => $lands->currentPage(),
                'last_page' => $lands->lastPage(),
                'per_page' => $lands->perPage(),
                'total' => $lands->total(),
            ],
        ], 'Lands fetched successfully');
    }

    public function show(Land $land)
    {
        abort_unless($land->is_active, 404);

        $land->load(['project', 'images', 'reservations']);

        return $this->successResponse(
            new LandResource($land),
            'Land fetched successfully'
        );
    }
}
