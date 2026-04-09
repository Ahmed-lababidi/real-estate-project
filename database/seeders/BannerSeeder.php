<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        Banner::updateOrCreate(
            ['title' => 'أهلاً بكم في مشاريعنا العقارية'],
            [
                'title_en' => 'Welcome to Our Real Estate Projects',
                'subtitle' => 'استثمر بأمان',
                'subtitle_en' => 'Invest with Confidence',
                'description' => 'اكتشف أحدث المشاريع السكنية والتجارية والخدمية.',
                'description_en' => 'Discover the latest residential, commercial, and service projects.',
                'image' => 'defaults/banner1.jpg',
                'project_category_id' => 1,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ]
        );
    }
}
