<?php

namespace Database\Seeders;

use App\Models\Farm;
use App\Models\Project;
use Illuminate\Database\Seeder;

class FarmSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::pluck('id', 'slug');

        $items = [
            [
                'project_slug' => 'oasis-resort-project',
                'name' => 'مزرعة الزيتون',
                'name_en' => 'Olive Farm',
                'description' => 'مزرعة متكاملة مناسبة للاستجمام والاستثمار الزراعي.',
                'description_en' => 'A complete farm suitable for leisure and agricultural investment.',
                'area' => 2500.00,
                'rooms_number' => 3,
                'price' => 98000.00,
                'location_within_project' => 'القطاع الجنوبي',
                'location_within_project_en' => 'South Sector',
                'cover_image' => 'defaults/farms/farm1.jpg',
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'project_slug' => 'oasis-resort-project',
                'name' => 'مزرعة النخيل',
                'name_en' => 'Palm Farm',
                'description' => 'مزرعة واسعة مع بناء صغير وخدمات أساسية.',
                'description_en' => 'A spacious farm with a small building and basic services.',
                'area' => 3200.00,
                'rooms_number' => 4,
                'price' => 125000.00,
                'location_within_project' => 'القطاع الشمالي',
                'location_within_project_en' => 'North Sector',
                'cover_image' => 'defaults/farms/farm2.jpg',
                'status' => 'available',
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            $projectId = $projects[$item['project_slug']] ?? null;

            if (!$projectId) {
                continue;
            }

            Farm::updateOrCreate(
                [
                    'project_id' => $projectId,
                    'name' => $item['name'],
                ],
                [
                    'name_en' => $item['name_en'],
                    'description' => $item['description'],
                    'description_en' => $item['description_en'],
                    'area' => $item['area'],
                    'rooms_number' => $item['rooms_number'],
                    'price' => $item['price'],
                    'location_within_project' => $item['location_within_project'],
                    'location_within_project_en' => $item['location_within_project_en'],
                    'cover_image' => $item['cover_image'],
                    'status' => $item['status'],
                    'is_active' => $item['is_active'],
                ]
            );
        }
    }
}
