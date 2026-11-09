<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->withCount('news')->orderBy('sort_order')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'sort_order' => 'required|integer',
            'is_active' => 'required|boolean',
            'seo_title' => 'nullable|string|max:200',
            'seo_description' => 'nullable|string|max:255',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'ক্যাটাগরি সফলভাবে তৈরি হয়েছে।');
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->get();
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'sort_order' => 'required|integer',
            'is_active' => 'required|boolean',
            'seo_title' => 'nullable|string|max:200',
            'seo_description' => 'nullable|string|max:255',
        ]);

        // Prevent cyclic parenting
        if ($validated['parent_id'] == $category->id) {
            return back()->withErrors(['parent_id' => 'ক্যাটাগরি নিজে নিজের প্যারেন্ট হতে পারবে না।']);
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'ক্যাটাগরি সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(Category $category)
    {
        if ($category->news()->count() > 0) {
            return back()->withErrors(['error' => 'এই ক্যাটাগরিতে সংবাদ যুক্ত থাকায় এটি মুছে ফেলা যাবে না। নিষ্ক্রিয় করতে পারেন।']);
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'ক্যাটাগরি সফলভাবে মুছে ফেলা হয়েছে।');
    }
}
