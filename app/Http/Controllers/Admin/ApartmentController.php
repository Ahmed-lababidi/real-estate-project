<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreApartmentRequest;
use App\Http\Requests\Admin\UpdateApartmentRequest;
use App\Http\Resources\ApartmentResource;
use App\Models\Apartment;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApartmentController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ImageService $imageService
    ) {}

    public function index(Request $request)
    {
        $apartments = Apartment::query()
            ->with(['tower.project', 'orientation', 'images'])
            ->withCount('reservations')
            ->when($request->filled('tower_id'), fn ($q) => $q->where('tower_id', $request->tower_id))
            ->when($request->filled('project_id'), fn ($q) =>
                $q->whereHas('tower', fn ($qq) => $qq->where('project_id', $request->project_id))
            )
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->when($request->filled('orientation_id'), fn ($q) => $q->where('apartment_orientation_id', $request->orientation_id))
            ->when($request->filled('floor_number'), fn ($q) => $q->where('floor_number', $request->floor_number))
            ->when($request->filled('bedrooms'), fn ($q) => $q->where('bedrooms', $request->bedrooms))
            ->when($request->filled('rooms_number'), fn ($q) => $q->where('rooms_number', $request->rooms_number))
            ->when($request->filled('min_price'), fn ($q) => $q->where('price', '>=', $request->min_price))
            ->when($request->filled('max_price'), fn ($q) => $q->where('price', '<=', $request->max_price))
            ->when($request->filled('min_area'), fn ($q) => $q->where('area', '>=', $request->min_area))
            ->when($request->filled('max_area'), fn ($q) => $q->where('area', '<=', $request->max_area))
            ->when($request->filled('search'), fn ($q) =>
                $q->where(function ($qq) use ($request) {
                    $qq->where('name', 'like', "%{$request->search}%")
                        ->orWhere('name_en', 'like', "%{$request->search}%")
                        ->orWhere('unit_number', 'like', "%{$request->search}%")
                        ->orWhere('code', 'like', "%{$request->search}%");
                })
            )
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse([
            'items' => ApartmentResource::collection($apartments->items()),
            'pagination' => [
                'current_page' => $apartments->currentPage(),
                'last_page' => $apartments->lastPage(),
                'per_page' => $apartments->perPage(),
                'total' => $apartments->total(),
            ],
        ], 'Apartments fetched successfully');
    }

    public function store(StoreApartmentRequest $request)
    {
        $apartment = DB::transaction(function () use ($request) {
            $data = $request->validated();

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $this->imageService->upload(
                    $request->file('cover_image'),
                    'apartments/covers'
                );
            }

            $apartment = Apartment::create($data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $this->imageService->upload($image, 'apartments/gallery');

                    $apartment->images()->create([
                        'path' => $path,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            return $apartment->load(['tower.project', 'orientation', 'images']);
        });

        return $this->successResponse(
            new ApartmentResource($apartment),
            'Apartment created successfully',
            201
        );
    }

    public function show(Apartment $apartment)
    {
        $apartment->load(['tower.project', 'orientation', 'images', 'reservations']);

        return $this->successResponse(
            new ApartmentResource($apartment),
            'Apartment fetched successfully'
        );
    }

    public function update(UpdateApartmentRequest $request, Apartment $apartment)
    {
        $apartment = DB::transaction(function () use ($request, $apartment) {
            $data = $request->validated();

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $this->imageService->upload(
                    $request->file('cover_image'),
                    'apartments/covers',
                    $apartment->cover_image
                );
            }

            $apartment->update($data);

            if ($request->filled('deleted_image_ids')) {
                $imagesToDelete = $apartment->images()
                    ->whereIn('id', $request->deleted_image_ids)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    $this->imageService->delete($image->path, $image->disk);
                    $image->delete();
                }
            }

            if ($request->hasFile('images')) {
                $lastSort = (int) $apartment->images()->max('sort_order');

                foreach ($request->file('images') as $index => $image) {
                    $path = $this->imageService->upload($image, 'apartments/gallery');

                    $apartment->images()->create([
                        'path' => $path,
                        'sort_order' => $lastSort + $index + 1,
                    ]);
                }
            }

            return $apartment->fresh()->load(['tower.project', 'orientation', 'images']);
        });

        return $this->successResponse(
            new ApartmentResource($apartment),
            'Apartment updated successfully'
        );
    }

    public function destroy(Apartment $apartment)
    {
        if ($apartment->reservations()->exists()) {
            return $this->errorResponse(
                'Cannot delete this apartment because it has related reservations.',
                422
            );
        }

        DB::transaction(function () use ($apartment) {
            $this->imageService->delete($apartment->cover_image);

            foreach ($apartment->images as $image) {
                $this->imageService->delete($image->path, $image->disk);
                $image->delete();
            }

            $apartment->forceDelete();
        });

        return $this->successResponse(null, 'Apartment deleted successfully');
    }
}
