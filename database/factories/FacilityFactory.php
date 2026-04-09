<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacilityFactory extends Factory
{
    protected $model = Facility::class;

    public function definition(): array
    {
        $types = [
            'garden' => ['حديقة', 'Garden'],
            'pool' => ['مسبح', 'Pool'],
            'farm' => ['مزرعة خدمية', 'Service Farm'],
            'court' => ['ملعب', 'Court'],
            'land' => ['مساحة مفتوحة', 'Open Land'],
            'hospital' => ['مركز صحي', 'Medical Center'],
            'school' => ['مدرسة', 'School'],
            'office' => ['مكتب إداري', 'Office'],
            'other' => ['مرفق عام', 'General Facility'],
        ];

        $type = fake()->randomElement(array_keys($types));
        $ar = $types[$type][0];
        $en = $types[$type][1];

        return [
            'project_id' => Project::factory(),
            'name' => $ar . ' ' . fake()->numberBetween(1, 20),
            'name_en' => $en . ' ' . fake()->numberBetween(1, 20),
            'description' => fake()->paragraphs(2, true),
            'description_en' => fake()->paragraphs(2, true),
            'area' => fake()->randomFloat(2, 50, 3000),
            'location_within_project' => fake()->randomElement([
                'قرب المدخل',
                'وسط المشروع',
                'الجهة الغربية',
                'بجانب الأبراج',
                'قرب المنطقة التجارية',
            ]),
            'location_within_project_en' => fake()->randomElement([
                'Near Entrance',
                'Center of Project',
                'West Side',
                'Beside Towers',
                'Near Commercial Zone',
            ]),
            'cover_image' => 'defaults/facilities/facility-' . fake()->numberBetween(1, 5) . '.jpg',
            'type' => $type,
            'is_active' => true,
        ];
    }
}
