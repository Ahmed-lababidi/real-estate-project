<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Tower;
use App\Models\TowerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TowerFactory extends Factory
{
    protected $model = Tower::class;

    public function definition(): array
    {
        $name = 'برج ' . fake()->randomElement(['الماس', 'الروضة', 'النور', 'الصفا', 'الهدى', 'المجد']) . ' ' . fake()->numberBetween(1, 99);

        return [
            'project_id' => Project::factory(),
            'tower_category_id' => TowerCategory::inRandomOrder()->value('id') ?? TowerCategory::factory(),
            'name' => $name,
            'name_en' => 'Tower ' . fake()->randomElement(['Diamond', 'Noor', 'Majd', 'Safa', 'Rawda']) . ' ' . fake()->numberBetween(1, 99),
            'slug' => Str::slug($name . '-' . fake()->unique()->numberBetween(1, 9999)),
            'description' => fake()->paragraphs(2, true),
            'description_en' => fake()->paragraphs(2, true),
            'number_of_floors' => fake()->numberBetween(5, 20),
            'location_within_project' => fake()->randomElement([
                'الجهة الشرقية',
                'الجهة الغربية',
                'قرب المدخل الرئيسي',
                'منتصف المشروع',
                'قرب الحديقة',
            ]),
            'location_within_project_en' => fake()->randomElement([
                'East Side',
                'West Side',
                'Near Main Entrance',
                'Center of Project',
                'Near Garden',
            ]),
            'is_active' => true,
            'cover_image' => 'defaults/towers/tower-' . fake()->numberBetween(1, 5) . '.jpg',
            'model_3d_path' => 'defaults/models/tower-' . fake()->numberBetween(1, 3) . '.glb',
        ];
    }
}
