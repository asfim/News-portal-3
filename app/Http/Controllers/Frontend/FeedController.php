<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use App\Models\Page;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    /**
     * Generate RSS 2.0 Feed for published news.
     */
    public function rss(): Response
    {
        $articles = News::published()
            ->with(['primaryCategory', 'authors'])
            ->orderByDesc('published_at')
            ->take(30)
            ->get();

        $content = view('frontend.feeds.rss', [
            'articles' => $articles,
            'channelTitle' => 'এইচ টেক প্রোভিশন আইটি (H Tech Provision IT)',
            'channelDescription' => 'সর্বশেষ জাতীয়, আন্তর্জাতিক, অর্থনীতি, খেলা ও প্রযুক্তির নির্ভুল সংবাদ',
            'channelLink' => url('/'),
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/rss+xml; charset=utf-8',
        ]);
    }

    /**
     * Generate standard XML Sitemap.
     */
    public function sitemap(): Response
    {
        $articles = News::published()
            ->select(['id', 'public_id', 'slug', 'published_at', 'content_updated_at', 'updated_at'])
            ->orderByDesc('published_at')
            ->take(500)
            ->get();

        $categories = Category::active()->get();
        $pages = Page::where('status', 'published')->get();

        $content = view('frontend.feeds.sitemap', compact('articles', 'categories', 'pages'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}

