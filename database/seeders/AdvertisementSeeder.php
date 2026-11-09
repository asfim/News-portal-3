<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Advertisement;
use App\Models\AdSlot;

class AdvertisementSeeder extends Seeder
{
    public function run(): void
    {
        $ad1 = Advertisement::firstOrCreate(
            ['title' => 'এইচ টেক প্রোভিশন ক্লাউড হোস্টিং ও সফটওয়্যার সলিউশন'],
            [
                'destination_url' => 'https://htechprovision.com/services',
                'provider_type' => 'direct',
                'starts_at' => now()->subDay(),
                'status' => 'active',
                'priority' => 10,
                'sponsor_name' => 'H Tech Provision IT',
            ]
        );

        $ad2 = Advertisement::firstOrCreate(
            ['title' => 'ভিসা কার্ডের বিশেষ ছাড় ও ক্যাশব্যাক অফার'],
            [
                'destination_url' => 'https://example.com/visa-offer',
                'provider_type' => 'direct',
                'starts_at' => now()->subDay(),
                'status' => 'active',
                'priority' => 5,
                'sponsor_name' => 'Visa Bangladesh',
            ]
        );

        $leaderboardSlot = AdSlot::where('key', 'header_leaderboard')->first();
        if ($leaderboardSlot) {
            $ad1->slots()->syncWithoutDetaching([$leaderboardSlot->id]);
        }

        $rectSlot = AdSlot::where('key', 'home_right_rectangle')->first();
        if ($rectSlot) {
            $ad1->slots()->syncWithoutDetaching([$rectSlot->id]);
        }

        $sportsSlot = AdSlot::where('key', 'sports_section_ad')->first();
        if ($sportsSlot) {
            $ad2->slots()->syncWithoutDetaching([$sportsSlot->id]);
        }

        $articleSlot = AdSlot::where('key', 'in_article_banner')->first();
        if ($articleSlot) {
            $ad1->slots()->syncWithoutDetaching([$articleSlot->id]);
        }
    }
}

