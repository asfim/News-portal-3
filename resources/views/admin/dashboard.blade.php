@extends('layouts.admin')

@section('title', 'ড্যাশবোর্ড')

@push('styles')
<style>
    .dashboard-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }
    .dashboard-eyebrow {
        color: var(--primary);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .08em;
        margin-bottom: 5px;
    }
    .dashboard-title {
        color: var(--text-main);
        font-size: clamp(24px, 3vw, 32px);
        line-height: 1.25;
        margin-bottom: 6px;
    }
    .dashboard-subtitle {
        color: var(--text-muted);
        font-size: 14px;
    }
    .dashboard-date {
        color: #596276;
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13px;
        white-space: nowrap;
    }
    .dashboard-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        padding: 26px 30px;
        margin-bottom: 22px;
        border-radius: 16px;
        color: #fff;
        background: linear-gradient(115deg, #9f1d27 0%, #c62828 53%, #df5545 100%);
        box-shadow: 0 12px 28px rgba(170, 35, 44, .16);
    }
    .dashboard-hero::after {
        content: "";
        position: absolute;
        width: 230px;
        height: 230px;
        right: 18%;
        top: -150px;
        border: 1px solid rgba(255,255,255,.16);
        border-radius: 50%;
        box-shadow: 0 0 0 26px rgba(255,255,255,.04), 0 0 0 54px rgba(255,255,255,.035);
        pointer-events: none;
    }
    .dashboard-hero-copy,
    .dashboard-hero-actions {
        position: relative;
        z-index: 1;
    }
    .dashboard-hero h2 {
        font-size: 20px;
        margin-bottom: 5px;
    }
    .dashboard-hero p {
        color: rgba(255,255,255,.82);
        font-size: 13px;
    }
    .dashboard-hero .btn {
        background: #fff;
        color: #a6222a;
        white-space: nowrap;
        box-shadow: 0 4px 12px rgba(77, 12, 19, .12);
    }
    .dashboard-hero .btn:hover {
        background: #fff5f4;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 26px;
    }
    .stat-card {
        --stat-color: #c62828;
        --stat-tint: #fff0ef;
        position: relative;
        min-width: 0;
        min-height: 142px;
        padding: 19px 20px;
        border: 1px solid var(--border-color);
        border-radius: 13px;
        background: #fff;
        box-shadow: 0 4px 15px rgba(27, 34, 50, .035);
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 9px 22px rgba(27, 34, 50, .08);
    }
    .stat-card::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 3px;
        border-radius: 13px 0 0 13px;
        background: var(--stat-color);
    }
    .stat-card.stat-green { --stat-color: #15845b; --stat-tint: #eaf7f0; }
    .stat-card.stat-amber { --stat-color: #bd7410; --stat-tint: #fff5e6; }
    .stat-card.stat-blue { --stat-color: #4b63bd; --stat-tint: #eef1ff; }
    .stat-card.stat-slate { --stat-color: #667085; --stat-tint: #f0f2f5; }
    .stat-card.stat-purple { --stat-color: #8052ad; --stat-tint: #f5effb; }
    .stat-card.stat-rose { --stat-color: #c34669; --stat-tint: #fff0f4; }
    .stat-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 15px;
    }
    .stat-label {
        color: #687186;
        font-size: 13px;
        font-weight: 600;
    }
    .stat-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        color: var(--stat-color);
        background: var(--stat-tint);
        font-size: 17px;
        font-weight: 800;
    }
    .stat-value {
        color: var(--text-main);
        font-size: 29px;
        font-weight: 800;
        letter-spacing: -.03em;
        line-height: 1.1;
    }
    .stat-caption {
        margin-top: 7px;
        color: #9098a8;
        font-size: 11px;
    }
    .recent-news-card .card-header {
        min-height: 70px;
    }
    .recent-news-title {
        font-size: 16px;
        font-weight: 750;
        color: var(--text-main);
    }
    .recent-news-subtitle {
        margin-top: 3px;
        color: var(--text-muted);
        font-size: 12px;
    }
    .news-title-cell {
        color: #252c3d;
        font-weight: 650;
        line-height: 1.55;
    }
    .news-title-cell small {
        display: block;
        margin-top: 3px;
        color: var(--primary);
        font-size: 11px;
        font-weight: 600;
    }
    .empty-news {
        padding: 38px 20px !important;
        color: var(--text-muted);
        text-align: center;
    }
    @media (max-width: 1150px) {
        .stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 700px) {
        .dashboard-heading { align-items: flex-start; flex-direction: column; }
        .dashboard-date { display: none; }
        .dashboard-hero { align-items: flex-start; flex-direction: column; padding: 23px; }
        .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 11px; }
        .stat-card { min-height: 130px; padding: 16px 14px; }
        .stat-value { font-size: 25px; }
    }
    @media (max-width: 380px) {
        .stats-grid { grid-template-columns: 1fr; }
        .stat-card { min-height: 112px; }
    }
</style>
@endpush

@section('content')
<section class="dashboard-heading">
    <div>
        <div class="dashboard-eyebrow">সম্পাদকীয় নিয়ন্ত্রণকেন্দ্র</div>
        <h1 class="dashboard-title">ড্যাশবোর্ড</h1>
        <p class="dashboard-subtitle">আপনার নিউজ পোর্টালের আজকের সার্বিক চিত্র এক নজরে।</p>
    </div>
    <div class="dashboard-date">{{ bn_date() }} <span aria-hidden="true">·</span> ঢাকা</div>
</section>

<section class="dashboard-hero" aria-label="দ্রুত সংবাদ তৈরি">
    <div class="dashboard-hero-copy">
        <h2>নতুন সংবাদ প্রকাশের জন্য প্রস্তুত?</h2>
        <p>আপনার পাঠকদের জন্য গুরুত্বপূর্ণ খবরটি তৈরি ও প্রকাশ করুন।</p>
    </div>
    <div class="dashboard-hero-actions">
        <a href="{{ route('admin.news.create') }}" class="btn">＋ নতুন সংবাদ লিখুন</a>
    </div>
</section>

<section class="stats-grid" aria-label="পোর্টালের সারসংক্ষেপ">
    <article class="stat-card stat-green">
        <div class="stat-card-top"><span class="stat-label">প্রকাশিত সংবাদ</span><span class="stat-icon" aria-hidden="true">✓</span></div>
        <div class="stat-value">{{ bn_num($publishedCount) }}</div>
        <div class="stat-caption">পাঠকদের জন্য লাইভ</div>
    </article>
    <article class="stat-card stat-amber">
        <div class="stat-card-top"><span class="stat-label">পর্যালোচনায়</span><span class="stat-icon" aria-hidden="true">…</span></div>
        <div class="stat-value">{{ bn_num($reviewPendingCount) }}</div>
        <div class="stat-caption">সম্পাদকের অনুমোদন অপেক্ষায়</div>
    </article>
    <article class="stat-card stat-blue">
        <div class="stat-card-top"><span class="stat-label">নির্ধারিত সংবাদ</span><span class="stat-icon" aria-hidden="true">◷</span></div>
        <div class="stat-value">{{ bn_num($scheduledCount) }}</div>
        <div class="stat-caption">পরবর্তীতে প্রকাশিত হবে</div>
    </article>
    <article class="stat-card stat-slate">
        <div class="stat-card-top"><span class="stat-label">খসড়া</span><span class="stat-icon" aria-hidden="true">✎</span></div>
        <div class="stat-value">{{ bn_num($draftCount) }}</div>
        <div class="stat-caption">এখনো প্রকাশ করা হয়নি</div>
    </article>
    <article class="stat-card">
        <div class="stat-card-top"><span class="stat-label">আজকের পঠিত</span><span class="stat-icon" aria-hidden="true">↗</span></div>
        <div class="stat-value">{{ bn_num($todayViews) }}</div>
        <div class="stat-caption">আজকের মোট পাঠসংখ্যা</div>
    </article>
    <article class="stat-card stat-rose">
        <div class="stat-card-top"><span class="stat-label">অপেক্ষমাণ মন্তব্য</span><span class="stat-icon" aria-hidden="true">☰</span></div>
        <div class="stat-value">{{ bn_num($pendingCommentsCount) }}</div>
        <div class="stat-caption">মডারেশন প্রয়োজন</div>
    </article>
    <article class="stat-card stat-purple">
        <div class="stat-card-top"><span class="stat-label">ক্যাটাগরি</span><span class="stat-icon" aria-hidden="true">▦</span></div>
        <div class="stat-value">{{ bn_num($categoryCount) }}</div>
        <div class="stat-caption">সংবাদ বিভাগ</div>
    </article>
    <article class="stat-card stat-blue">
        <div class="stat-card-top"><span class="stat-label">লেখক ও রিপোর্টার</span><span class="stat-icon" aria-hidden="true">♙</span></div>
        <div class="stat-value">{{ bn_num($authorCount) }}</div>
        <div class="stat-caption">কনটেন্ট টিম</div>
    </article>
</section>

<section class="card recent-news-card">
    <div class="card-header">
        <div>
            <h2 class="recent-news-title">সাম্প্রতিক সংবাদ</h2>
            <p class="recent-news-subtitle">সম্প্রতি তৈরি বা সম্পাদিত সংবাদগুলোর অবস্থা</p>
        </div>
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary btn-sm">সব সংবাদ দেখুন →</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>শিরোনাম</th>
                        <th>ক্যাটাগরি</th>
                        <th>লেখক</th>
                        <th>অবস্থা</th>
                        <th>প্রকাশের তারিখ</th>
                        <th style="text-align: right;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentNews as $news)
                        <tr>
                            <td>
                                <div class="news-title-cell">{{ $news->title }}
                                    @if($news->kicker)
                                        <small>{{ $news->kicker }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $news->primaryCategory ? $news->primaryCategory->name : '-' }}</td>
                            <td>{{ $news->authors->first()?->name ?? 'ডেস্ক' }}</td>
                            <td>
                                <span class="status-badge status-{{ $news->status }}">
                                    @if($news->status === 'published') প্রকাশিত
                                    @elseif($news->status === 'draft') খসড়া
                                    @elseif($news->status === 'review_pending') পর্যালোচনায়
                                    @elseif($news->status === 'scheduled') নির্ধারিত
                                    @else আর্কাইভ @endif
                                </span>
                            </td>
                            <td>{{ bn_date($news->published_at ?? $news->created_at) }}</td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('admin.news.edit', $news->id) }}" class="btn btn-secondary btn-sm">সম্পাদনা</a>
                                @if($news->status === 'published')
                                    <a href="{{ route('news.show', [$news->public_id, $news->slug]) }}" target="_blank" rel="noopener" class="btn btn-sm" style="color: var(--primary);">দেখুন</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-news">এখনো কোনো সংবাদ তৈরি হয়নি। নতুন সংবাদ লিখে শুরু করুন।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="card" style="margin-top: 24px;">
    <div class="card-header">
        <div>
            <h2 class="recent-news-title">সর্বাধিক পঠিত সংবাদ (বিগত ৭ দিন)</h2>
            <p class="recent-news-subtitle">পাঠকদের সবচেয়ে বেশি আগ্রহ থাকা সংবাদগুলোর তালিকা ও মোট পাঠসংখ্যা</p>
        </div>
        <span style="font-size: 13px; color: var(--text-muted); font-weight: 600;">সাপ্তাহিক মোট ভিউ: {{ bn_num($weekViews) }}</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">ক্রম</th>
                        <th>শিরোনাম</th>
                        <th>ক্যাটাগরি</th>
                        <th>মোট পাঠসংখ্যা (ভিউ)</th>
                        <th>প্রকাশের সময়</th>
                        <th style="text-align: right;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topNews7Days as $idx => $tNews)
                        <tr>
                            <td><strong style="color: var(--primary); font-size: 16px;">{{ bn_num($idx + 1) }}</strong></td>
                            <td>
                                <div class="news-title-cell">{{ $tNews->title }}</div>
                            </td>
                            <td>{{ $tNews->primaryCategory?->name ?? '-' }}</td>
                            <td>
                                <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700; color: #0f766e; background: #ccfbf1; padding: 3px 9px; border-radius: 9999px; font-size: 13px;">
                                    👁 {{ bn_num($tNews->total_views) }}
                                </span>
                            </td>
                            <td>{{ bn_date($tNews->published_at) }}</td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('news.show', [$tNews->public_id, $tNews->slug]) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">দেখুন</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-news">বিগত ৭ দিনে কোনো পঠিত সংবাদের তথ্য পাওয়া যায়নি।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
