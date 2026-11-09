<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            CategorySeeder::class,
            LocationSeeder::class,
            TagSeeder::class,
            AdSlotSeeder::class,
            HomepageSectionSeeder::class,
            NewsSeeder::class,
            PageSeeder::class,
            AdvertisementSeeder::class,
        ]);
    }
}
