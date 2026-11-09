<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index()
    {
        $mediaList = Media::with('uploader')->orderByDesc('created_at')->paginate(24);
        return view('admin.media.index', compact('mediaList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,webp,avif|max:10240',
            'caption' => 'nullable|string|max:255',
            'credit' => 'nullable|string|max:150',
            'alt_text' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads/media', $filename, 'public');

        $media = Media::create([
            'uploaded_by' => auth()->id(),
            'disk' => 'public',
            'path' => asset('storage/' . $path),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size_bytes' => $file->getSize(),
            'alt_text' => $request->input('alt_text'),
            'caption' => $request->input('caption'),
            'credit' => $request->input('credit'),
            'status' => 'active',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'media' => $media]);
        }

        return back()->with('success', 'মিডিয়া ফাইল সফলভাবে আপলোড করা হয়েছে।');
    }

    public function destroy(Media $media)
    {
        if ($media->newsCover()->count() > 0) {
            return back()->withErrors(['error' => 'এই ছবিটি সংবাদের কভার হিসেবে ব্যবহৃত হচ্ছে। এটি মুছে ফেলা যাবে না।']);
        }

        $media->delete();
        return back()->with('success', 'মিডিয়া ফাইল মুছে ফেলা হয়েছে।');
    }
}
