<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->input('q', ''));
        $categoryId = $request->input('category');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $newsQuery = News::published()
            ->with(['primaryCategory', 'authors', 'coverMedia']);

        if (!empty($query)) {
            $newsQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%")
                  ->orWhere('body_plain', 'like', "%{$query}%");
            });
        }

        if (!empty($categoryId)) {
            $newsQuery->where('primary_category_id', $categoryId);
        }

        if (!empty($dateFrom)) {
            $newsQuery->whereDate('published_at', '>=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $newsQuery->whereDate('published_at', '<=', $dateTo);
        }

        $newsList = !empty($query) || !empty($categoryId) || !empty($dateFrom) || !empty($dateTo)
            ? $newsQuery->orderByDesc('published_at')->paginate(12)->withQueryString()
            : collect();

        $categories = Category::active()->whereNull('parent_id')->orderBy('sort_order')->get();
        $navCategories = Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(12)->get();
        $latestNews = News::published()->with('primaryCategory')->orderByDesc('published_at')->take(6)->get();

        return view('frontend.search', compact('newsList', 'query', 'categoryId', 'dateFrom', 'dateTo', 'categories', 'navCategories', 'latestNews'));
    }
}
