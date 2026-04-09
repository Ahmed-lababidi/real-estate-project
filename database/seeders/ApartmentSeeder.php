<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\ApartmentOrientation;
use App\Models\Tower;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApartmentSeeder extends Seeder
{
    public function run(): void
    {
        $orientations = ApartmentOrientation::pluck('id', 'slug');
        $towers = Tower::all();

        foreach ($towers as $tower) {
            for ($floor = 1; $floor <= 3; $floor++) {
                for ($unit = 1; $unit <= 4; $unit++) {
                    $code = strtoupper('APT-' . $tower->id . '-' . $floor . '-' . $unit);
                    $slug = Str::slug($tower->slug . '-apartment-' . $floor . '-' . $unit);

                    Apartment::updateOrCreate(
                        ['code' => $code],
                        [
                            'tower_id' => $tower->id,
                            'apartment_orientation_id' => collect($orientations)->random(),
                            'name' => "شقة {$unit} - طابق {$floor}",
                            'name_en' => "Apartment {$unit} - Floor {$floor}",
                            'slug' => $slug,
                            'floor_number' => $floor,
                            'unit_number' => (string) $unit,
                            'bedrooms' => rand(2, 4),
                            'bathrooms' => rand(1, 3),
                            'rooms_number' => rand(3, 6),
                            'area' => rand(90, 180),
                            'price' => rand(65000, 180000),
                            'status' => 'available',
                            'description' => 'شقة بتصميم عصري ومساحة مريحة وإطلالة ممتازة.',
                            'description_en' => 'A modern apartment with a comfortable area and excellent view.',
                            'is_featured' => rand(0, 1),
                            'is_active' => true,
                            'cover_image' => 'defaults/apartments/apartment.jpg',
                        ]
                    );
                }
            }
        }
    }
}
