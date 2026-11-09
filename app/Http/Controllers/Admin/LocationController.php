<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');
        $query = Location::with('parent')->withCount('news')->orderBy('name');

        if ($type) {
            $query->where('type', $type);
        }

        $locations = $query->paginate(25)->withQueryString();
        return view('admin.locations.index', compact('locations', 'type'));
    }

    public function create()
    {
        $divisions = Location::divisions()->orderBy('name')->get();
        return view('admin.locations.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:locations,slug',
            'type' => 'required|in:division,district,upazila',
            'parent_id' => 'nullable|exists:locations,id',
            'code' => 'nullable|string|max:20',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Location::create($validated);

        return redirect()->route('admin.locations.index')->with('success', 'লোকেশন সফলভাবে তৈরি হয়েছে।');
    }

    public function edit(Location $location)
    {
        $divisions = Location::divisions()->where('id', '!=', $location->id)->orderBy('name')->get();
        return view('admin.locations.edit', compact('location', 'divisions'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:locations,slug,' . $location->id,
            'type' => 'required|in:division,district,upazila',
            'parent_id' => 'nullable|exists:locations,id',
            'code' => 'nullable|string|max:20',
        ]);

        $location->update($validated);

        return redirect()->route('admin.locations.index')->with('success', 'লোকেশন সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(Location $location)
    {
        if ($location->news()->count() > 0) {
            return back()->withErrors(['error' => 'এই লোকেশনে সংবাদ যুক্ত রয়েছে। এটি মুছে ফেলা যাবে না।']);
        }

        $location->delete();
        return redirect()->route('admin.locations.index')->with('success', 'লোকেশন সফলভাবে মুছে ফেলা হয়েছে।');
    }
}
