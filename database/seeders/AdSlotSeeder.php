<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdSlot;

class AdSlotSeeder extends Seeder
{
    public function run(): void
    {
        $slots = [
            [
                'key' => 'header_leaderboard',
                'name' => 'Header Top Leaderboard',
                'desktop_width' => 970,
                'desktop_height' => 90,
                'mobile_width' => 320,
                'mobile_height' => 50,
            ],
            [
                'key' => 'home_right_rectangle',
                'name' => 'Homepage Hero Right Rectangle',
                'desktop_width' => 300,
                'desktop_height' => 250,
                'mobile_width' => 300,
                'mobile_height' => 250,
            ],
            [
                'key' => 'mid_page_leaderboard',
                'name' => 'Middle Full-Width Banner',
                'desktop_width' => 970,
                'desktop_height' => 90,
                'mobile_width' => 320,
                'mobile_height' => 100,
            ],
            [
                'key' => 'sports_section_ad',
                'name' => 'Sports Section Sponsor Ad (Visa)',
                'desktop_width' => 300,
                'desktop_height' => 120,
                'mobile_width' => 300,
                'mobile_height' => 100,
            ],
            [
                'key' => 'in_article_banner',
                'name' => 'Article Detail In-Body Ad',
                'desktop_width' => 640,
                'desktop_height' => 100,
                'mobile_width' => 300,
                'mobile_height' => 250,
            ],
            [
                'key' => 'footer_banner',
                'name' => 'Footer Banner',
                'desktop_width' => 970,
                'desktop_height' => 90,
                'mobile_width' => 320,
                'mobile_height' => 50,
            ],
        ];

        foreach ($slots as $slot) {
            AdSlot::firstOrCreate(['key' => $slot['key']], $slot);
        }
    }
}

