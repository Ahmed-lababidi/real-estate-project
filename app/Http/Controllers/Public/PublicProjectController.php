<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectDetailesResource;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PublicProjectController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $projects = Project::query()
            ->where('is_active', true)
            ->with([
                'category:id,name,name_en,slug',
                'images:id,path',
                'towers',
                'lands',
                'farms',
            ])
            ->withCount('towers')
            ->when(
                $request->filled('project_category_id'),
                fn($q) =>
                $q->where('project_category_id', $request->project_category_id)
            )
            ->when(
                $request->filled('is_featured'),
                fn($q) =>
                $q->where('is_featured', $request->boolean('is_featured'))
            )
            ->when(
                $request->filled('search'),
                fn($q) =>
                $q->where(function ($qq) use ($request) {
                    $qq->where('name', 'like', "%{$request->search}%")
                        ->orWhere('name_en', 'like', "%{$request->search}%")
                        ->orWhere('location_text', 'like', "%{$request->search}%");
                })
            )
            ->latest()
            ->paginate($request->integer('per_page', 12));

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

    // public function show(Project $project)
    // {
    //     abort_unless($project->is_active, 404);

    //     $project->load([
    //         'category',
    //         'images',
    //         'towers' => fn($q) => $q->where('is_active', true)->with(['category', 'images'])->withCount('apartments'),
    //         'lands' => fn($q) => $q->where('is_active', true)->with(['images']),
    //         'farms' => fn($q) => $q->where('is_active', true)->with(['images']),
    //     ])->loadCount('towers');

    //     return $this->successResponse(
    //         new ProjectResource($project),
    //         'Project fetched successfully'
    //     );
    // }

    public function show(Project $project)
    {
        abort_unless($project->is_active, 404);

        $project->load([
            'category',
            'images',

            'towers' => fn($q) => $q
                ->where('is_active', true)
                ->select([
                    'id',
                    'project_id',
                    'name',
                    'name_en',
                    'description',
                    'description_en',
                    'cover_image',
                ])
                ->latest()
                ->take(3),

            'lands' => fn($q) => $q
                ->where('is_active', true)
                ->select([
                    'id',
                    'project_id',
                    'name',
                    'name_en',
                    'description',
                    'description_en',
                    'cover_image',
                ])
                ->latest()
                ->take(3),

            'farms' => fn($q) => $q
                ->where('is_active', true)
                ->select([
                    'id',
                    'project_id',
                    'name',
                    'name_en',
                    'description',
                    'description_en',
                    'cover_image',
                ])
                ->latest()
                ->take(3),
        ])->loadCount([
            'towers' => fn($q) => $q->where('is_active', true),
            'lands' => fn($q) => $q->where('is_active', true),
            'landsAvailable',
            'landsReserved',
            'landsSold',
            'farms' => fn($q) => $q->where('is_active', true),
            'farmsAvailable',
            'farmsReserved',
            'farmsSold',
        ]);

        return $this->successResponse(
            new ProjectDetailesResource($project),
            'Project fetched successfully'
        );
    }
}
