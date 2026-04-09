<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Tower;
use App\Models\TowerCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TowerSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::pluck('id', 'slug');
        $categories = TowerCategory::pluck('id', 'slug');

        $items = [
            [
                'project_slug' => 'jasmine-residential-project',
                'tower_category_slug' => 'residential',
                'name' => 'البرج A',
                'name_en' => 'Tower A',
                'slug' => 'tower-a',
                'description' => 'برج سكني فاخر يضم شقق متعددة المساحات.',
                'description_en' => 'A premium residential tower with apartments of various sizes.',
                'number_of_floors' => 12,
                'location_within_project' => 'الجهة الشمالية',
                'location_within_project_en' => 'North Side',
                'is_active' => true,
                'cover_image' => 'defaults/towers/tower-a.jpg',
                'model_3d_path' => 'defaults/models/tower-a.glb',
            ],
            [
                'project_slug' => 'jasmine-residential-project',
                'tower_category_slug' => 'residential',
                'name' => 'البرج B',
                'name_en' => 'Tower B',
                'slug' => 'tower-b',
                'description' => 'برج سكني حديث مناسب للعائلات.',
                'description_en' => 'A modern residential tower suitable for families.',
                'number_of_floors' => 10,
                'location_within_project' => 'الجهة الجنوبية',
                'location_within_project_en' => 'South Side',
                'is_active' => true,
                'cover_image' => 'defaults/towers/tower-b.jpg',
                'model_3d_path' => 'defaults/models/tower-b.glb',
            ],
            [
                'project_slug' => 'elite-commercial-project',
                'tower_category_slug' => 'commercial',
                'name' => 'برج الأعمال',
                'name_en' => 'Business Tower',
                'slug' => 'business-tower',
                'description' => 'برج مخصص للمكاتب والأعمال التجارية.',
                'description_en' => 'A tower dedicated to offices and commercial business spaces.',
                'number_of_floors' => 15,
                'location_within_project' => 'المدخل الرئيسي',
                'location_within_project_en' => 'Main Entrance',
                'is_active' => true,
                'cover_image' => 'defaults/towers/business-tower.jpg',
                'model_3d_path' => 'defaults/models/business-tower.glb',
            ],
        ];

        foreach ($items as $item) {
            $projectId = $projects[$item['project_slug']] ?? null;
            $towerCategoryId = $categories[$item['tower_category_slug']] ?? null;

            if (!$projectId || !$towerCategoryId) {
                continue;
            }

            Tower::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'project_id' => $projectId,
                    'tower_category_id' => $towerCategoryId,
                    'name' => $item['name'],
                    'name_en' => $item['name_en'],
                    'description' => $item['description'],
                    'description_en' => $item['description_en'],
                    'number_of_floors' => $item['number_of_floors'],
                    'location_within_project' => $item['location_within_project'],
                    'location_within_project_en' => $item['location_within_project_en'],
                    'is_active' => $item['is_active'],
                    'cover_image' => $item['cover_image'],
                    'model_3d_path' => $item['model_3d_path'],
                ]
            );
        }
    }
}
