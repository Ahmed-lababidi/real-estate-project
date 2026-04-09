<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFacilityRequest;
use App\Http\Requests\Admin\UpdateFacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacilityController extends Controller
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
        $facilities = Facility::query()
            ->with(['project:id,name,name_en,slug', 'images'])
            ->when($request->filled('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->when($request->filled('min_area'), fn ($q) => $q->where('area', '>=', $request->min_area))
            ->when($request->filled('max_area'), fn ($q) => $q->where('area', '<=', $request->max_area))
            ->when($request->filled('search'), fn ($q) =>
                $q->where(function ($qq) use ($request) {
                    $qq->where('name', 'like', "%{$request->search}%")
                        ->orWhere('name_en', 'like', "%{$request->search}%")
                        ->orWhere('location_within_project', 'like', "%{$request->search}%")
                        ->orWhere('location_within_project_en', 'like', "%{$request->search}%");
                })
            )
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse([
            'items' => FacilityResource::collection($facilities->items()),
            'pagination' => [
                'current_page' => $facilities->currentPage(),
                'last_page' => $facilities->lastPage(),
                'per_page' => $facilities->perPage(),
                'total' => $facilities->total(),
            ],
        ], 'Facilities fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFacilityRequest $request)
    {
        $facilities = DB::transaction(function () use ($request) {
            $data = $request->validated();

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $this->imageService->upload(
                    $request->file('cover_image'),
                    'facilities/covers'
                );
            }

            $facilities = Facility::create($data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $this->imageService->upload($image, 'facilities/gallery');

                    $facilities->images()->create([
                        'path' => $path,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            return $facilities->load(['project', 'images']);
        });

        return $this->successResponse(
            new FacilityResource($facilities),
            'Facility created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Facility $facility)
    {
        $facility->load(['project', 'images']);

        return $this->successResponse(
            new FacilityResource($facility),
            'facility fetched successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFacilityRequest $request, Facility $facility)
    {
        $facility = DB::transaction(function () use ($request, $facility) {
            $data = $request->validated();

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $this->imageService->upload(
                    $request->file('cover_image'),
                    'facilities/covers',
                    $facility->cover_image
                );
            }

            $facility->update($data);

            if ($request->filled('deleted_image_ids')) {
                $imagesToDelete = $facility->images()
                    ->whereIn('id', $request->deleted_image_ids)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    $this->imageService->delete($image->path, $image->disk);
                    $image->delete();
                }
            }

            if ($request->hasFile('images')) {
                $lastSort = (int) $facility->images()->max('sort_order');

                foreach ($request->file('images') as $index => $image) {
                    $path = $this->imageService->upload($image, 'facilities/gallery');

                    $facility->images()->create([
                        'path' => $path,
                        'sort_order' => $lastSort + $index + 1,
                    ]);
                }
            }

            return $facility->fresh()->load(['project', 'images']);
        });

        return $this->successResponse(
            new FacilityResource($facility),
            'Facility updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facility $facility)
    {

        DB::transaction(function () use ($facility) {
            $this->imageService->delete($facility->cover_image);

            foreach ($facility->images as $image) {
                $this->imageService->delete($image->path, $image->disk);
                $image->delete();
            }

            $facility->forceDelete();
        });

        return $this->successResponse(null, 'Facility deleted successfully');
    }
}
