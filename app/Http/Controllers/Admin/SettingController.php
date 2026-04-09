<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Services\ImageService;
use App\Services\SettingService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly SettingService $settingService,
        private readonly ImageService $imageService
    ) {}

    public function index()
    {
        $general = $this->settingService->group('general');
        $social = $this->settingService->group('social');
        $pages = $this->settingService->group('pages');
        $branding = $this->settingService->group('branding');

        if (! empty($branding['logo'])) {
            $branding['logo_url'] = Storage::url($branding['logo']);
        }

        if (! empty($branding['favicon'])) {
            $branding['favicon_url'] = Storage::url($branding['favicon']);
        }

        return $this->successResponse([
            'general' => $general,
            'social' => $social,
            'pages' => $pages,
            'branding' => $branding,
        ], 'Settings fetched successfully');
    }

    public function update(UpdateSettingsRequest $request)
    {
        $data = $request->validated();

        // General
        $this->settingService->set('company_name', $data['company_name'] ?? null, 'general');
        $this->settingService->set('company_description', $data['company_description'] ?? null, 'general', 'text');
        $this->settingService->set('company_phone', $data['company_phone'] ?? null, 'general');
        $this->settingService->set('company_whatsapp', $data['company_whatsapp'] ?? null, 'general');
        $this->settingService->set('company_email', $data['company_email'] ?? null, 'general');
        $this->settingService->set('company_address', $data['company_address'] ?? null, 'general');

        // Social
        $this->settingService->set('facebook_url', $data['facebook_url'] ?? null, 'social');
        $this->settingService->set('instagram_url', $data['instagram_url'] ?? null, 'social');
        $this->settingService->set('youtube_url', $data['youtube_url'] ?? null, 'social');
        $this->settingService->set('telegram_url', $data['telegram_url'] ?? null, 'social');
        $this->settingService->set('website_url', $data['website_url'] ?? null, 'social');

        // Pages
        $this->settingService->set('about_us', $data['about_us'] ?? null, 'pages', 'text');
        $this->settingService->set('privacy_policy', $data['privacy_policy'] ?? null, 'pages', 'text');
        $this->settingService->set('terms_conditions', $data['terms_conditions'] ?? null, 'pages', 'text');

        // Branding
        if ($request->hasFile('logo')) {
            $oldLogo = $this->settingService->get('logo');

            $logoPath = $this->imageService->upload(
                $request->file('logo'),
                'settings/branding',
                $oldLogo
            );

            $this->settingService->set('logo', $logoPath, 'branding', 'image');
        }

        if ($request->hasFile('favicon')) {
            $oldFavicon = $this->settingService->get('favicon');

            $faviconPath = $this->imageService->upload(
                $request->file('favicon'),
                'settings/branding',
                $oldFavicon
            );

            $this->settingService->set('favicon', $faviconPath, 'branding', 'image');
        }

        return $this->index();
    }
}
