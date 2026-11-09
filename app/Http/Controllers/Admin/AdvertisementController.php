<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\AdSlot;
use App\Models\Media;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    public function index()
    {
        $advertisements = Advertisement::with(['slots', 'media'])->latest('id')->paginate(15);
        $slots = AdSlot::all();
        $mediaList = Media::where('mime_type', 'like', 'image/%')->latest('id')->take(30)->get();

        return view('admin.advertisements.index', compact('advertisements', 'slots', 'mediaList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'destination_url' => 'required|url|max:255',
            'sponsor_name' => 'nullable|string|max:150',
            'media_id' => 'nullable|exists:media,id',
            'ad_slot_ids' => 'required|array',
            'ad_slot_ids.*' => 'exists:ad_slots,id',
            'status' => 'required|in:active,paused,expired',
            'priority' => 'nullable|integer',
        ]);

        $ad = Advertisement::create([
            'title' => $validated['title'],
            'destination_url' => $validated['destination_url'],
            'sponsor_name' => $validated['sponsor_name'] ?? null,
            'media_id' => $validated['media_id'] ?? null,
            'provider_type' => 'direct',
            'status' => $validated['status'],
            'priority' => $validated['priority'] ?? 5,
            'starts_at' => now(),
        ]);

        $ad->slots()->sync($validated['ad_slot_ids']);

        return back()->with('success', 'নতুন বিজ্ঞাপন সফলভাবে যুক্ত হয়েছে এবং নির্ধারিত স্লটে প্রদর্শিত হচ্ছে।');
    }

    public function toggle(Advertisement $advertisement)
    {
        $newStatus = $advertisement->status === 'active' ? 'paused' : 'active';
        $advertisement->update(['status' => $newStatus]);

        return back()->with('success', 'বিজ্ঞাপনের স্ট্যাটাস আপডেট করা হয়েছে।');
    }

    public function destroy(Advertisement $advertisement)
    {
        $advertisement->slots()->detach();
        $advertisement->delete();

        return back()->with('success', 'বিজ্ঞাপনটি মুছে ফেলা হয়েছে।');
    }
}

