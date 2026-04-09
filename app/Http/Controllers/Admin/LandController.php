<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLandRequest;
use App\Http\Requests\Admin\UpdateLandRequest;
use App\Http\Resources\LandResource;
use App\Models\Land;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;


class LandController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ImageService $imageService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $lands = Land::query()
            ->with(['project', 'images'])
            ->withCount('reservations')
            ->when($request->filled('project_id'), fn($q) => $q->where('project_id', $request->project_id))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('is_active'), fn($q) => $q->where('is_active', $request->boolean('is_active')))
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
            ->paginate($request->integer('per_page', 15));

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLandRequest $request)
    {
        $land = DB::transaction(function () use ($request) {
            $data = $request->validated();

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $this->imageService->upload(
                    $request->file('cover_image'),
                    'lands/covers'
                );
            }

            $land = Land::create($data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $this->imageService->upload($image, 'lands/gallery');

                    $land->images()->create([
                        'path' => $path,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            return $land->load(['project', 'images']);
        });

        return $this->successResponse(
            new LandResource($land),
            'Land created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Land $land)
    {
        $land->load(['project', 'images', 'reservations']);

        return $this->successResponse(
            new LandResource($land),
            'Land fetched successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLandRequest $request, Land $land)
    {
        $land = DB::transaction(function () use ($request, $land) {
            $data = $request->validated();

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $this->imageService->upload(
                    $request->file('cover_image'),
                    'lands/covers',
                    $land->cover_image
                );
            }

            $land->update($data);

            if ($request->filled('deleted_image_ids')) {
                $imagesToDelete = $land->images()
                    ->whereIn('id', $request->deleted_image_ids)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    $this->imageService->delete($image->path, $image->disk);
                    $image->delete();
                }
            }

            if ($request->hasFile('images')) {
                $lastSort = (int) $land->images()->max('sort_order');

                foreach ($request->file('images') as $index => $image) {
                    $path = $this->imageService->upload($image, 'lands/gallery');

                    $land->images()->create([
                        'path' => $path,
                        'sort_order' => $lastSort + $index + 1,
                    ]);
                }
            }

            return $land->fresh()->load(['project', 'images']);
        });

        return $this->successResponse(
            new LandResource($land),
            'Land updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Land $land)
    {
        if ($land->reservations()->exists()) {
            return $this->errorResponse(
                'Cannot delete this land because it has related reservations.',
                422
            );
        }

        DB::transaction(function () use ($land) {
            $this->imageService->delete($land->cover_image);

            foreach ($land->images as $image) {
                $this->imageService->delete($image->path, $image->disk);
                $image->delete();
            }

            $land->forceDelete();
        });

        return $this->successResponse(null, 'Land deleted successfully');
    }
}
