<?php

namespace App\Services;

use App\Models\News;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Location;
use App\Models\HomepageSection;
use App\Models\AdSlot;
use Illuminate\Support\Facades\Cache;

class HomepageBuilderService
{
    const CACHE_KEY = 'homepage_rendered_data';
    const CACHE_TTL = 300; // 5 minutes cache

    /**
     * Resolve all homepage sections and components with caching and fallbacks.
     */
    public function resolveHomepageData(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            $this->invalidateCache();
        }

        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->buildHomepageData();
        });
    }

    /**
     * Invalidate homepage cache when news or sections change.
     */
    public function invalidateCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Build the full composite homepage dataset.
     */
    protected function buildHomepageData(): array
    {
        // 1. Fetch active sections
        $sections = HomepageSection::with(['items.news'])->orderBy('sort_order')->get()->keyBy('key');

        // 2. Lead / Top Stories (Hero composite grid)
        $heroLimit = $sections->has('hero_top') ? $sections['hero_top']->item_limit : 30;
        $heroLimit = max($heroLimit, 30);

        // Check if manual items are pinned in hero_top
        $pinnedNewsIds = collect();
        if ($sections->has('hero_top') && $sections['hero_top']->items->isNotEmpty()) {
            $pinnedNewsIds = $sections['hero_top']->items
                ->filter(function ($item) {
                    $now = now();
                    if ($item->starts_at && $item->starts_at->isFuture()) return false;
                    if ($item->ends_at && $item->ends_at->isPast()) return false;
                    return $item->news && $item->news->isPublished();
                })
                ->pluck('news_id');
        }

        $heroNews = collect();
        if ($pinnedNewsIds->isNotEmpty()) {
            $heroNews = News::published()
                ->whereIn('id', $pinnedNewsIds)
                ->with(['primaryCategory', 'authors', 'coverMedia'])
                ->get()
                ->sortBy(function ($item) use ($pinnedNewsIds) {
                    return $pinnedNewsIds->search($item->id);
                });
        }

        $remainingHeroCount = $heroLimit - $heroNews->count();
        if ($remainingHeroCount > 0) {
            $freshHeroNews = News::published()
                ->whereNotIn('id', $heroNews->pluck('id'))
                ->with(['primaryCategory', 'authors', 'coverMedia'])
                ->orderByDesc('published_at')
                ->take($remainingHeroCount)
                ->get();
            $heroNews = $heroNews->concat($freshHeroNews);
        }

        $leadStory = $heroNews->first();
        $leftStories = $heroNews->slice(1, 5);
        $centerSubStories = $heroNews->slice(6, 4);
        $rightStories = $heroNews->slice(10, 4);

        // Opinion (মতামত)
        $opinionCategory = Category::where('slug', 'opinion')->first();
        $opinionNews = $opinionCategory
            ? News::published()->where('primary_category_id', $opinionCategory->id)->with(['primaryCategory', 'authors', 'coverMedia'])->orderByDesc('published_at')->take(6)->get()
            : collect();

        // Hero slices
        $leftTopStories = $heroNews->slice(1, 2);
        $leftMiddleStories = $heroNews->slice(3, 2);
        $leftTextStories = $heroNews->slice(5, 5);

        $featureLeadStory = $opinionNews->first() ?? $heroNews->slice(10, 1)->first() ?? $leadStory;
        $featureSubStories = $opinionNews->slice(1, 4);
        if ($featureSubStories->count() < 4) {
            $featureSubStories = $featureSubStories->concat($heroNews->slice(11, 4 - $featureSubStories->count()));
        }

        $centerRow3Stories = $heroNews->slice(15, 2);
        $centerRow4Stories = $heroNews->slice(17, 3);
        $centerRow5Stories = $heroNews->slice(20, 3);
        $rightInDepthStories = $heroNews->slice(23, 3);

        // 3. Trending Tags
        $trendingTags = Tag::where('is_featured', true)
            ->orderBy('sort_order')
            ->take(10)
            ->get();

        // 4. For You Stories (Recommendations / Fallback)
        $forYouStories = News::published()
            ->with(['primaryCategory', 'coverMedia'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        // 5. Video Stories
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

        // 6. Tabs: Latest & Most Read (Accurate Views Subquery)
        $latestNews = News::published()
            ->with(['primaryCategory', 'coverMedia'])
            ->orderByDesc('published_at')
            ->take(6)
            ->get();

        $mostReadNews = News::published()
            ->with(['primaryCategory', 'coverMedia'])
            ->mostRead(7)
            ->take(6)
            ->get();

        // 7. Category Slices
        $sportsCategory = Category::where('slug', 'sports')->first();
        $sportsNews = $sportsCategory 
            ? News::published()->where('primary_category_id', $sportsCategory->id)->with(['primaryCategory', 'coverMedia'])->orderByDesc('published_at')->take(5)->get()
            : collect();

        $bdCategory = Category::where('slug', 'bangladesh')->first();
        $bdNews = $bdCategory
            ? News::published()->where('primary_category_id', $bdCategory->id)->with(['primaryCategory', 'coverMedia'])->orderByDesc('published_at')->take(7)->get()
            : collect();

        $techCategory = Category::where('slug', 'technology')->first();
        $techNews = $techCategory
            ? News::published()->where('primary_category_id', $techCategory->id)->with(['primaryCategory', 'coverMedia'])->orderByDesc('published_at')->take(5)->get()
            : collect();

        $entCategory = Category::where('slug', 'entertainment')->first();
        $entNews = $entCategory
            ? News::published()->where('primary_category_id', $entCategory->id)->with(['primaryCategory', 'coverMedia'])->orderByDesc('published_at')->take(4)->get()
            : collect();

        $worldCategory = Category::where('slug', 'world')->first();
        $worldNews = $worldCategory
            ? News::published()->where('primary_category_id', $worldCategory->id)->with(['primaryCategory', 'coverMedia'])->orderByDesc('published_at')->take(4)->get()
            : collect();

        // 8. Locations, Navigation & Ads
        $divisions = Location::divisions()->with('children')->get();
        $navCategories = Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(12)->get();
        $adSlots = AdSlot::with('activeAdvertisements')->get()->keyBy('key');

        return compact(
            'sections',
            'leadStory',
            'leftStories',
            'centerSubStories',
            'rightStories',
            'leftTopStories',
            'leftMiddleStories',
            'leftTextStories',
            'featureLeadStory',
            'featureSubStories',
            'centerRow3Stories',
            'centerRow4Stories',
            'centerRow5Stories',
            'rightInDepthStories',
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
        );
    }
}
