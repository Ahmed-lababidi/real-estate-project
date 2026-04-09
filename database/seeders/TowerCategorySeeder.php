<?php

namespace Database\Seeders;

use App\Models\TowerCategory;
use Illuminate\Database\Seeder;

class TowerCategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'سكني', 'name_en' => 'residential', 'slug' => 'residential'],
            ['name' => 'تجاري', 'name_en' => 'commercial', 'slug' => 'commercial'],
            ['name' => 'خدمي', 'name_en' => 'service', 'slug' => 'service'],
        ];

        foreach ($items as $index => $item) {
            TowerCategory::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'name_en' => $item['name_en'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
