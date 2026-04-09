<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBannerRequest;
use App\Http\Requests\Admin\UpdateBannerRequest;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ImageService $imageService
    ) {}

    public function index(Request $request)
    {
        $items = Banner::query()
            ->when($request->filled('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->when($request->filled('is_featured'), fn ($q) => $q->where('is_featured', $request->boolean('is_featured')))
            ->when($request->filled('project_category_id'), fn ($q) => $q->where('project_category_id', $request->integer('project_category_id')))
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse([
            'items' => BannerResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ], 'Banners fetched successfully');
    }

    public function store(StoreBannerRequest $request)
    {
        $data = $request->validated();

        $data['image'] = $this->imageService->upload(
            $request->file('image'),
            'banners'
        );

        if ($request->hasFile('mobile_image')) {
            $data['mobile_image'] = $this->imageService->upload(
                $request->file('mobile_image'),
                'banners/mobile'
            );
        }

        $banner = Banner::create($data);

        return $this->successResponse(
            new BannerResource($banner),
            'Banner created successfully',
            201
        );
    }

    public function show(Banner $banner)
    {
        return $this->successResponse(
            new BannerResource($banner),
            'Banner fetched successfully'
        );
    }

    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageService->upload(
                $request->file('image'),
                'banners',
                $banner->image
            );
        }

        if ($request->hasFile('mobile_image')) {
            $data['mobile_image'] = $this->imageService->upload(
                $request->file('mobile_image'),
                'banners/mobile',
                $banner->mobile_image
            );
        }

        $banner->update($data);

        return $this->successResponse(
            new BannerResource($banner->fresh()),
            'Banner updated successfully'
        );
    }

    public function destroy(Banner $banner)
    {
        $this->imageService->delete($banner->image);
        $this->imageService->delete($banner->mobile_image);

        $banner->delete();

        return $this->successResponse(null, 'Banner deleted successfully');
    }
}
