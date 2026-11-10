<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use App\Models\Comment;
use App\Models\AuthorProfile;
use App\Models\NewsDailyStat;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $publishedCount = News::where('status', 'published')->count();
        $draftCount = News::where('status', 'draft')->count();
        $reviewPendingCount = News::where('status', 'review_pending')->count();
        $scheduledCount = News::where('status', 'scheduled')->count();
        $pendingCommentsCount = Comment::where('status', 'pending')->count();
        $categoryCount = Category::count();
        $authorCount = AuthorProfile::count();

        // Recent news
        $recentNews = News::with(['primaryCategory', 'authors'])
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        // Top Read News (Last 7 Days)
        $topNews7Days = News::published()
            ->with(['primaryCategory'])
            ->mostRead(7)
            ->take(5)
            ->get();

        // Total views today & last 7 days
        $todayViews = NewsDailyStat::where('stat_date', now()->toDateString())->sum('views');
        $weekViews = NewsDailyStat::where('stat_date', '>=', now()->subDays(7)->toDateString())->sum('views');

        return view('admin.dashboard', compact(
            'publishedCount',
            'draftCount',
            'reviewPendingCount',
            'scheduledCount',
            'pendingCommentsCount',
            'categoryCount',
            'authorCount',
            'recentNews',
            'topNews7Days',
            'todayViews',
            'weekViews'
        ));
    }
}
