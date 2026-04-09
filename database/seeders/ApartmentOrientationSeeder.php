<?php

namespace Database\Seeders;

use App\Models\ApartmentOrientation;
use Illuminate\Database\Seeder;

class ApartmentOrientationSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'شرقي','name_en' => 'east' , 'slug' => 'east', 'sort_order' => 1],
            ['name' => 'غربي', 'name_en' => 'west', 'slug' => 'west', 'sort_order' => 2],
            ['name' => 'شمالي', 'name_en' => 'north', 'slug' => 'north', 'sort_order' => 3],
            ['name' => 'جنوبي', 'name_en' => 'south', 'slug' => 'south', 'sort_order' => 4],
            ['name' => 'شمالي شرقي', 'name_en' => 'north-east', 'slug' => 'north-east', 'sort_order' => 5],
            ['name' => 'شمالي غربي', 'name_en' => 'north-west', 'slug' => 'north-west', 'sort_order' => 6],
            ['name' => 'جنوبي شرقي', 'name_en' => 'south-east', 'slug' => 'south-east', 'sort_order' => 7],
            ['name' => 'جنوبي غربي', 'name_en' => 'south-west', 'slug' => 'south-west', 'sort_order' => 8],
            ['name' => 'مفتوح', 'name_en' => 'open', 'slug' => 'open', 'sort_order' => 9],

        ];

        foreach ($items as $item) {
            ApartmentOrientation::updateOrCreate(
                ['slug' => $item['slug']],
                array_merge($item, ['is_active' => true])
            );
        }
    }
}
