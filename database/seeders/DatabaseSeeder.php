<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            ProjectCategorySeeder::class,
            TowerCategorySeeder::class,
            ApartmentOrientationSeeder::class,
            SettingSeeder::class,
            BannerSeeder::class,

            // البيانات الأساسية للعقارات
            RealEstateSeeder::class,
        ]);
    }
}
