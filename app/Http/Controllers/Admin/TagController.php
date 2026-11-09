<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount('news')->orderBy('sort_order')->paginate(20);
        return view('admin.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:tags,slug',
            'description' => 'nullable|string|max:255',
            'is_featured' => 'nullable',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order'] = $request->filled('sort_order')
            ? (int) $request->input('sort_order')
            : ((Tag::max('sort_order') ?? 0) + 1);

        if (empty($validated['slug'])) {
            $slug = Str::slug($validated['name']);
            if (empty($slug)) {
                $slug = 'tag-' . Str::lower(Str::random(6));
            }
            $originalSlug = $slug;
            $count = 1;
            while (Tag::where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }
            $validated['slug'] = $slug;
        }

        $tag = Tag::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'ট্যাগ সফলভাবে তৈরি হয়েছে।',
                'tag' => $tag,
            ]);
        }

        return redirect()->route('admin.tags.index')->with('success', 'ট্যাগ সফলভাবে তৈরি হয়েছে।');
    }

    public function quickStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $name = trim($request->input('name'));
        
        // Return existing tag if already created
        $existing = Tag::where('name', $name)->first();
        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'ট্যাগটি পূর্বেই সংরক্ষিত রয়েছে।',
                'tag' => $existing,
            ]);
        }

        $slug = Str::slug($name);
        if (empty($slug)) {
            $slug = 'tag-' . Str::lower(Str::random(6));
        }
        $originalSlug = $slug;
        $count = 1;
        while (Tag::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        $tag = Tag::create([
            'name' => $name,
            'slug' => $slug,
            'description' => null,
            'is_featured' => false,
            'sort_order' => (Tag::max('sort_order') ?? 0) + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ট্যাগ সফলভাবে তৈরি হয়েছে।',
            'tag' => $tag,
        ]);
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:tags,slug,' . $tag->id,
            'description' => 'nullable|string|max:255',
            'is_featured' => 'nullable',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order'] = $request->filled('sort_order')
            ? (int) $request->input('sort_order')
            : $tag->sort_order;

        if (empty($validated['slug'])) {
            $slug = Str::slug($validated['name']);
            if (empty($slug)) {
                $slug = 'tag-' . Str::lower(Str::random(6));
            }
            $originalSlug = $slug;
            $count = 1;
            while (Tag::where('slug', $slug)->where('id', '!=', $tag->id)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }
            $validated['slug'] = $slug;
        }

        $tag->update($validated);

        return redirect()->route('admin.tags.index')->with('success', 'ট্যাগ সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(Tag $tag)
    {
        $tag->news()->detach();
        $tag->delete();

        return redirect()->route('admin.tags.index')->with('success', 'ট্যাগ সফলভাবে মুছে ফেলা হয়েছে।');
    }
}
