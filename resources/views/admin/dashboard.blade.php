@extends('layouts.admin')

@section('title', 'ড্যাশবোর্ড')

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }
    .stat-card {
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 20px;
        display: flex;
        flex-direction: column;
    }
    .stat-label {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 8px;
    }
    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-main);
    }
    .stat-accent {
        height: 4px;
        width: 40px;
        background: var(--primary);
        border-radius: 2px;
        margin-top: 12px;
    }
</style>
@endpush

@section('content')
<div>
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">প্রকাশিত সংবাদ</span>
            <span class="stat-value">{{ bn_num($publishedCount) }}</span>
            <div class="stat-accent" style="background: #059669;"></div>
        </div>

        <div class="stat-card">
            <span class="stat-label">পর্যালোচনার অপেক্ষায়</span>
            <span class="stat-value" style="color: #d97706;">{{ bn_num($reviewPendingCount) }}</span>
            <div class="stat-accent" style="background: #d97706;"></div>
        </div>

        <div class="stat-card">
            <span class="stat-label">নির্ধারিত (শিডিউল্ড)</span>
            <span class="stat-value" style="color: #4f46e5;">{{ bn_num($scheduledCount) }}</span>
            <div class="stat-accent" style="background: #4f46e5;"></div>
        </div>

        <div class="stat-card">
            <span class="stat-label">খসড়া (ড্রাফট)</span>
            <span class="stat-value">{{ bn_num($draftCount) }}</span>
            <div class="stat-accent" style="background: #6b7280;"></div>
        </div>

        <div class="stat-card">
            <span class="stat-label">আজকের মোট পঠিত</span>
            <span class="stat-value" style="color: var(--primary);">{{ bn_num($todayViews) }}</span>
            <div class="stat-accent" style="background: var(--primary);"></div>
        </div>

        <div class="stat-card">
            <span class="stat-label">অপেক্ষমাণ মন্তব্য</span>
            <span class="stat-value" style="color: #dc2626;">{{ bn_num($pendingCommentsCount) }}</span>
            <div class="stat-accent" style="background: #dc2626;"></div>
        </div>
    </div>

    <!-- Quick Action Bar -->
    <div style="display: flex; gap: 12px; margin-bottom: 24px;">
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
            <span>+ নতুন সংবাদ লিখুন</span>
        </a>
        <a href="{{ route('admin.media.index') }}" class="btn btn-secondary">
            <span>ছবি আপলোড</span>
        </a>
        <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary">
            <span>মন্তব্য যাচাই ({{ bn_num($pendingCommentsCount) }})</span>
        </a>
    </div>

    <!-- Recent News Table -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">সাম্প্রতিক সংবাদ তালিকা</h2>
            <a href="{{ route('admin.news.index') }}" class="btn btn-secondary btn-sm">সব দেখুন</a>
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
                                    <strong style="color: #111;">{{ $news->title }}</strong>
                                    @if($news->kicker)
                                        <div style="font-size: 11px; color: var(--primary);">{{ $news->kicker }}</div>
                                    @endif
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
                                <td style="text-align: right;">
                                    <a href="{{ route('admin.news.edit', $news->id) }}" class="btn btn-secondary btn-sm">সম্পাদনা</a>
                                    @if($news->status === 'published')
                                        <a href="{{ route('news.show', [$news->public_id, $news->slug]) }}" target="_blank" class="btn btn-sm" style="color: var(--primary);">দেখুন</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">কোনো সংবাদ পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

