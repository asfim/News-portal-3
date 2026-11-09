<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->with('children')
            ->firstOrFail();

        // Include subcategories in the news feed
        $categoryIds = $category->children->pluck('id')->push($category->id);

        $newsList = News::published()
            ->whereIn('primary_category_id', $categoryIds)
            ->with(['primaryCategory', 'authors', 'coverMedia'])
            ->orderByDesc('published_at')
            ->paginate(12);

        $leadNews = $newsList->first();
        $otherNews = $newsList->slice(1);

        $latestNews = News::published()->with('primaryCategory')->orderByDesc('published_at')->take(6)->get();
        $navCategories = Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(12)->get();

        return view('frontend.category.show', compact('category', 'newsList', 'leadNews', 'otherNews', 'latestNews', 'navCategories'));
    }
}
