<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer(['layouts.frontend', 'frontend.*'], function ($view) {
            $navCategories = \App\Models\Category::active()->whereNull('parent_id')->where('slug', '!=', 'latest')->orderBy('sort_order')->take(14)->get();
            $trendingTags = \App\Models\Tag::where('is_featured', true)->orderBy('sort_order')->take(8)->get();
            $topHeaderStories = \App\Models\News::published()->with(['primaryCategory', 'coverMedia'])->orderByDesc('published_at')->take(3)->get();
            $breakingNews = \App\Models\BreakingItem::active()->with('news')->first();
            $view->with([
                'navCategories' => $navCategories,
                'trendingTags' => $trendingTags,
                'topHeaderStories' => $topHeaderStories,
                'breakingNews' => $breakingNews,
            ]);
        });
    }
}
