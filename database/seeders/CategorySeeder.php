<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'বিশেষ সংবাদ', 'slug' => 'special-news', 'sort_order' => 1],
            ['name' => 'রাজনীতি', 'slug' => 'politics', 'sort_order' => 2],
            ['name' => 'বাংলাদেশ', 'slug' => 'bangladesh', 'sort_order' => 3],
            ['name' => 'অপরাধ', 'slug' => 'crime', 'sort_order' => 4],
            ['name' => 'বিশ্ব', 'slug' => 'world', 'sort_order' => 5],
            ['name' => 'বাণিজ্য', 'slug' => 'business', 'sort_order' => 6],
            ['name' => 'মতামত', 'slug' => 'opinion', 'sort_order' => 7],
            ['name' => 'খেলা', 'slug' => 'sports', 'sort_order' => 8],
            ['name' => 'বিনোদন', 'slug' => 'entertainment', 'sort_order' => 9],
            ['name' => 'চাকরি', 'slug' => 'jobs', 'sort_order' => 10],
            ['name' => 'জীবনযাপন', 'slug' => 'lifestyle', 'sort_order' => 11],
            ['name' => 'বিজ্ঞান ও প্রযুক্তি', 'slug' => 'technology', 'sort_order' => 12],
            ['name' => 'শিক্ষা', 'slug' => 'education', 'sort_order' => 13],
            ['name' => 'পরিবেশ', 'slug' => 'environment', 'sort_order' => 14],
            ['name' => 'ছবি', 'slug' => 'photo', 'sort_order' => 15],
            ['name' => 'ভিডিও', 'slug' => 'video', 'sort_order' => 16],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}

