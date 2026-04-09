<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Http\Requests\Admin\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ImageService $imageService
    ) {}

    public function index(Request $request)
    {
        $projects = Project::query()
            ->with(['category', 'images'])
            ->withCount('towers')
            ->when($request->filled('project_category_id'), fn ($q) =>
                $q->where('project_category_id', $request->project_category_id)
            )
            ->when($request->filled('is_active'), fn ($q) =>
                $q->where('is_active', $request->boolean('is_active'))
            )
            ->when($request->filled('is_featured'), fn ($q) =>
                $q->where('is_featured', $request->boolean('is_featured'))
            )
            ->when($request->filled('search'), fn ($q) =>
                $q->where(function ($qq) use ($request) {
                    $qq->where('name', 'like', "%{$request->search}%")
                        ->orWhere('code', 'like', "%{$request->search}%")
                        ->orWhere('location_text', 'like', "%{$request->search}%");
                })
            )
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse([
            'items' => ProjectResource::collection($projects->items()),
            'pagination' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
            ],
        ], 'Projects fetched successfully');
    }

    public function store(StoreProjectRequest $request)
    {
        $project = DB::transaction(function () use ($request) {
            $data = $request->validated();

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $this->imageService->upload(
                    $request->file('cover_image'),
                    'projects/covers'
                );
            }

            $project = Project::create($data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $this->imageService->upload($image, 'projects/gallery');

                    $project->images()->create([
                        'path' => $path,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            return $project->load(['category', 'images'])->loadCount('towers');
        });

        return $this->successResponse(
            new ProjectResource($project),
            'Project created successfully',
            201
        );
    }

    public function show(Project $project)
    {
        $project->load(['category', 'images', 'towers.category'])->loadCount('towers');

        return $this->successResponse(
            new ProjectResource($project),
            'Project fetched successfully'
        );
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project = DB::transaction(function () use ($request, $project) {
            $data = $request->validated();

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $this->imageService->upload(
                    $request->file('cover_image'),
                    'projects/covers',
                    $project->cover_image
                );
            }

            $project->update($data);

            if ($request->filled('deleted_image_ids')) {
                $imagesToDelete = $project->images()
                    ->whereIn('id', $request->deleted_image_ids)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    $this->imageService->delete($image->path, $image->disk);
                    $image->delete();
                }
            }

            if ($request->hasFile('images')) {
                $lastSort = (int) $project->images()->max('sort_order');

                foreach ($request->file('images') as $index => $image) {
                    $path = $this->imageService->upload($image, 'projects/gallery');

                    $project->images()->create([
                        'path' => $path,
                        'sort_order' => $lastSort + $index + 1,
                    ]);
                }
            }

            return $project->fresh()->load(['category', 'images'])->loadCount('towers');
        });

        return $this->successResponse(
            new ProjectResource($project),
            'Project updated successfully'
        );
    }

    public function destroy(Project $project)
    {
        DB::transaction(function () use ($project) {
            $this->imageService->delete($project->cover_image);

            foreach ($project->images as $image) {
                $this->imageService->delete($image->path, $image->disk);
                $image->delete();
            }

            $project->forceDelete();
        });

        return $this->successResponse(null, 'Project deleted successfully');
    }
}
