<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectCategoryRequest;
use App\Http\Requests\Admin\UpdateProjectCategoryRequest;
use App\Http\Resources\ProjectCategoryResource;
use App\Models\ProjectCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProjectCategoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $categories = ProjectCategory::query()
            ->withCount('projects')
            ->when($request->filled('is_active'), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse([
            'items' => ProjectCategoryResource::collection($categories->items()),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ],
        ], 'Project categories fetched successfully');
    }

    public function store(StoreProjectCategoryRequest $request)
    {
        $category = ProjectCategory::create($request->validated());

        return $this->successResponse(
            new ProjectCategoryResource($category->fresh()->loadCount('projects')),
            'Project category created successfully',
            201
        );
    }

    public function show(ProjectCategory $projectCategory)
    {
        $projectCategory->loadCount('projects');

        return $this->successResponse(
            new ProjectCategoryResource($projectCategory),
            'Project category fetched successfully'
        );
    }

    public function update(UpdateProjectCategoryRequest $request, ProjectCategory $projectCategory)
    {
        $projectCategory->update($request->validated());

        return $this->successResponse(
            new ProjectCategoryResource($projectCategory->fresh()->loadCount('projects')),
            'Project category updated successfully'
        );
    }

    public function destroy(ProjectCategory $projectCategory)
    {
        if ($projectCategory->projects()->exists()) {
            return $this->errorResponse(
                'Cannot delete this category because it has related projects.',
                422
            );
        }

        $projectCategory->forceDelete();

        return $this->successResponse(null, 'Project category deleted successfully');
    }
}
