<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'company_name', 'value' => 'شركة التطوير العقاري', 'group' => 'general', 'type' => 'string'],
            ['key' => 'company_name_en', 'value' => 'Real Estate Development Company', 'group' => 'general', 'type' => 'string'],
            ['key' => 'company_description', 'value' => 'شركة متخصصة في التطوير العقاري والمجمعات السكنية.', 'group' => 'general', 'type' => 'text'],
            ['key' => 'company_description_en', 'value' => 'A company specialized in real estate development and residential complexes.', 'group' => 'general', 'type' => 'text'],
            ['key' => 'company_phone', 'value' => '+963000000000', 'group' => 'general', 'type' => 'string'],
            ['key' => 'company_whatsapp', 'value' => '+963000000000', 'group' => 'general', 'type' => 'string'],
            ['key' => 'company_email', 'value' => 'info@example.com', 'group' => 'general', 'type' => 'string'],
            ['key' => 'company_address', 'value' => 'دمشق - سوريا', 'group' => 'general', 'type' => 'string'],
            ['key' => 'company_address_en', 'value' => 'Damascus, Syria', 'group' => 'general', 'type' => 'string'],

            ['key' => 'facebook_url', 'value' => 'https://facebook.com', 'group' => 'social', 'type' => 'string'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com', 'group' => 'social', 'type' => 'string'],
            ['key' => 'youtube_url', 'value' => 'https://youtube.com', 'group' => 'social', 'type' => 'string'],
            ['key' => 'telegram_url', 'value' => 'https://t.me/example', 'group' => 'social', 'type' => 'string'],
            ['key' => 'website_url', 'value' => 'https://example.com', 'group' => 'social', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
