<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'সিলেট টেস্ট', 'slug' => 'sylhet-test', 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'যুক্তরাষ্ট্র নির্বাচন', 'slug' => 'usa-election', 'is_featured' => true, 'sort_order' => 2],
            ['name' => 'পরীমনি', 'slug' => 'pori-moni', 'is_featured' => true, 'sort_order' => 3],
            ['name' => 'বিআইডব্লিউটিসি', 'slug' => 'biwtc', 'is_featured' => true, 'sort_order' => 4],
            ['name' => 'ডেঙ্গু পরিস্থিতি', 'slug' => 'dengue-situation', 'is_featured' => true, 'sort_order' => 5],
            ['name' => 'ইসরায়েল', 'slug' => 'israel', 'is_featured' => true, 'sort_order' => 6],
            ['name' => 'পোশাকশ্রমিক', 'slug' => 'garments-workers', 'is_featured' => true, 'sort_order' => 7],
            ['name' => 'শীতের আবহাওয়া', 'slug' => 'winter-weather', 'is_featured' => true, 'sort_order' => 8],
            ['name' => 'মেরি কুরি', 'slug' => 'marie-curie', 'is_featured' => true, 'sort_order' => 9],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['slug' => $tag['slug']], $tag);
        }
    }
}

