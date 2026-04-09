<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;

class PublicSettingController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly SettingService $settingService
    ) {}

    public function index()
    {
        return $this->successResponse([
            'company_name' => $this->settingService->get('company_name'),
            'company_name_en' => $this->settingService->get('company_name_en'),
            'company_description' => $this->settingService->get('company_description'),
            'company_description_en' => $this->settingService->get('company_description_en'),
            'company_phone' => $this->settingService->get('company_phone'),
            'company_whatsapp' => $this->settingService->get('company_whatsapp'),
            'company_email' => $this->settingService->get('company_email'),
            'company_address' => $this->settingService->get('company_address'),
            'company_address_en' => $this->settingService->get('company_address_en'),

            'facebook_url' => $this->settingService->get('facebook_url'),
            'instagram_url' => $this->settingService->get('instagram_url'),
            'youtube_url' => $this->settingService->get('youtube_url'),
            'telegram_url' => $this->settingService->get('telegram_url'),
            'website_url' => $this->settingService->get('website_url'),

            'about_us' => $this->settingService->get('about_us'),
            'privacy_policy' => $this->settingService->get('privacy_policy'),
            'terms_conditions' => $this->settingService->get('terms_conditions'),

            'logo_url' => $this->settingService->get('logo') ? Storage::url($this->settingService->get('logo')) : null,
            'favicon_url' => $this->settingService->get('favicon') ? Storage::url($this->settingService->get('favicon')) : null,
        ], 'App settings fetched successfully');
    }
}
