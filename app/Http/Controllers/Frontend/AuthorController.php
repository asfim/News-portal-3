<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AuthorProfile;
use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function show(string $slug)
    {
        $author = AuthorProfile::where('slug', $slug)->firstOrFail();

        $newsList = News::published()
            ->whereHas('authors', fn($q) => $q->where('author_profiles.id', $author->id))
            ->with(['primaryCategory', 'authors', 'coverMedia'])
            ->orderByDesc('published_at')
            ->paginate(12);

        $latestNews = News::published()->with('primaryCategory')->orderByDesc('published_at')->take(6)->get();
        $navCategories = Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(12)->get();

        return view('frontend.author.show', compact('author', 'newsList', 'latestNews', 'navCategories'));
    }
}
