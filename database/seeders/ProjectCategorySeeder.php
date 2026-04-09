<?php

namespace Database\Seeders;

use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;

class ProjectCategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'سكني','name_en' => 'residential' ,'slug' => 'residential'],
            ['name' => 'تجاري', 'name_en' => 'commercial', 'slug' => 'commercial'],
            ['name' => 'استثماري', 'name_en' => 'investment', 'slug' => 'investment'],
            ['name' => 'اصطياف', 'name_en' => 'resort', 'slug' => 'resort'],
        ];

        foreach ($items as $index => $item) {
            ProjectCategory::updateOrCreate(
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
