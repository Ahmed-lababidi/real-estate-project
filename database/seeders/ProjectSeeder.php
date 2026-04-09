<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ProjectCategory::pluck('id', 'slug');

        $items = [
            [
                'name' => 'مشروع الياسمين السكني',
                'name_en' => 'Jasmine Residential Project',
                'slug' => 'jasmine-residential-project',
                'code' => 'PRJ-001',
                'description' => 'مجمع سكني حديث يحتوي على أبراج وشقق وخدمات متكاملة.',
                'description_en' => 'A modern residential complex with towers, apartments, and integrated services.',
                'location_text' => 'دمشق - ضاحية الياسمين',
                'location_text_en' => 'Damascus - Jasmine Suburb',
                'latitude' => 33.5138000,
                'longitude' => 36.2765000,
                'starting_price' => 85000.00,
                'delivery_date' => '2027-06-01',
                'is_featured' => true,
                'is_active' => true,
                'cover_image' => 'defaults/projects/project1.jpg',
                'project_category_id' => $categories['residential'] ?? 1,
            ],
            [
                'name' => 'مشروع النخبة التجاري',
                'name_en' => 'Elite Commercial Project',
                'slug' => 'elite-commercial-project',
                'code' => 'PRJ-002',
                'description' => 'مشروع تجاري واستثماري يضم مكاتب ومحلات وخدمات.',
                'description_en' => 'A commercial and investment project with offices, shops, and services.',
                'location_text' => 'دمشق - المنطقة التجارية',
                'location_text_en' => 'Damascus - Commercial Zone',
                'latitude' => 33.5201000,
                'longitude' => 36.2900000,
                'starting_price' => 120000.00,
                'delivery_date' => '2028-01-15',
                'is_featured' => false,
                'is_active' => true,
                'cover_image' => 'defaults/projects/project2.jpg',
                'project_category_id' => $categories['commercial'] ?? 2,
            ],
            [
                'name' => 'مشروع الواحة الاصطيافي',
                'name_en' => 'Oasis Resort Project',
                'slug' => 'oasis-resort-project',
                'code' => 'PRJ-003',
                'description' => 'مشروع اصطيافي يضم مزارع وأراضي ومرافق ترفيهية.',
                'description_en' => 'A resort project featuring farms, lands, and recreational facilities.',
                'location_text' => 'ريف دمشق - منطقة الواحة',
                'location_text_en' => 'Rural Damascus - Oasis Area',
                'latitude' => 33.4300000,
                'longitude' => 36.1800000,
                'starting_price' => 45000.00,
                'delivery_date' => '2027-09-10',
                'is_featured' => true,
                'is_active' => true,
                'cover_image' => 'defaults/projects/project3.jpg',
                'project_category_id' => $categories['resort'] ?? 4,
            ],
        ];

        foreach ($items as $item) {
            Project::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
