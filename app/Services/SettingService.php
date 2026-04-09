<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value,
                'group' => $group,
                'type' => $type,
            ]
        );
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $setting = Setting::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'json' => json_decode($setting->value, true),
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($setting->value) ? $setting->value + 0 : $setting->value,
            default => $setting->value,
        };
    }

    public function group(string $group): array
    {
        return Setting::where('group', $group)
            ->get()
            ->mapWithKeys(function ($item) {
                $value = match ($item->type) {
                    'json' => json_decode($item->value, true),
                    'boolean' => filter_var($item->value, FILTER_VALIDATE_BOOLEAN),
                    'number' => is_numeric($item->value) ? $item->value + 0 : $item->value,
                    default => $item->value,
                };

                return [$item->key => $value];
            })
            ->toArray();
    }
}
