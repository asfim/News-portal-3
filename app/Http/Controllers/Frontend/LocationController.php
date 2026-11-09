<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function show(string $slug)
    {
        $location = Location::where('slug', $slug)->with('children')->firstOrFail();

        $locationIds = $location->children->pluck('id')->push($location->id);

        $newsList = News::published()
            ->whereIn('location_id', $locationIds)
            ->with(['primaryCategory', 'authors', 'coverMedia'])
            ->orderByDesc('published_at')
            ->paginate(12);

        $latestNews = News::published()->with('primaryCategory')->orderByDesc('published_at')->take(6)->get();
        $navCategories = Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(12)->get();
        $divisions = Location::divisions()->with('children')->get();

        return view('frontend.location.show', compact('location', 'newsList', 'latestNews', 'navCategories', 'divisions'));
    }

    public function getDistricts(Request $request)
    {
        $divisionId = $request->query('division_id');
        $districts = Location::where('parent_id', $divisionId)->orderBy('name')->get();
        return response()->json($districts);
    }
}
