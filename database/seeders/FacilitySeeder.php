<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Project;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::pluck('id', 'slug');

        $items = [
            [
                'project_slug' => 'jasmine-residential-project',
                'name' => 'الحديقة المركزية',
                'name_en' => 'Central Garden',
                'description' => 'حديقة واسعة للعائلات والأطفال.',
                'description_en' => 'A spacious garden for families and children.',
                'area' => 1500.00,
                'location_within_project' => 'وسط المشروع',
                'location_within_project_en' => 'Center of the Project',
                'cover_image' => 'defaults/facilities/garden.jpg',
                'type' => 'garden',
                'is_active' => true,
            ],
            [
                'project_slug' => 'jasmine-residential-project',
                'name' => 'المسبح الرئيسي',
                'name_en' => 'Main Pool',
                'description' => 'مسبح ترفيهي للسكان.',
                'description_en' => 'A recreational swimming pool for residents.',
                'area' => 400.00,
                'location_within_project' => 'الجهة الغربية',
                'location_within_project_en' => 'West Side',
                'cover_image' => 'defaults/facilities/pool.jpg',
                'type' => 'pool',
                'is_active' => true,
            ],
            [
                'project_slug' => 'elite-commercial-project',
                'name' => 'مبنى المكاتب',
                'name_en' => 'Office Building',
                'description' => 'مرفق مخصص للأعمال الإدارية.',
                'description_en' => 'A facility designated for administrative businesses.',
                'area' => 900.00,
                'location_within_project' => 'المدخل الجنوبي',
                'location_within_project_en' => 'South Entrance',
                'cover_image' => 'defaults/facilities/office.jpg',
                'type' => 'office',
                'is_active' => true,
            ],
            [
                'project_slug' => 'oasis-resort-project',
                'name' => 'الملعب الرياضي',
                'name_en' => 'Sports Court',
                'description' => 'ملعب متعدد الاستخدامات.',
                'description_en' => 'A multi-purpose sports court.',
                'area' => 700.00,
                'location_within_project' => 'القطاع الأوسط',
                'location_within_project_en' => 'Middle Sector',
                'cover_image' => 'defaults/facilities/court.jpg',
                'type' => 'court',
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            $projectId = $projects[$item['project_slug']] ?? null;

            if (!$projectId) {
                continue;
            }

            Facility::updateOrCreate(
                [
                    'project_id' => $projectId,
                    'name' => $item['name'],
                ],
                [
                    'name_en' => $item['name_en'],
                    'description' => $item['description'],
                    'description_en' => $item['description_en'],
                    'area' => $item['area'],
                    'location_within_project' => $item['location_within_project'],
                    'location_within_project_en' => $item['location_within_project_en'],
                    'cover_image' => $item['cover_image'],
                    'type' => $item['type'],
                    'is_active' => $item['is_active'],
                ]
            );
        }
    }
}
