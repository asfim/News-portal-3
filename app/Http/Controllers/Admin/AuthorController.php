<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthorProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = AuthorProfile::with('user')->withCount('news')->orderBy('name')->paginate(20);
        return view('admin.authors.index', compact('authors'));
    }

    public function create()
    {
        $users = User::where('status', 'active')->orderBy('name')->get();
        return view('admin.authors.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:author_profiles,slug',
            'user_id' => 'nullable|exists:users,id|unique:author_profiles,user_id',
            'designation' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        AuthorProfile::create($validated);

        return redirect()->route('admin.authors.index')->with('success', 'লেখক প্রোফাইল সফলভাবে তৈরি হয়েছে।');
    }

    public function edit(AuthorProfile $author)
    {
        $users = User::where('status', 'active')->orderBy('name')->get();
        return view('admin.authors.edit', compact('author', 'users'));
    }

    public function update(Request $request, AuthorProfile $author)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:author_profiles,slug,' . $author->id,
            'user_id' => 'nullable|exists:users,id|unique:author_profiles,user_id,' . $author->id,
            'designation' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
        ]);

        $author->update($validated);

        return redirect()->route('admin.authors.index')->with('success', 'লেখক প্রোফাইল সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(AuthorProfile $author)
    {
        if ($author->news()->count() > 0) {
            return back()->withErrors(['error' => 'এই লেখকের সংবাদ রয়েছে। প্রোফাইল নিষ্ক্রিয় করতে পারেন।']);
        }

        $author->delete();
        return redirect()->route('admin.authors.index')->with('success', 'লেখক প্রোফাইল সফলভাবে মুছে ফেলা হয়েছে।');
    }
}
