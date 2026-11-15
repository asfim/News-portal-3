<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use App\Models\NewsDailyStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', '7'); // 1, 7, 30, all
        $days = is_numeric($period) ? (int)$period : 7;
        if ($period === 'all') {
            $days = null;
        }

        // 1. Summary Totals
        $statsQuery = NewsDailyStat::query();
        if ($days !== null) {
            $startDate = now()->subDays($days)->toDateString();
            $statsQuery->where('stat_date', '>=', $startDate);
        }

        $totalViews = (int) $statsQuery->sum('views');
        $totalUniqueViews = (int) $statsQuery->sum('unique_views');

        // 2. Daily Trend for Charts (Last 14 days or chosen period)
        $chartDays = $days && $days <= 30 ? $days : 14;
        $chartStartDate = now()->subDays($chartDays)->toDateString();

        $dailyTrend = NewsDailyStat::selectRaw('stat_date, SUM(views) as total_views, SUM(unique_views) as total_unique')
            ->where('stat_date', '>=', $chartStartDate)
            ->groupBy('stat_date')
            ->orderBy('stat_date')
            ->get();

        // 3. Top Read Articles in Period
        $topArticles = News::published()
            ->with(['primaryCategory', 'authors'])
            ->withViewStats($days)
            ->orderByDesc('total_views')
            ->take(15)
            ->get();

        // 4. Category-Wise Performance
        $categoryStatsQuery = DB::table('categories')
            ->leftJoin('news', 'categories.id', '=', 'news.primary_category_id')
            ->leftJoin('news_daily_stats', function ($join) use ($days) {
                $join->on('news.id', '=', 'news_daily_stats.news_id');
                if ($days !== null) {
                    $join->where('news_daily_stats.stat_date', '>=', now()->subDays($days)->toDateString());
                }
            })
            ->select(
                'categories.id',
                'categories.name',
                'categories.slug',
                DB::raw('COUNT(DISTINCT news.id) as article_count'),
                DB::raw('COALESCE(SUM(news_daily_stats.views), 0) as category_views'),
                DB::raw('COALESCE(SUM(news_daily_stats.unique_views), 0) as category_unique_views')
            )
            ->whereNull('categories.parent_id')
            ->groupBy('categories.id', 'categories.name', 'categories.slug')
            ->orderByDesc('category_views')
            ->get();

        $maxViews = $dailyTrend->max('total_views') ?: 1;

        return view('admin.analytics.index', compact(
            'period',
            'days',
            'totalViews',
            'totalUniqueViews',
            'dailyTrend',
            'topArticles',
            'categoryStatsQuery',
            'maxViews'
        ));
    }
}

