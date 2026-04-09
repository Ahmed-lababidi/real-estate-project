<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $arabicNames = [
            'مشروع الياسمين',
            'مشروع النخيل',
            'مشروع الندى',
            'مشروع الربوة',
            'مشروع المروج',
            'مشروع السندس',
            'مشروع الورود',
            'مشروع الريف الذهبي',
            'مشروع الواحة',
            'مشروع السكينة',
        ];

        $name = fake()->unique()->randomElement($arabicNames) . ' ' . fake()->unique()->numberBetween(1, 999);
        $slug = Str::slug($name . '-' . fake()->unique()->numberBetween(1, 9999));

        return [
            'project_category_id' => ProjectCategory::inRandomOrder()->value('id') ?? ProjectCategory::factory(),
            'name' => $name,
            'name_en' => 'Project ' . fake()->unique()->words(2, true),
            'slug' => $slug,
            'code' => 'PRJ-' . strtoupper(fake()->unique()->bothify('###??')),
            'description' => fake()->paragraphs(3, true),
            'description_en' => fake()->paragraphs(3, true),
            'location_text' => fake()->randomElement([
                'دمشق - يعفور',
                'دمشق - قدسيا',
                'ريف دمشق - الصبورة',
                'حمص - طريق الشام',
                'اللاذقية - الشاطئ الأزرق',
                'طرطوس - الكورنيش',
            ]),
            'location_text_en' => fake()->randomElement([
                'Damascus - Yaafour',
                'Damascus - Qudsaya',
                'Rural Damascus - Al-Saboura',
                'Homs - Damascus Road',
                'Latakia - Blue Coast',
                'Tartus - Corniche',
            ]),
            'latitude' => fake()->latitude(33.3000000, 34.2000000),
            'longitude' => fake()->longitude(35.8000000, 36.8000000),
            'starting_price' => fake()->numberBetween(50000, 300000),
            'delivery_date' => fake()->dateTimeBetween('+3 months', '+3 years')->format('Y-m-d'),
            'is_featured' => fake()->boolean(30),
            'is_active' => true,
            'cover_image' => 'defaults/projects/project-' . fake()->numberBetween(1, 5) . '.jpg',
        ];
    }
}
