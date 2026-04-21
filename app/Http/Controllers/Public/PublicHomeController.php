<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApartmentResource;
use App\Http\Resources\BannerResource;
use App\Http\Resources\ProjectResource;
use App\Models\Apartment;
use App\Models\Banner;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Services\SettingService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;

class PublicHomeController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly SettingService $settingService
    ) {}

    public function index()
    {
        $banners = Banner::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderBy('sort_order')
            ->get();

        $projectCategories = ProjectCategory::query()
            ->where('is_active', true)
            ->withCount(['projects' => function ($q) {
                $q->where('is_active', true);
            }])
            ->having('projects_count', '>', 0)
            ->get();

        $projects = Project::query()
            ->where('is_active', true)
            // ->where('is_featured', true)
            ->with(['category:id,name,name_en', 'images'])
            ->take(8)
            ->get();


        $company = [
            'company_name' => $this->settingService->get('company_name'),
            'company_name_en' => $this->settingService->get('company_name_en'),
            'company_description' => $this->settingService->get('company_description'),
            'company_description_en' => $this->settingService->get('company_description_en'),
            'company_phone' => $this->settingService->get('company_phone'),
            'company_whatsapp' => $this->settingService->get('company_whatsapp'),
            'company_email' => $this->settingService->get('company_email'),
            'instagram_url' => $this->settingService->get('instagram_url'),
            'facebook_url' => $this->settingService->get('facebook_url'),
            'company_address' => $this->settingService->get('company_address'),
            'company_address_en' => $this->settingService->get('company_address_en'),
            'logo_url' => $this->settingService->get('logo') ? Storage::url($this->settingService->get('logo')) : null,
        ];

        return $this->successResponse([
            'banners' => $banners->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'title_en' => $banner->title_en,
                    'project_category_id' => $banner->project_category_id,
                    'description' => $banner->description,
                    'description_en' => $banner->description_en,
                    'image_url' => $banner->image ? Storage::url($banner->image) : null,
                ];
            }),
            'project_categories' => $projectCategories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'name_en' => $category->name_en,
                    'projects_count' => $category->projects_count,
                ];
            }),
            'projects' => $projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'category' => $project->category,
                    'name' => $project->name,
                    'name_en' => $project->name_en,
                    'location_text' => $project->location_text,
                    'location_text_en' => $project->location_text_en,
                    'cover_image' => $project->cover_image ? Storage::url($project->cover_image) : null,
                ];
            }),
            'company' => $company,
        ], 'Home data fetched successfully');
    }
}
