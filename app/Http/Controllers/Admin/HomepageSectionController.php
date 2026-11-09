<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use Illuminate\Http\Request;

class HomepageSectionController extends Controller
{
    public function index()
    {
        $sections = HomepageSection::orderBy('sort_order')->get();

        return view('admin.homepage_sections.index', compact('sections'));
    }

    public function toggle(HomepageSection $section)
    {
        $section->update(['is_active' => !$section->is_active]);

        return back()->with('success', "'{$section->title}' সেকশনের স্ট্যাটাস আপডেট করা হয়েছে।");
    }

    public function update(Request $request, HomepageSection $section)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'item_limit' => 'required|integer|min:1|max:30',
            'sort_order' => 'required|integer|min:1|max:100',
        ]);

        $section->update($validated);

        return back()->with('success', "'{$section->title}' সেকশন সফলভাবে আপডেট করা হয়েছে।");
    }
}

