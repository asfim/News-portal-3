<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Location;
use App\Models\HomepageSection;
use App\Models\AdSlot;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Lead / Top Stories (Hero section)
        $heroNews = News::published()
            ->with(['primaryCategory', 'authors', 'coverMedia'])
            ->orderByDesc('published_at')
            ->take(15)
            ->get();

        $leadStory = $heroNews->first();
        $leftStories = $heroNews->slice(1, 5);
        $centerSubStories = $heroNews->slice(6, 4);
        $rightStories = $heroNews->slice(10, 4);

        // 2. Trending Tags
        $trendingTags = Tag::where('is_featured', true)
            ->orderBy('sort_order')
            ->take(10)
            ->get();

        // 3. For You (Personalized / Latest Recommendations)
        $forYouStories = News::published()
            ->with(['primaryCategory', 'coverMedia'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        // 4. Video Stories
        $videoStories = News::published()
            ->where(function ($q) {
                $q->where('type', 'video')
                  ->orWhereHas('primaryCategory', fn($c) => $c->where('slug', 'video'));
            })
            ->with(['primaryCategory', 'coverMedia', 'video'])
            ->orderByDesc('published_at')
            ->take(8)
            ->get();

        if ($videoStories->isEmpty()) {
            $videoStories = News::published()->with(['primaryCategory', 'coverMedia'])->take(4)->get();
        }

        // 5. Tabs: Latest & Most Read
        $latestNews = News::published()
            ->with(['primaryCategory', 'coverMedia'])
            ->orderByDesc('published_at')
            ->take(6)
            ->get();

        $mostReadNews = News::published()
            ->with(['primaryCategory', 'coverMedia'])
            ->withCount('dailyStats')
            ->orderByDesc('daily_stats_count')
            ->orderByDesc('published_at')
            ->take(6)
            ->get();

        // 6. Sports (খেলা)
        $sportsCategory = Category::where('slug', 'sports')->first();
        $sportsNews = $sportsCategory 
            ? News::published()->where('primary_category_id', $sportsCategory->id)->with(['primaryCategory', 'coverMedia'])->orderByDesc('published_at')->take(5)->get()
            : collect();

        // 7. Bangladesh (বাংলাদেশ)
        $bdCategory = Category::where('slug', 'bangladesh')->first();
        $bdNews = $bdCategory
            ? News::published()->where('primary_category_id', $bdCategory->id)->with(['primaryCategory', 'coverMedia'])->orderByDesc('published_at')->take(7)->get()
            : collect();

        // 8. Opinion (মতামত)
        $opinionCategory = Category::where('slug', 'opinion')->first();
        $opinionNews = $opinionCategory
            ? News::published()->where('primary_category_id', $opinionCategory->id)->with(['primaryCategory', 'authors', 'coverMedia'])->orderByDesc('published_at')->take(4)->get()
            : collect();

        // 9. Technology (বিজ্ঞান ও প্রযুক্তি)
        $techCategory = Category::where('slug', 'technology')->first();
        $techNews = $techCategory
            ? News::published()->where('primary_category_id', $techCategory->id)->with(['primaryCategory', 'coverMedia'])->orderByDesc('published_at')->take(5)->get()
            : collect();

        // 10. Entertainment (বিনোদন)
        $entCategory = Category::where('slug', 'entertainment')->first();
        $entNews = $entCategory
            ? News::published()->where('primary_category_id', $entCategory->id)->with(['primaryCategory', 'coverMedia'])->orderByDesc('published_at')->take(4)->get()
            : collect();

        // 11. World (বিশ্ব)
        $worldCategory = Category::where('slug', 'world')->first();
        $worldNews = $worldCategory
            ? News::published()->where('primary_category_id', $worldCategory->id)->with(['primaryCategory', 'coverMedia'])->orderByDesc('published_at')->take(4)->get()
            : collect();

        // 12. Divisions for Location Selector
        $divisions = Location::divisions()->with('children')->get();

        // 13. Navigation Categories
        $navCategories = Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(12)->get();

        // 14. Ad Slots
        $adSlots = AdSlot::with('activeAdvertisements')->get()->keyBy('key');

        return view('frontend.home', compact(
            'leadStory',
            'leftStories',
            'centerSubStories',
            'rightStories',
            'trendingTags',
            'forYouStories',
            'videoStories',
            'latestNews',
            'mostReadNews',
            'sportsNews',
            'bdNews',
            'opinionNews',
            'techNews',
            'entNews',
            'worldNews',
            'divisions',
            'navCategories',
            'adSlots'
        ));
    }
}
