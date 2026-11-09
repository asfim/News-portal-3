<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Bookmark;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $bookmarks = Bookmark::where('user_id', $user->id)
            ->with(['news.primaryCategory', 'news.coverMedia', 'news.authors'])
            ->latest('id')
            ->paginate(12);

        $navCategories = Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(12)->get();

        return view('frontend.bookmarks.index', compact('bookmarks', 'navCategories'));
    }

    public function toggle(string $public_id)
    {
        $news = News::published()->where('public_id', $public_id)->firstOrFail();
        $user = Auth::user();

        $bookmark = Bookmark::where('user_id', $user->id)->where('news_id', $news->id)->first();

        if ($bookmark) {
            $bookmark->delete();
            $bookmarked = false;
            $message = 'সংবাদটি বুকমার্ক থেকে সরিয়ে নেওয়া হয়েছে।';
        } else {
            Bookmark::create([
                'user_id' => $user->id,
                'news_id' => $news->id,
            ]);
            $bookmarked = true;
            $message = 'সংবাদটি বুকমার্কে সংরক্ষিত হয়েছে।';
        }

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'bookmarked' => $bookmarked,
                'message' => $message,
            ]);
        }

        return back()->with('bookmark_status', $message);
    }
}

