<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFarmRequest;
use App\Http\Requests\Admin\UpdateFarmRequest;
use App\Http\Resources\FarmResource;
use App\Models\Farm;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;


class FarmController extends Controller
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
        $farms = Farm::query()
            ->with(['project', 'images'])
            ->withCount('reservations')
            ->when($request->filled('project_id'), fn($q) => $q->where('project_id', $request->project_id))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('is_active'), fn($q) => $q->where('is_active', $request->boolean('is_active')))
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
            ->paginate($request->integer('per_page', 15));

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFarmRequest $request)
    {
        $farm = DB::transaction(function () use ($request) {
            $data = $request->validated();

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $this->imageService->upload(
                    $request->file('cover_image'),
                    'farms/covers'
                );
            }

            $farm = Farm::create($data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $this->imageService->upload($image, 'farms/gallery');

                    $farm->images()->create([
                        'path' => $path,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            return $farm->load(['project', 'images']);
        });

        return $this->successResponse(
            new FarmResource($farm),
            'Farm created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Farm $farm)
    {
        $farm->load(['project', 'images', 'reservations']);

        return $this->successResponse(
            new FarmResource($farm),
            'Farm fetched successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFarmRequest $request, Farm $farm)
    {
        $farm = DB::transaction(function () use ($request, $farm) {
            $data = $request->validated();

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $this->imageService->upload(
                    $request->file('cover_image'),
                    'farms/covers',
                    $farm->cover_image
                );
            }

            $farm->update($data);

            if ($request->filled('deleted_image_ids')) {
                $imagesToDelete = $farm->images()
                    ->whereIn('id', $request->deleted_image_ids)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    $this->imageService->delete($image->path, $image->disk);
                    $image->delete();
                }
            }

            if ($request->hasFile('images')) {
                $lastSort = (int) $farm->images()->max('sort_order');

                foreach ($request->file('images') as $index => $image) {
                    $path = $this->imageService->upload($image, 'farms/gallery');

                    $farm->images()->create([
                        'path' => $path,
                        'sort_order' => $lastSort + $index + 1,
                    ]);
                }
            }

            return $farm->fresh()->load(['project', 'images']);
        });

        return $this->successResponse(
            new FarmResource($farm),
            'Farm updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Farm $farm)
    {
        if ($farm->reservations()->exists()) {
            return $this->errorResponse(
                'Cannot delete this farm because it has related reservations.',
                422
            );
        }

        DB::transaction(function () use ($farm) {
            $this->imageService->delete($farm->cover_image);

            foreach ($farm->images as $image) {
                $this->imageService->delete($image->path, $image->disk);
                $image->delete();
            }

            $farm->forceDelete();
        });

        return $this->successResponse(null, 'Farm deleted successfully');
    }
}
