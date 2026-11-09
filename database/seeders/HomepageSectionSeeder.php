<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomepageSection;

class HomepageSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'key' => 'hero_top',
                'title' => 'প্রধান সংবাদ',
                'layout' => 'hero_3col',
                'source_type' => 'latest',
                'item_limit' => 10,
                'sort_order' => 1,
            ],
            [
                'key' => 'trending_topics',
                'title' => 'আলোচিত বিষয়',
                'layout' => 'tags_bar',
                'source_type' => 'latest',
                'item_limit' => 10,
                'sort_order' => 2,
            ],
            [
                'key' => 'for_you',
                'title' => 'আপনার জন্য',
                'layout' => 'grid_4col',
                'source_type' => 'latest',
                'item_limit' => 4,
                'sort_order' => 3,
            ],
            [
                'key' => 'video_carousel',
                'title' => 'ভিডিও',
                'layout' => 'video_carousel',
                'source_type' => 'latest',
                'item_limit' => 8,
                'sort_order' => 4,
            ],
            [
                'key' => 'tabs_and_sports',
                'title' => 'সর্বশেষ, সর্বাধিক পঠিত ও খেলা',
                'layout' => 'mid_3col',
                'source_type' => 'latest',
                'item_limit' => 10,
                'sort_order' => 5,
            ],
            [
                'key' => 'bangladesh',
                'title' => 'বাংলাদেশ',
                'layout' => 'section_lead_quad',
                'source_type' => 'category',
                'item_limit' => 7,
                'sort_order' => 6,
            ],
            [
                'key' => 'opinion',
                'title' => 'মতামত',
                'layout' => 'opinion_split',
                'source_type' => 'category',
                'item_limit' => 5,
                'sort_order' => 7,
            ],
            [
                'key' => 'technology',
                'title' => 'বিজ্ঞান ও প্রযুক্তি',
                'layout' => 'beige_tech',
                'source_type' => 'category',
                'item_limit' => 5,
                'sort_order' => 8,
            ],
            [
                'key' => 'dark_video_hub',
                'title' => 'ভিডিও হাব',
                'layout' => 'dark_video_hub',
                'source_type' => 'latest',
                'item_limit' => 4,
                'sort_order' => 9,
            ],
            [
                'key' => 'publications',
                'title' => 'অন্যান্য প্রকাশনা ও উদ্যোগ',
                'layout' => 'pubs_grid',
                'source_type' => 'manual',
                'item_limit' => 4,
                'sort_order' => 10,
            ],
        ];

        foreach ($sections as $sec) {
            HomepageSection::firstOrCreate(['key' => $sec['key']], $sec);
        }
    }
}

