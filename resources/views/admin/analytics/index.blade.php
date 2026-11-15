@extends('layouts.admin')

@section('title', 'পাঠক অ্যানালিটিক্স ও সর্বাধিক পঠিত প্রতিবেদন')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">পাঠক অ্যানালিটিক্স ও পারফরম্যান্স রিপোর্ট</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">প্রকৃত পাঠসংখ্যা (SUM Views), ইউনিক ভিজিটর ও বিভাগওয়ারী পাঠকপ্রিয়তার পরিসংখ্যান</p>
    </div>
    <div style="display: flex; gap: 6px; background: #fff; padding: 4px; border-radius: 8px; border: 1px solid var(--border-color);">
        <a href="{{ route('admin.analytics.index', ['period' => '1']) }}" class="btn btn-sm {{ $period == '1' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 5px 12px; font-size: 12px;">বিগত ২৪ ঘণ্টা</a>
        <a href="{{ route('admin.analytics.index', ['period' => '7']) }}" class="btn btn-sm {{ $period == '7' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 5px 12px; font-size: 12px;">বিগত ৭ দিন</a>
        <a href="{{ route('admin.analytics.index', ['period' => '30']) }}" class="btn btn-sm {{ $period == '30' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 5px 12px; font-size: 12px;">বিগত ৩০ দিন</a>
        <a href="{{ route('admin.analytics.index', ['period' => 'all']) }}" class="btn btn-sm {{ $period == 'all' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 5px 12px; font-size: 12px;">সর্বকালের</a>
    </div>
</div>

<!-- Metric Overview -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-label">মোট পাঠসংখ্যা (Total Views)</span>
            <span class="stat-icon">👁️</span>
        </div>
        <div class="stat-value">{{ bn_num($totalViews) }}</div>
        <div class="stat-caption">নির্বাচিত সময়ের মোট পৃষ্ঠা দর্শন</div>
    </div>

    <div class="stat-card stat-green">
        <div class="stat-card-top">
            <span class="stat-label">ইউনিক পাঠক (Unique Views)</span>
            <span class="stat-icon">👤</span>
        </div>
        <div class="stat-value">{{ bn_num($totalUniqueViews) }}</div>
        <div class="stat-caption">পৃথক সেশন থেকে পাঠসংখ্যা</div>
    </div>

    <div class="stat-card stat-purple">
        <div class="stat-card-top">
            <span class="stat-label">শীর্ষ পাঠকপ্রিয় বিভাগ</span>
            <span class="stat-icon">🏆</span>
        </div>
        <div class="stat-value" style="font-size: 20px; font-weight: 700;">
            {{ $categoryStatsQuery->first()->name ?? 'কোনোটি নয়' }}
        </div>
        <div class="stat-caption">{{ bn_num($categoryStatsQuery->first()->category_views ?? 0) }} বার পঠিত</div>
    </div>

    <div class="stat-card stat-blue">
        <div class="stat-card-top">
            <span class="stat-label">বট ও ডুপ্লিকেট ফিল্টারিং</span>
            <span class="stat-icon">🛡️</span>
        </div>
        <div class="stat-value" style="font-size: 20px; color: #0284c7;">সক্রিয়</div>
        <div class="stat-caption">স্বয়ংক্রিয় সেশন ও ক্রলার ডিটেকশন</div>
    </div>
</div>

