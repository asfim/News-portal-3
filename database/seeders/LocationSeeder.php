<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            'dhaka' => [
                'name' => 'ঢাকা',
                'districts' => [
                    ['name' => 'ঢাকা', 'slug' => 'dhaka-dist'],
                    ['name' => 'গাজীপুর', 'slug' => 'gazipur'],
                    ['name' => 'নারায়ণগঞ্জ', 'slug' => 'narayanganj'],
                    ['name' => 'টাঙ্গাইল', 'slug' => 'tangail'],
                    ['name' => 'ফরিদপুর', 'slug' => 'faridpur'],
                ]
            ],
            'chattogram' => [
                'name' => 'চট্টগ্রাম',
                'districts' => [
                    ['name' => 'চট্টগ্রাম', 'slug' => 'chattogram-dist'],
                    ['name' => 'কক্সবাজার', 'slug' => 'coxs-bazar'],
                    ['name' => 'কুমিল্লা', 'slug' => 'cumilla'],
                    ['name' => 'নোয়াখালী', 'slug' => 'noakhali'],
                ]
            ],
            'rajshahi' => [
                'name' => 'রাজশাহী',
                'districts' => [
                    ['name' => 'রাজশাহী', 'slug' => 'rajshahi-dist'],
                    ['name' => 'বগুড়া', 'slug' => 'bogura'],
                    ['name' => 'সিরাজগঞ্জ', 'slug' => 'sirajganj'],
                    ['name' => 'পাবনা', 'slug' => 'pabna'],
                ]
            ],
            'khulna' => [
                'name' => 'খুলনা',
                'districts' => [
                    ['name' => 'খুলনা', 'slug' => 'khulna-dist'],
                    ['name' => 'যশোর', 'slug' => 'jashore'],
                    ['name' => 'কুষ্টিয়া', 'slug' => 'kushtia'],
                ]
            ],
            'barishal' => [
                'name' => 'বরিশাল',
                'districts' => [
                    ['name' => 'বরিশাল', 'slug' => 'barishal-dist'],
                    ['name' => 'পটুয়াখালী', 'slug' => 'patuakhali'],
                    ['name' => 'ভোলা', 'slug' => 'bhola'],
                ]
            ],
            'sylhet' => [
                'name' => 'সিলেট',
                'districts' => [
                    ['name' => 'সিলেট', 'slug' => 'sylhet-dist'],
                    ['name' => 'মৌলভীবাজার', 'slug' => 'moulvibazar'],
                    ['name' => 'হবিগঞ্জ', 'slug' => 'habiganj'],
                    ['name' => 'সুনামগঞ্জ', 'slug' => 'sunamganj'],
                ]
            ],
            'rangpur' => [
                'name' => 'রংপুর',
                'districts' => [
                    ['name' => 'রংপুর', 'slug' => 'rangpur-dist'],
                    ['name' => 'দিনাজপুর', 'slug' => 'dinajpur'],
                    ['name' => 'কুড়িগ্রাম', 'slug' => 'kurigram'],
                ]
            ],
            'mymensingh' => [
                'name' => 'ময়মনসিংহ',
                'districts' => [
                    ['name' => 'ময়মনসিংহ', 'slug' => 'mymensingh-dist'],
                    ['name' => 'জামালপুর', 'slug' => 'jamalpur'],
                    ['name' => 'শেরপুর', 'slug' => 'sherpur'],
                ]
            ],
        ];

        foreach ($divisions as $slug => $data) {
            $div = Location::firstOrCreate(
                ['slug' => $slug],
                [
                    'type' => 'division',
                    'name' => $data['name'],
                    'code' => strtoupper($slug),
                ]
            );

            foreach ($data['districts'] as $dist) {
                Location::firstOrCreate(
                    ['slug' => $dist['slug']],
                    [
                        'parent_id' => $div->id,
                        'type' => 'district',
                        'name' => $dist['name'],
                    ]
                );
            }
        }
    }
}

