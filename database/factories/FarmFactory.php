<?php

namespace Database\Factories;

use App\Models\Farm;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class FarmFactory extends Factory
{
    protected $model = Farm::class;

    public function definition(): array
    {
        $name = 'مزرعة رقم ' . fake()->unique()->numberBetween(1, 999);

        return [
            'project_id' => Project::factory(),
            'name' => $name,
            'name_en' => 'Farm No. ' . fake()->unique()->numberBetween(1, 999),
            'description' => fake()->paragraphs(2, true),
            'description_en' => fake()->paragraphs(2, true),
            'area' => fake()->randomFloat(2, 500, 5000),
            'rooms_number' => fake()->numberBetween(1, 6),
            'price' => fake()->numberBetween(50000, 700000),
            'location_within_project' => fake()->randomElement([
                'الجهة الشمالية',
                'الجهة الجنوبية',
                'قرب البحيرة',
                'قرب المدخل الزراعي',
                'الزاوية الشرقية',
            ]),
            'location_within_project_en' => fake()->randomElement([
                'North Side',
                'South Side',
                'Near Lake',
                'Near Agricultural Entrance',
                'East Corner',
            ]),
            'cover_image' => 'defaults/farms/farm-' . fake()->numberBetween(1, 5) . '.jpg',
            'status' => fake()->randomElement(['available', 'reserved', 'sold']),
            'is_active' => true,
        ];
    }
}
