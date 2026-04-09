<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\FarmResource;
use App\Models\Farm;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PublicFarmController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $farms = Farm::query()
            ->where('is_active', true)
            ->with(['project', 'images', 'reservations'])
            ->when(
                $request->filled('project_id'),
                fn($q) =>
                $q->whereHas('tower', fn($qq) => $qq->where('project_id', $request->project_id))
            )
            ->when($request->filled('project_id'), fn($q) => $q->where('project_id', $request->project_id))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('rooms_number'), fn($q) => $q->where('rooms_number', $request->rooms_number))
            ->when($request->filled('min_price'), fn($q) => $q->where('price', '>=', $request->min_price))
            ->when($request->filled('max_price'), fn($q) => $q->where('price', '<=', $request->max_price))
            ->when($request->filled('min_area'), fn($q) => $q->where('area', '>=', $request->min_area))
            ->when($request->filled('max_area'), fn($q) => $q->where('area', '<=', $request->max_area))
            ->when(
                $request->filled('search'),
                fn($q) =>
                $q->where(function ($qq) use ($request) {
                    $qq->where('name', 'like', "%{$request->search}%")
                        ->orWhere('name_en', 'like', "%{$request->search}%");
                })
            )
            ->latest()
            ->paginate($request->integer('per_page', 12));

        return $this->successResponse([
            'items' => FarmResource::collection($farms->items()),
            'pagination' => [
                'current_page' => $farms->currentPage(),
                'last_page' => $farms->lastPage(),
                'per_page' => $farms->perPage(),
                'total' => $farms->total(),
            ],
        ], 'Farms fetched successfully');
    }

    public function show(Farm $farm)
    {
        abort_unless($farm->is_active, 404);

        $farm->load(['project', 'images', 'reservations']);

        return $this->successResponse(
            new FarmResource($farm),
            'Farm fetched successfully'
        );
    }
}
