<?php

namespace Database\Factories;

use App\Models\Land;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class LandFactory extends Factory
{
    protected $model = Land::class;

    public function definition(): array
    {
        $name = 'أرض رقم ' . fake()->unique()->numberBetween(1, 999);

        return [
            'project_id' => Project::factory(),
            'name' => $name,
            'name_en' => 'Land No. ' . fake()->unique()->numberBetween(1, 999),
            'description' => fake()->paragraphs(2, true),
            'description_en' => fake()->paragraphs(2, true),
            'area' => fake()->randomFloat(2, 200, 1500),
            'price' => fake()->numberBetween(30000, 500000),
            'location_within_project' => fake()->randomElement([
                'القطاع A',
                'القطاع B',
                'قرب الحديقة',
                'قرب الشارع الرئيسي',
                'الواجهة الجنوبية',
            ]),
            'location_within_project_en' => fake()->randomElement([
                'Sector A',
                'Sector B',
                'Near Garden',
                'Near Main Road',
                'South Front',
            ]),
            'cover_image' => 'defaults/lands/land-' . fake()->numberBetween(1, 5) . '.jpg',
            'type' => fake()->randomElement(['agricultural', 'residential', 'commercial', 'other']),
            'status' => fake()->randomElement(['available', 'reserved', 'sold']),
            'is_active' => true,
        ];
    }
}
