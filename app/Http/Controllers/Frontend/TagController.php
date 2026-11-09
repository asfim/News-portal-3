<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function show(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $newsList = News::published()
            ->whereHas('tags', fn($q) => $q->where('tags.id', $tag->id))
            ->with(['primaryCategory', 'authors', 'coverMedia'])
            ->orderByDesc('published_at')
            ->paginate(12);

        $latestNews = News::published()->with('primaryCategory')->orderByDesc('published_at')->take(6)->get();
        $navCategories = Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(12)->get();

        return view('frontend.tag.show', compact('tag', 'newsList', 'latestNews', 'navCategories'));
    }
}
