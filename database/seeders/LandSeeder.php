<?php

namespace Database\Seeders;

use App\Models\Land;
use App\Models\Project;
use Illuminate\Database\Seeder;

class LandSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::pluck('id', 'slug');

        $items = [
            [
                'project_slug' => 'oasis-resort-project',
                'name' => 'أرض زراعية 1',
                'name_en' => 'Agricultural Land 1',
                'description' => 'أرض زراعية مناسبة للاستثمار الزراعي.',
                'description_en' => 'Agricultural land suitable for farming investment.',
                'area' => 1200.00,
                'price' => 48000.00,
                'location_within_project' => 'القطاع الشرقي',
                'location_within_project_en' => 'East Sector',
                'cover_image' => 'defaults/lands/land1.jpg',
                'type' => 'agricultural',
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'project_slug' => 'oasis-resort-project',
                'name' => 'أرض سكنية 2',
                'name_en' => 'Residential Land 2',
                'description' => 'أرض سكنية مناسبة للبناء والاستثمار.',
                'description_en' => 'Residential land suitable for construction and investment.',
                'area' => 850.00,
                'price' => 62000.00,
                'location_within_project' => 'القطاع الغربي',
                'location_within_project_en' => 'West Sector',
                'cover_image' => 'defaults/lands/land2.jpg',
                'type' => 'residential',
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'project_slug' => 'elite-commercial-project',
                'name' => 'أرض تجارية 1',
                'name_en' => 'Commercial Land 1',
                'description' => 'أرض مناسبة لمشروع تجاري أو استثماري.',
                'description_en' => 'Land suitable for a commercial or investment project.',
                'area' => 950.00,
                'price' => 135000.00,
                'location_within_project' => 'قرب المدخل الرئيسي',
                'location_within_project_en' => 'Near Main Entrance',
                'cover_image' => 'defaults/lands/land3.jpg',
                'type' => 'commercial',
                'status' => 'available',
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            $projectId = $projects[$item['project_slug']] ?? null;

            if (!$projectId) {
                continue;
            }

            Land::updateOrCreate(
                [
                    'project_id' => $projectId,
                    'name' => $item['name'],
                ],
                [
                    'name_en' => $item['name_en'],
                    'description' => $item['description'],
                    'description_en' => $item['description_en'],
                    'area' => $item['area'],
                    'price' => $item['price'],
                    'location_within_project' => $item['location_within_project'],
                    'location_within_project_en' => $item['location_within_project_en'],
                    'cover_image' => $item['cover_image'],
                    'type' => $item['type'],
                    'status' => $item['status'],
                    'is_active' => $item['is_active'],
                ]
            );
        }
    }
}
