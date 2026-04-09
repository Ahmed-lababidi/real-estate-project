<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTowerCategoryRequest;
use App\Http\Requests\Admin\UpdateTowerCategoryRequest;
use App\Http\Resources\TowerCategoryResource;
use App\Models\TowerCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TowerCategoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $categories = TowerCategory::query()
            ->withCount('towers')
            ->when($request->filled('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse([
            'items' => TowerCategoryResource::collection($categories->items()),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ],
        ], 'Tower categories fetched successfully');
    }

    public function store(StoreTowerCategoryRequest $request)
    {
        $category = TowerCategory::create($request->validated());

        return $this->successResponse(
            new TowerCategoryResource($category->loadCount('towers')),
            'Tower category created successfully',
            201
        );
    }

    public function show(TowerCategory $towerCategory)
    {
        $towerCategory->loadCount('towers');

        return $this->successResponse(
            new TowerCategoryResource($towerCategory),
            'Tower category fetched successfully'
        );
    }

    public function update(UpdateTowerCategoryRequest $request, TowerCategory $towerCategory)
    {
        $towerCategory->update($request->validated());

        return $this->successResponse(
            new TowerCategoryResource($towerCategory->fresh()->loadCount('towers')),
            'Tower category updated successfully'
        );
    }

    public function destroy(TowerCategory $towerCategory)
    {
        if ($towerCategory->towers()->exists()) {
            return $this->errorResponse(
                'Cannot delete this category because it has related towers.',
                422
            );
        }

        $towerCategory->forceDelete();

        return $this->successResponse(null, 'Tower category deleted successfully');
    }
}
