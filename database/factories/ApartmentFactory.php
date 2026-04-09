<?php

namespace Database\Factories;

use App\Models\Apartment;
use App\Models\ApartmentOrientation;
use App\Models\Tower;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ApartmentFactory extends Factory
{
    protected $model = Apartment::class;

    public function definition(): array
    {
        $floor = fake()->numberBetween(1, 15);
        $unit = fake()->numberBetween(1, 8);
        $name = "شقة {$floor}-{$unit}";
        $slug = Str::slug($name . '-' . fake()->unique()->numberBetween(1, 99999));
        $code = 'APT-' . strtoupper(fake()->unique()->bothify('##??##'));

        $bedrooms = fake()->numberBetween(1, 5);
        $bathrooms = fake()->numberBetween(1, 4);

        return [
            'tower_id' => Tower::factory(),
            'apartment_orientation_id' => ApartmentOrientation::inRandomOrder()->value('id'),
            'name' => $name,
            'name_en' => "Apartment {$floor}-{$unit}",
            'slug' => $slug,
            'code' => $code,
            'floor_number' => $floor,
            'unit_number' => (string) $unit,
            'bedrooms' => $bedrooms,
            'bathrooms' => $bathrooms,
            'rooms_number' => $bedrooms + fake()->numberBetween(1, 3),
            'area' => fake()->randomFloat(2, 75, 250),
            'price' => fake()->numberBetween(40000, 250000),
            'status' => fake()->randomElement(['available', 'reserved', 'sold']),
            'description' => fake()->paragraphs(2, true),
            'description_en' => fake()->paragraphs(2, true),
            'is_featured' => fake()->boolean(20),
            'is_active' => true,
            'cover_image' => 'defaults/apartments/apartment-' . fake()->numberBetween(1, 6) . '.jpg',
        ];
    }
}