<!-- Daily Traffic Trend Chart -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">দৈনিক পাঠসংখ্যা ট্রেন্ড (Traffic Trend)</h3>
        <span style="font-size: 12px; color: var(--text-muted);">তারিখ অনুযায়ী ভিউ ও ইউনিক ভিউর তুলনামূলক গ্রাফ</span>
    </div>
    <div class="card-body">
        @if($dailyTrend->isNotEmpty())
            <div style="display: flex; align-items: flex-end; gap: 12px; height: 180px; padding: 20px 10px 0; border-bottom: 2px solid #e2e8f0; overflow-x: auto;">
                @foreach($dailyTrend as $stat)
                    @php
                        $heightPercent = max(round(($stat->total_views / $maxViews) * 100), 5);
                    @endphp
                    <div style="flex: 1; min-width: 45px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%;">
                        <div style="font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 4px;">{{ bn_num($stat->total_views) }}</div>
                        <div style="width: 24px; height: {{ $heightPercent }}%; background: linear-gradient(180deg, #c62828 0%, #ef4444 100%); border-radius: 4px 4px 0 0;" title="{{ $stat->stat_date }}: {{ $stat->total_views }} ভিউ"></div>
                        <div style="font-size: 10px; color: #64748b; margin-top: 6px; white-space: nowrap;">{{ date('d M', strtotime($stat->stat_date)) }}</div>
                    </div>
                @endforeach
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 16px; margin-top: 12px; font-size: 12px; color: #64748b;">
                <span style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 12px; height: 12px; background: #c62828; border-radius: 2px;"></span> মোট ভিউ
                </span>
            </div>
        @else
            <div style="text-align: center; padding: 40px; color: #94a3b8;">
                কোনো দৈনিক পাঠসংখ্যার পরিসংখ্যান পাওয়া যায়নি।
            </div>
        @endif
    </div>
</div>

<div class="admin-grid-2col" style="grid-template-columns: 1.4fr 1fr;">
    <!-- Top Read News Table -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">সর্বাধিক পঠিত সংবাদ তালিকা</h3>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">প্রকৃত SUM(views) ভিত্তিতে সেরা প্রতিবেদন</p>
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">ক্রম</th>
                            <th>শিরোনাম</th>
                            <th>ক্যাটাগরি</th>
                            <th style="text-align: right;">মোট ভিউ</th>
                            <th style="text-align: right;">ইউনিক ভিউ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topArticles as $idx => $art)
                            <tr>
                                <td>
                                    <strong style="color: var(--primary); font-size: 15px;">{{ bn_num($idx + 1) }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('news.show', [$art->public_id, $art->slug]) }}" target="_blank" style="color: inherit; text-decoration: none; font-weight: 600;" onmouseover="this.style.color='#c62828'" onmouseout="this.style.color='inherit'">
                                        {{ $art->title }}
                                    </a>
                                    <div style="font-size: 11px; color: #94a3b8; margin-top: 3px;">
                                        {{ bn_date($art->published_at) }} · {{ $art->authors->first()?->name ?? 'ডেস্ক' }}
                                    </div>
                                </td>
                                <td>
                                    <span style="background: #f1f5f9; padding: 2px 7px; border-radius: 4px; font-size: 12px;">
                                        {{ $art->primaryCategory?->name ?? 'বিভাগহীন' }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <span style="font-weight: 700; color: #0f766e; background: #ccfbf1; padding: 2px 8px; border-radius: 9999px; font-size: 12px;">
                                        👁 {{ bn_num($art->total_views) }}
                                    </span>
                                </td>
                                <td style="text-align: right; font-size: 12px; color: #64748b;">
                                    {{ bn_num($art->total_unique_views) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8;">
                                    নির্বাচিত সময়ে কোনো পঠিত সংবাদ নেই।
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Category Performance -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">ক্যাটাগরি অনুযায়ী পারফরম্যান্স</h3>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">কোন বিভাগে পাঠক বেশি আগ্রহী</p>
            </div>
        </div>
        <div class="card-body">
            @php $totalCatViews = $categoryStatsQuery->sum('category_views') ?: 1; @endphp
            <div style="display: flex; flex-direction: column; gap: 16px;">
                @foreach($categoryStatsQuery as $catStat)
                    @php
                        $percent = round(($catStat->category_views / $totalCatViews) * 100, 1);
                    @endphp
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; font-size: 13px;">
                            <span style="font-weight: 600;">{{ $catStat->name }}</span>
                            <span style="color: #64748b;">
                                <strong>{{ bn_num($catStat->category_views) }}</strong> ভিউ 
                                <span style="font-size: 11px; color: #94a3b8;">({{ bn_num($percent) }}%)</span>
                            </span>
                        </div>
                        <div style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                            <div style="width: {{ $percent }}%; height: 100%; background: linear-gradient(90deg, #c62828 0%, #f97316 100%); border-radius: 4px;"></div>
                        </div>
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 3px;">
                            মোট সংবাদ: {{ bn_num($catStat->article_count) }}টি
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

