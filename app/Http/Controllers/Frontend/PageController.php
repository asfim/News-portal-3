<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Category;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        $navCategories = Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(12)->get();

        return view('frontend.page.show', compact('page', 'navCategories'));
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:3000',
        ]);

        ContactMessage::create($validated);

        return back()->with('success', 'আপনার বার্তা সফলভাবে গৃহীত হয়েছে। ধন্যবাদ!');
    }
}
