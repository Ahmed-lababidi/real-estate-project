<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Farm;
use App\Models\Facility;
use App\Models\Land;
use App\Models\Project;
use App\Models\Tower;
use Illuminate\Database\Seeder;

class RealEstateSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء 5 مشاريع على الأقل
        Project::factory(5)->create()->each(function (Project $project, int $index) {

            // تحديث starting_price لاحقاً بعد إنشاء الوحدات
            $project->update([
                'is_featured' => $index < 2, // أول مشروعين featured
            ]);

            /**
             * 1) لكل مشروع 3 أبراج على الأقل
             */
            $towers = Tower::factory(fake()->numberBetween(3, 5))
                ->create([
                    'project_id' => $project->id,
                ]);

            /**
             * 2) لكل برج شقق (مثلاً من 8 إلى 15 شقة)
             */
            foreach ($towers as $towerIndex => $tower) {
                $apartmentsCount = fake()->numberBetween(8, 15);

                for ($i = 1; $i <= $apartmentsCount; $i++) {
                    $floor = fake()->numberBetween(1, max(1, $tower->number_of_floors ?? 10));
                    $unit = fake()->numberBetween(1, 8);

                    Apartment::factory()->create([
                        'tower_id' => $tower->id,
                        'name' => "شقة {$tower->id}-{$i}",
                        'name_en' => "Apartment {$tower->id}-{$i}",
                        'floor_number' => $floor,
                        'unit_number' => (string) $unit,
                    ]);
                }
            }

            /**
             * 3) لكل مشروع 3 أراضي على الأقل
             */
            Land::factory(fake()->numberBetween(3, 6))->create([
                'project_id' => $project->id,
            ]);

            /**
             * 4) لكل مشروع 3 مزارع على الأقل
             */
            Farm::factory(fake()->numberBetween(3, 6))->create([
                'project_id' => $project->id,
            ]);

            /**
             * 5) مرافق لكل مشروع (5 إلى 8)
             */
            Facility::factory(fake()->numberBetween(5, 8))->create([
                'project_id' => $project->id,
            ]);

            /**
             * 6) تحديث starting_price بناءً على أقل سعر فعلي
             * من الشقق والأراضي والمزارع
             */
            $minApartmentPrice = Apartment::whereHas('tower', fn($q) => $q->where('project_id', $project->id))->min('price');
            $minLandPrice = Land::where('project_id', $project->id)->min('price');
            $minFarmPrice = Farm::where('project_id', $project->id)->min('price');

            $prices = collect([$minApartmentPrice, $minLandPrice, $minFarmPrice])->filter();

            $project->update([
                'starting_price' => $prices->min() ?? $project->starting_price,
            ]);
        });
    }
}
