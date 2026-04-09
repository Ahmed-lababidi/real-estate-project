<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\TowerResource;
use App\Models\Tower;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PublicTowerController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $towers = Tower::query()
            ->where('is_active', true)
            ->with([
                'project:id,name,name_en,slug',
                'category:id,name,name_en,slug',
                'images',
            ])
            ->withCount('apartments')
            ->when(
                $request->filled('project_id'),
                fn($q) =>
                $q->where('project_id', $request->project_id)
            )
            ->when(
                $request->filled('tower_category_id'),
                fn($q) =>
                $q->where('tower_category_id', $request->tower_category_id)
            )
            ->when(
                $request->filled('search'),
                fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('name_en', 'like', "%{$request->search}%")
            )
            ->latest()
            ->paginate($request->integer('per_page', 12));

        return $this->successResponse([
            'items' => TowerResource::collection($towers->items()),
            'pagination' => [
                'current_page' => $towers->currentPage(),
                'last_page' => $towers->lastPage(),
                'per_page' => $towers->perPage(),
                'total' => $towers->total(),
            ],
        ], 'Towers fetched successfully');
    }

    public function show(Tower $tower)
    {
        abort_unless($tower->is_active, 404);

        $tower->load(['project', 'category', 'images'])->loadCount('apartments');

        return $this->successResponse(
            new TowerResource($tower),
            'Tower fetched successfully'
        );
    }
}
