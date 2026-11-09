<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Location;
use App\Models\AuthorProfile;
use App\Models\Media;
use App\Models\User;
use App\Models\NewsVideo;
use App\Models\HomepageSection;
use App\Models\HomepageSectionItem;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $primaryCategory = Category::where('slug', 'bangladesh')->first() ?? Category::first();
        $sportsCategory = Category::where('slug', 'sports')->first() ?? Category::first();
        $worldCategory = Category::where('slug', 'world')->first() ?? Category::first();
        $techCategory = Category::where('slug', 'technology')->first() ?? Category::first();
        $opinionCategory = Category::where('slug', 'opinion')->first() ?? Category::first();
        $entertainmentCategory = Category::where('slug', 'entertainment')->first() ?? Category::first();
        $lifestyleCategory = Category::where('slug', 'lifestyle')->first() ?? Category::first();
        $businessCategory = Category::where('slug', 'business')->first() ?? Category::first();

        $dhakaLocation = Location::where('slug', 'dhaka')->first();
        $chittagongLocation = Location::where('slug', 'chattogram')->first();

        // Create Authors
        $authors = [
            ['name' => 'রিয়াদুল করিম', 'slug' => 'riyadul-karim', 'designation' => 'বিশেষ প্রতিনিধি'],
            ['name' => 'আহমেদ শামীম', 'slug' => 'ahmed-shamim', 'designation' => 'কলামিস্ট ও লেখক'],
            ['name' => 'কামাল আহমেদ', 'slug' => 'kamal-ahmed', 'designation' => 'রাজনৈতিক বিশ্লেষক'],
            ['name' => 'ড. দেবপ্রিয় ভট্টাচার্য', 'slug' => 'dr-debapriya', 'designation' => 'বিশিষ্ট অর্থনীতিবিদ'],
            ['name' => 'খেলা ডেস্ক', 'slug' => 'sports-desk', 'designation' => 'ক্রীড়া বিভাগ'],
            ['name' => 'বিজ্ঞানচিন্তা ডেস্ক', 'slug' => 'science-desk', 'designation' => 'বিজ্ঞান ও প্রযুক্তি প্রতিবেদক'],
            ['name' => 'বিনোদন প্রতিবেদক', 'slug' => 'entertainment-reporter', 'designation' => 'বিনোদন বিভাগ'],
        ];

        $authorModels = [];
        foreach ($authors as $a) {
            $authorModels[$a['slug']] = AuthorProfile::firstOrCreate(['slug' => $a['slug']], $a);
        }

        // Tags
        $allTags = Tag::all()->keyBy('slug');

        $newsItems = [
            // Lead Story
            [
                'title' => 'ধারাবাহিক লোকসানে বিআইডব্লিউটিসি, পদ্মা সেতু চালুই কি একমাত্র কারণ',
                'slug' => 'biwtc-continuous-loss-padma-bridge',
                'kicker' => 'বিশেষ অনুসন্ধান',
                'excerpt' => 'পদ্মা সেতু চালুর পর থেকেই নৌপথে যাত্রী পরিবহন কমে গেছে। তবে সংস্থাটির কর্মকর্তারা বলছেন, অব্যাহত লোকসানের কারণ শুধু এটিই নয়; অনিয়ম, অব্যবস্থাপনা এবং জ্বালানি অপচয়ও বড় কারণ।',
                'body_html' => '<p>পদ্মা সেতু চালু হওয়ার পর দক্ষিণবঙ্গের নৌপথে যাত্রী ও যানবাহন পরিবহন ব্যাপকভাবে কমেছে। তবে বাংলাদেশ অভ্যন্তরীণ নৌপরিবহন সংস্থা (বিআইডব্লিউটিসি) টানা লোকসানের মুখে পড়ার পেছনে শুধু সেতু চালু হওয়াই একমাত্র কারণ নয় বলে মনে করছেন বিশেষজ্ঞরা।</p><p>সংস্থাটির অভ্যন্তরীণ অডিট ও অনুসন্ধানে উঠে এসেছে নানা অব্যবস্থাপনা, অতিরিক্ত জ্বালানি খরচ এবং পুরাতন জলযানের উচ্চ মেরামত ব্যয়। বিগত পাঁচ বছরে সংস্থাটির পুঞ্জীভূত ক্ষতির পরিমাণ দাঁড়িয়েছে প্রায় ১৩৭ কোটি টাকা।</p>',
                'body_plain' => 'পদ্মা সেতু চালুর পর থেকেই নৌপথে যাত্রী পরিবহন কমে গেছে। তবে সংস্থাটির কর্মকর্তারা বলছেন, অব্যাহত লোকসানের কারণ শুধু এটিই নয়; অনিয়ম, অব্যবস্থাপনা এবং জ্বালানি অপচয়ও বড় কারণ।',
                'primary_category_id' => $primaryCategory->id,
                'author' => 'riyadul-karim',
                'image_url' => 'https://media.prothomalo.com/prothomalo-bangla/2025-05-11/6w3hzg1b/BarishalDH049620250511BarishalSteamer-4.jpg',
                'is_lead' => true,
                'type' => 'article',
                'location_id' => $dhakaLocation ? $dhakaLocation->id : null,
                'tags' => ['bangladesh-politics', 'economy'],
            ],
            // World
            [
                'title' => 'নেতানিয়াহু নন, ইসরায়েলে নতুন সরকার গঠনের দায়িত্ব পেলেন আইজেনকট',
                'slug' => 'eisenkot-tasked-to-form-new-israel-government',
                'kicker' => 'আন্তর্জাতিক',
                'excerpt' => 'ইসরায়েলে নতুন সরকার গঠনের জন্য বিরোধী নেতা ও সাবেক সেনাপ্রধান গাদি আইজেনকটকে আনুষ্ঠানিকভাবে আহ্বান জানানো হয়েছে।',
                'body_html' => '<p>ইসরায়েলে রাজনৈতিক অচলাবস্থার মধ্যে দেশটির প্রেসিডেন্ট নতুন সরকার গঠনের দায়িত্ব দিয়েছেন সাবেক চিফ অব জেনারেল স্টাফ গাদি আইজেনকটকে। বর্তমান প্রধানমন্ত্রী নেতানিয়াহুর নেতৃত্বাধীন জোট প্রয়োজনীয় সংখ্যাগরিষ্ঠতা প্রমাণে ব্যর্থ হওয়ার পর এ পদক্ষেপ এলো।</p>',
                'body_plain' => 'ইসরায়েলে নতুন সরকার গঠনের জন্য বিরোধী নেতা ও সাবেক সেনাপ্রধান গাদি আইজেনকটকে আনুষ্ঠানিকভাবে আহ্বান জানানো হয়েছে।',
                'primary_category_id' => $worldCategory->id,
                'author' => 'kamal-ahmed',
                'image_url' => 'https://media.prothomalo.com/prothomalo-bangla/2026-11-07/qvygax45/Benjamin-Netanyahu.avif',
                'is_lead' => false,
                'type' => 'article',
                'tags' => ['world-news', 'election'],
            ],
            // Sports 1
            [
                'title' => 'সরাসরি সিলেট টেস্ট: সিলভাকে ফিরিয়ে জুটি ভাঙলেন তাইজুল',
                'slug' => 'sylhet-test-live-taijul-breaks-partnership',
                'kicker' => 'ক্রিকেট',
                'excerpt' => 'সিলেট আন্তর্জাতিক ক্রিকেট স্টেডিয়ামে অনুষ্ঠিত টেস্ট ম্যাচে দুর্দান্ত বোলিংয়ে জুটি ভেঙেছেন তাইজুল ইসলাম।',
                'body_html' => '<p>সিলেটে দুই ম্যাচ সিরিজের প্রথম টেস্টের তৃতীয় দিনে ঘুরে দাঁড়িয়েছে বাংলাদেশ। শ্রীলঙ্কার ওপেনিং জুটিতে আঘাত হেনে সিলভাকে প্যাভিলিয়নে ফেরান বাঁহাতি স্পিনার তাইজুল ইসলাম। মধ্যাহ্নভোজের বিরতি পর্যন্ত শ্রীলঙ্কার সংগ্রহ ২ উইকেটে ১২৮ রান।</p>',
                'body_plain' => 'সিলেট আন্তর্জাতিক ক্রিকেট স্টেডিয়ামে অনুষ্ঠিত টেস্ট ম্যাচে দুর্দান্ত বোলিংয়ে জুটি ভেঙেছেন তাইজুল ইসলাম।',
                'primary_category_id' => $sportsCategory->id,
                'author' => 'sports-desk',
                'image_url' => 'https://media.prothomalo.com/prothomalo-bangla/2025-04-28/hc11kyrd/06527542-4c5b-468d-b1b0-1668f3d97a6c.jpeg',
                'is_lead' => false,
                'type' => 'article',
                'tags' => ['cricket', 'sports'],
            ],
            // Sports 2
            [
                'title' => 'রেকর্ড গড়া ইনিংস নিয়ে হৃদয় বললেন, ‘এটা আমার দীর্ঘদিনের স্বপ্ন ছিল’',
                'slug' => 'towhid-hridoy-record-century-interview',
                'kicker' => 'ক্রিকেট',
                'excerpt' => 'কঠিন পরিস্থিতিতে ব্যাট করতে নেমে অনবদ্য সেঞ্চুরি করে ম্যাচ জেতানো ইনিংস উপহার দিয়েছেন তরুণ ব্যাটার তাওহিদ হৃদয়।',
                'body_html' => '<p>ম্যাচ শেষে সংবাদ সম্মেলনে হৃদয় বলেন, "দলের জয়ে অবদান রাখতে পারাটাই সবচেয়ে বড় প্রাপ্তি। যখন উইকেটে গিয়েছিলাম তখন লক্ষ্য ছিল শুধু টিকে থাকা এবং সুযোগ বুঝে রান বাড়ানো।"</p>',
                'body_plain' => 'কঠিন পরিস্থিতিতে ব্যাট করতে নেমে অনবদ্য সেঞ্চুরি করে ম্যাচ জেতানো ইনিংস উপহার দিয়েছেন তরুণ ব্যাটার তাওহিদ হৃদয়।',
                'primary_category_id' => $sportsCategory->id,
                'author' => 'sports-desk',
                'image_url' => 'https://media.prothomalo.com/prothomalo-bangla/2025-02-14/h9w8h591/Towhid-Hridoy.jpg',
                'is_lead' => false,
                'type' => 'article',
                'tags' => ['cricket', 'sports'],
            ],
            // Tech 1
            [
                'title' => 'মরুভূমিতে মিলেছে মঙ্গল গ্রহের প্রাচীন উল্কা, গবেষণায় নতুন তথ্য',
                'slug' => 'ancient-mars-meteorite-found-in-desert',
                'kicker' => 'মহাকাশ বিজ্ঞান',
                'excerpt' => 'সাহারা মরুভূমিতে সন্ধান পাওয়া একটি উল্কাপিণ্ড বিশ্লেষণ করে বিজ্ঞানীরা কোটি কোটি বছর আগের মঙ্গল গ্রহের পরিবেশ সম্পর্কে চমকপ্রদ তথ্য পেয়েছেন।',
                'body_html' => '<p>আন্তর্জাতিক মহাকাশ গবেষকদের একটি দল জানিয়েছে, সম্প্রতি মরক্কোর মরুভূমি থেকে সংগৃহীত একটি উল্কাপিণ্ড আসলে মঙ্গল গ্রহ থেকে ছিটকে আসা অংশ। এটি থেকে গ্রহটির প্রাচীন পানির অস্তিত্ব ও রাসায়নিক গঠন সম্পর্কে নতুন তথ্য জানা গেছে।</p>',
                'body_plain' => 'সাহারা মরুভূমিতে সন্ধান পাওয়া একটি উল্কাপিণ্ড বিশ্লেষণ করে বিজ্ঞানীরা কোটি কোটি বছর আগের মঙ্গল গ্রহের পরিবেশ সম্পর্কে চমকপ্রদ তথ্য পেয়েছেন।',
                'primary_category_id' => $techCategory->id,
                'author' => 'science-desk',
                'image_url' => 'https://media.prothomalo.com/prothomalo-bangla/2026-11-07/g8olfm3y/mars.jpg',
                'is_lead' => false,
                'type' => 'article',
                'tags' => ['technology', 'artificial-intelligence'],
            ],
            // Tech 2
            [
                'title' => 'নতুন কৃত্রিম বুদ্ধিমত্তা মডেল আনল ওপেনএআই, কী থাকছে এতে',
                'slug' => 'openai-releases-new-reasoning-ai-model',
                'kicker' => 'প্রযুক্তি',
                'excerpt' => 'গাণিতিক সমস্যা সমাধান এবং জটিল কোডিংয়ে মানুষের চেয়েও দ্রুত কাজ করতে পারে নতুন প্রজন্মের এই এআই মডেল।',
                'body_html' => '<p>প্রযুক্তি বিশ্বে নতুন আলোড়ন সৃষ্টি করে ওপেনএআই উন্মোচন করেছে তাদের সর্বাধুনিক এআই মডেল। এটি চিন্তাভাবনা করে ধাপে ধাপে জটিল প্রশ্নের উত্তর তৈরি করতে সক্ষম।</p>',
                'body_plain' => 'গাণিতিক সমস্যা সমাধান এবং জটিল কোডিংয়ে মানুষের চেয়েও দ্রুত কাজ করতে পারে নতুন প্রজন্মের এই এআই মডেল।',
                'primary_category_id' => $techCategory->id,
                'author' => 'science-desk',
                'image_url' => 'https://media.prothomalo.com/prothomalo-bangla/2026-11-08/eyjry2rr/Untitled-1.png',
                'is_lead' => false,
                'type' => 'article',
                'tags' => ['artificial-intelligence', 'technology'],
            ],
            // Opinion 1
            [
                'title' => 'গণতান্ত্রিক সংস্কার ও আগামী নির্বাচনের পথরেখা',
                'slug' => 'democratic-reforms-and-election-roadmap',
                'kicker' => 'মতামত',
                'excerpt' => 'দেশের রাজনৈতিক স্থিতিশীলতা এবং প্রাতিষ্ঠানিক জবাবদিহিতা নিশ্চিত করতে নির্বাচনী সংস্কারের বিকল্প নেই।',
                'body_html' => '<p>একটি সুষ্ঠু ও অন্তর্ভুক্তিমূলক গণতান্ত্রিক সমাজ বিনির্মাণে নির্বাচন কমিশনসহ সব সাংবিধানিক প্রতিষ্ঠানের কাঠামোগত সংস্কার এখন সময়ের দাবি। নাগরিকদের ভোটাধিকার সুরক্ষায় স্বচ্ছ প্রাতিষ্ঠানিক রূপরেখা গড়ে তোলা জরুরি।</p>',
                'body_plain' => 'একটি সুষ্ঠু ও অন্তর্ভুক্তিমূলক গণতান্ত্রিক সমাজ বিনির্মাণে নির্বাচন কমিশনসহ সব সাংবিধানিক প্রতিষ্ঠানের কাঠামোগত সংস্কার এখন সময়ের দাবি।',
                'primary_category_id' => $opinionCategory->id,
                'author' => 'ahmed-shamim',
                'image_url' => 'https://media.prothomalo.com/prothomalo-bangla/2026-11-07/w78eyf3o/Shamim.jpg',
                'is_lead' => false,
                'type' => 'opinion',
                'tags' => ['election', 'bangladesh-politics'],
            ],
            // Opinion 2
            [
                'title' => 'ঋণের এই মহাসাগর থেকে কীভাবে উদ্ধার পাবে অর্থনীতি',
                'slug' => 'how-to-recover-from-national-debt-burden',
                'kicker' => 'অর্থনীতি বিশ্লেষণ',
                'excerpt' => 'মেগা প্রকল্পগুলোর আর্থিক কার্যকারিতা পর্যালোচনা এবং রাজস্ব আয় বৃদ্ধি ছাড়া বাজেট ঘাটতি কমানো অসম্ভব।',
                'body_html' => '<p>অভ্যন্তরীণ ও বৈদেশিক ঋণের দায় পরিশোধে প্রতিবছর বাজেটের একটি বড় অংশ ব্যয় হয়ে যাচ্ছে। অর্থনীতিকে স্বস্তিতে ফেরাতে হলে কর জিডিপি অনুপাত বৃদ্ধি এবং অপ্রয়োজনীয় পরিচালন ব্যয় কমাতে হবে।</p>',
                'body_plain' => 'মেগা প্রকল্পগুলোর আর্থিক কার্যকারিতা পর্যালোচনা এবং রাজস্ব আয় বৃদ্ধি ছাড়া বাজেট ঘাটতি কমানো অসম্ভব।',
                'primary_category_id' => $opinionCategory->id,
                'author' => 'dr-debapriya',
                'image_url' => 'https://media.prothomalo.com/prothomalo-bangla/2026-11-05/8jtyqf1a/Debapriya.jpg',
                'is_lead' => false,
                'type' => 'opinion',
                'tags' => ['economy'],
            ],
            // Video 1
            [
                'title' => 'বাজারে এত ছেঁড়া নোট কেন? জেনে নিন আসল কারণ',
                'slug' => 'why-so-many-torn-notes-in-market',
                'kicker' => 'ভিডিও প্রতিবেদন',
                'excerpt' => 'কেনাকাটার সময় ছেঁড়াফাটা টাকা নিয়ে ভোগান্তিতে পড়ছেন সাধারণ মানুষ। ব্যাংকগুলো কেন এসব নোট প্রত্যাহার করছে না?',
                'body_html' => '<p>রাজধানীর বিভিন্ন বাজারে ছেঁড়াফাটা টাকার লেনদেন নিয়ে প্রতিদিন তৈরি হচ্ছে অসন্তোষ। বাংলাদেশ ব্যাংকের নির্দেশনা থাকার পরও কেন বাজারে এত ছেঁড়া নোট ঘুরছে—তা নিয়ে বিশেষ ভিডিও প্রতিবেদন।</p>',
                'body_plain' => 'কেনাকাটার সময় ছেঁড়াফাটা টাকা নিয়ে ভোগান্তিতে পড়ছেন সাধারণ মানুষ। ব্যাংকগুলো কেন এসব নোট প্রত্যাহার করছে না?',
                'primary_category_id' => $primaryCategory->id,
                'author' => 'riyadul-karim',
                'image_url' => 'https://media.prothomalo.com/prothomalo-bangla/2026-11-07/qfsi91dd/web.jpg',
                'is_lead' => false,
                'type' => 'video',
                'video_provider' => 'youtube',
                'video_id' => 'dQw4w9WgXcQ',
                'tags' => ['economy', 'bangladesh-politics'],
            ],
            // Video 2
            [
                'title' => 'সাজেকের বদলে রাঙামাটিতে পর্যটকদের উপচে পড়া ভিড়',
                'slug' => 'tourists-crowd-in-rangamati-instead-of-sajek',
                'kicker' => 'ভিডিও দৃশ্য',
                'excerpt' => 'ছুটির দিনে কাপ্তাই হ্রদ ও ঝুলন্ত সেতু দেখতে হাজারো ভ্রমণপিপাসুর ভিড় জমেছে পাহাড়ি জেলা রাঙামাটিতে।',
                'body_html' => '<p>ঘন সবুজের কোলে কাপ্তাই হ্রদের শান্ত পানি আর ঝুলন্ত সেতু। পর্যটকদের অপেক্ষায় ঘাটে সারি সারি বোট নোঙর করে রাখা হয়েছে। সাজেকে যাতায়াতে কড়াকড়ির কারণে ভ্রমণপ্রেমীরা এখন রাঙামাটির প্রাকৃতিক সৌন্দর্য উপভোগ করতে ছুটে আসছেন।</p>',
                'body_plain' => 'ছুটির দিনে কাপ্তাই হ্রদ ও ঝুলন্ত সেতু দেখতে হাজারো ভ্রমণপিপাসুর ভিড় জমেছে পাহাড়ি জেলা রাঙামাটিতে।',
                'primary_category_id' => $primaryCategory->id,
                'author' => 'riyadul-karim',
                'image_url' => 'https://media.prothomalo.com/prothomalo-bangla/2026-11-07/r1vezluy/5.-RangamatitourismKaptai-Lakehillshandicraftslocal-fruits08112026.jpg',
                'is_lead' => false,
                'type' => 'video',
                'video_provider' => 'youtube',
                'video_id' => 'dQw4w9WgXcQ',
                'location_id' => $chittagongLocation ? $chittagongLocation->id : null,
                'tags' => ['bangladesh-politics'],
            ],
            // Entertainment
            [
                'title' => 'সালমান খানের ফোন ও হুমকির পর যেভাবে মোড় নিয়েছিল বিবেকের জীবন',
                'slug' => 'vivek-oberoi-salman-khan-controversy-retrospective',
                'kicker' => 'বলিউড',
                'excerpt' => 'ক্যারিয়ারের তুঙ্গে থাকা অবস্থায় একটি বিতর্ক কীভাবে একজন সম্ভাবনাময় অভিনেতার পথচলা পাল্টে দিয়েছিল।',
                'body_html' => '<p>বলিউডে একসময় দারুণ জনপ্রিয় হয়ে ওঠা বিবেক ওবেরয় সম্প্রতি এক সাক্ষাৎকারে মুখ খুলেছেন তাঁর ফেলে আসা সেই বিতর্কিত অধ্যায় নিয়ে। কীভাবে ক্যারিয়ারের গতি থমকে গিয়েছিল, তা জানিয়েছেন তিনি।</p>',
                'body_plain' => 'ক্যারিয়ারের তুঙ্গে থাকা অবস্থায় একটি বিতর্ক কীভাবে একজন সম্ভাবনাময় অভিনেতার পথচলা পাল্টে দিয়েছিল।',
                'primary_category_id' => $entertainmentCategory->id,
                'author' => 'entertainment-reporter',
                'image_url' => 'https://media.prothomalo.com/prothomalo-bangla/2026-11-07/esxks7wu/32.jpeg',
                'is_lead' => false,
                'type' => 'article',
                'tags' => ['entertainment'],
            ],
            // Bangladesh Local
            [
                'title' => 'ঠাকুরগাঁওয়ে আমন ধানের বাম্পার ফলন, তবে দাম নিয়ে কৃষকের শঙ্কা',
                'slug' => 'thakurgaon-aman-paddy-bumper-harvest-farmers-concern',
                'kicker' => 'জেলা সংবাদ',
                'excerpt' => 'অনুকূল আবহাওয়ায় এবার ভালো ফলন হলেও বাজারে ন্যায্যমূল্য না পাওয়ার আশঙ্কায় রয়েছেন প্রান্তিক চাষিরা।',
                'body_html' => '<p>উত্তরাঞ্চলের জেলাগুলোতে আমন ধান কাটার ধুম পড়েছে। ফলন আশাতীত ভালো হলেও ডিজেল ও সারের বাড়তি খরচের কারণে উৎপাদন খরচ তুলতে পারবেন কি না, তা নিয়ে উদ্বিগ্ন কৃষকেরা।</p>',
                'body_plain' => 'অনুকূল আবহাওয়ায় এবার ভালো ফলন হলেও বাজারে ন্যায্যমূল্য না পাওয়ার আশঙ্কায় রয়েছেন প্রান্তিক চাষিরা।',
                'primary_category_id' => $primaryCategory->id,
                'author' => 'riyadul-karim',
                'image_url' => 'https://media.prothomalo.com/prothomalo-bangla/2025-05-11/6w3hzg1b/BarishalDH049620250511BarishalSteamer-4.jpg',
                'is_lead' => false,
                'type' => 'article',
                'tags' => ['economy'],
            ],
            // Business
            [
                'title' => 'পোশাক রপ্তানিতে নতুন বাজার খুঁজছেন উদ্যোক্তারা, বাড়ল প্রবৃদ্ধি',
                'slug' => 'apparel-exports-growth-new-global-markets',
                'kicker' => 'বাণিজ্য',
                'excerpt' => 'প্রচলিত ইউরোপ ও আমেরিকা বাজারের পাশাপাশি অস্ট্রেলিয়া ও জাপানে বাংলাদেশের তৈরি পোশাকের রপ্তানি উল্লেখযোগ্য হারে বাড়ছে।',
                'body_html' => '<p>রপ্তানি উন্নয়ন ব্যুরো (ইপিবি)-র তথ্য অনুযায়ী চলতি অর্থবছরের প্রথম চার মাসে অপ্রচলিত বাজারে তৈরি পোশাক রপ্তানিতে প্রায় ১৮ শতাংশ প্রবৃদ্ধি অর্জিত হয়েছে।</p>',
                'body_plain' => 'প্রচলিত ইউরোপ ও আমেরিকা বাজারের পাশাপাশি অস্ট্রেলিয়া ও জাপানে বাংলাদেশের তৈরি পোশাকের রপ্তানি উল্লেখযোগ্য হারে বাড়ছে।',
                'primary_category_id' => $businessCategory->id,
                'author' => 'riyadul-karim',
                'image_url' => 'https://media.prothomalo.com/prothomalo-bangla/2026-11-08/eyjry2rr/Untitled-1.png',
                'is_lead' => false,
                'type' => 'article',
                'tags' => ['economy'],
            ],
        ];

        $heroSection = HomepageSection::where('key', 'hero_top')->first();

        foreach ($newsItems as $idx => $item) {
            $media = Media::firstOrCreate(
                ['path' => $item['image_url']],
                [
                    'uploaded_by' => $admin->id,
                    'disk' => 'public',
                    'original_name' => basename($item['image_url']),
                    'mime_type' => 'image/jpeg',
                    'size_bytes' => 102400,
                    'width' => 800,
                    'height' => 500,
                    'status' => 'active',
                ]
            );

            $news = News::firstOrCreate(
                ['slug' => $item['slug']],
                [
                    'public_id' => (string) Str::ulid(),
                    'title' => $item['title'],
                    'kicker' => $item['kicker'] ?? null,
                    'excerpt' => $item['excerpt'],
                    'body_html' => $item['body_html'],
                    'body_plain' => $item['body_plain'],
                    'type' => $item['type'] ?? 'article',
                    'status' => 'published',
                    'primary_category_id' => $item['primary_category_id'],
                    'location_id' => $item['location_id'] ?? null,
                    'cover_media_id' => $media->id,
                    'created_by' => $admin->id,
                    'published_by' => $admin->id,
                    'approved_by' => $admin->id,
                    'published_at' => now()->subMinutes($idx * 25),
                    'content_updated_at' => now(),
                    'comments_enabled' => true,
                    'lock_version' => 1,
                ]
            );

            // Video model
            if (($item['type'] ?? '') === 'video' && isset($item['video_provider'])) {
                NewsVideo::firstOrCreate(
                    ['news_id' => $news->id],
                    [
                        'provider' => $item['video_provider'],
                        'provider_video_id' => $item['video_id'],
                        'duration_seconds' => 180,
                    ]
                );
            }

            // Attach Author
            if (isset($authorModels[$item['author']])) {
                $news->authors()->syncWithoutDetaching([$authorModels[$item['author']]->id => ['sort_order' => 1]]);
            }

            // Attach Tags
            if (!empty($item['tags'])) {
                $tagIds = [];
                foreach ($item['tags'] as $tagSlug) {
                    if (isset($allTags[$tagSlug])) {
                        $tagIds[] = $allTags[$tagSlug]->id;
                    }
                }
                if (!empty($tagIds)) {
                    $news->tags()->syncWithoutDetaching($tagIds);
                }
            }

            // Pin lead story
            if ($item['is_lead'] && $heroSection) {
                HomepageSectionItem::firstOrCreate(
                    ['section_id' => $heroSection->id, 'news_id' => $news->id],
                    ['position' => 1, 'starts_at' => now()]
                );
            }
        }
    }
}
