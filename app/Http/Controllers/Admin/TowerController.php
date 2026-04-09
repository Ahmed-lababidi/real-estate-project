<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTowerRequest;
use App\Http\Requests\Admin\UpdateTowerRequest;
use App\Http\Resources\TowerResource;
use App\Models\Tower;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TowerController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ImageService $imageService
    ) {}

    public function index(Request $request)
    {
        $towers = Tower::query()
            ->with(['project', 'category', 'images'])
            ->withCount('apartments')
            ->when($request->filled('project_id'), fn ($q) =>
                $q->where('project_id', $request->project_id)
            )
            ->when($request->filled('tower_category_id'), fn ($q) =>
                $q->where('tower_category_id', $request->tower_category_id)
            )
            ->when($request->filled('is_active'), fn ($q) =>
                $q->where('is_active', $request->boolean('is_active'))
            )
            ->when($request->filled('search'), fn ($q) =>
                $q->where(function ($qq) use ($request) {
                    $qq->where('name', 'like', "%{$request->search}%")
                        ->orWhere('name_en', 'like', "%{$request->search}%")
                        ->orWhere('description', 'like', "%{$request->search}%")
                        ->orWhere('description_en', 'like', "%{$request->search}%")
                        ->orWhere('location_within_project', 'like', "%{$request->search}%")
                        ->orWhere('location_within_project_en', 'like', "%{$request->search}%");
                })
            )
            ->latest()
            ->paginate($request->integer('per_page', 15));

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

    public function store(StoreTowerRequest $request)
    {
        $tower = DB::transaction(function () use ($request) {
            $data = $request->validated();

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $this->imageService->upload(
                    $request->file('cover_image'),
                    'towers/covers'
                );
            }

            if ($request->hasFile('model_3d')) {
                $data['model_3d_path'] = $request->file('model_3d')->store('towers/3d_models', 'public');
            }

            $tower = Tower::create($data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $this->imageService->upload($image, 'towers/gallery');

                    $tower->images()->create([
                        'path' => $path,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            return $tower->load(['project', 'category', 'images'])->loadCount('apartments');
        });

        return $this->successResponse(
            new TowerResource($tower),
            'Tower created successfully',
            201
        );
    }

    public function show(Tower $tower)
    {
        $tower->load(['project', 'category', 'images'])->loadCount('apartments');

        return $this->successResponse(
            new TowerResource($tower),
            'Tower fetched successfully'
        );
    }

    public function update(UpdateTowerRequest $request, Tower $tower)
    {
        $tower = DB::transaction(function () use ($request, $tower) {
            $data = $request->validated();

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $this->imageService->upload(
                    $request->file('cover_image'),
                    'towers/covers',
                    $tower->cover_image
                );
            }

            $tower->update($data);

            if ($request->filled('deleted_image_ids')) {
                $imagesToDelete = $tower->images()
                    ->whereIn('id', $request->deleted_image_ids)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    $this->imageService->delete($image->path, $image->disk);
                    $image->delete();
                }
            }

            if ($request->hasFile('images')) {
                $lastSort = (int) $tower->images()->max('sort_order');

                foreach ($request->file('images') as $index => $image) {
                    $path = $this->imageService->upload($image, 'towers/gallery');

                    $tower->images()->create([
                        'path' => $path,
                        'sort_order' => $lastSort + $index + 1,
                    ]);
                }
            }

            return $tower->fresh()->load(['project', 'category', 'images'])->loadCount('apartments');
        });

        return $this->successResponse(
            new TowerResource($tower),
            'Tower updated successfully'
        );
    }

    public function destroy(Tower $tower)
    {
        DB::transaction(function () use ($tower) {
            $this->imageService->delete($tower->cover_image);

            foreach ($tower->images as $image) {
                $this->imageService->delete($image->path, $image->disk);
                $image->delete();
            }

            $tower->forceDelete();
        });

        return $this->successResponse(null, 'Tower deleted successfully');
    }
}
