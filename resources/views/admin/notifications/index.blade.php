@extends('layouts.admin')

@section('title', 'ওয়েব পুশ নোটিফিকেশন ব্যবস্থাপনা')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">ওয়েব পুশ নোটিফিকেশন নিয়ন্ত্রণ</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">ব্রাউজার পুশ নোটিফিকেশন ও ব্রেকিং নিউজ সরাসরি পাঠকদের ডিভাইসে পাঠান</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.notifications.subscribers') }}" class="btn btn-secondary btn-sm">গ্রাহক তালিকা ({{ bn_num($activeSubscribersCount) }}) →</a>
        <a href="{{ route('admin.notifications.logs') }}" class="btn btn-secondary btn-sm">ডেলিভারি হিস্টোরি</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 24px;">
    <div class="stat-card stat-green">
        <div class="stat-card-top">
            <span class="stat-label">সক্রিয় পুশ গ্রাহক</span>
            <span class="stat-icon">🔔</span>
        </div>
        <div class="stat-value">{{ bn_num($activeSubscribersCount) }}</div>
        <div class="stat-caption">নোটিফিকেশনে সম্মতি দেওয়া পাঠক</div>
    </div>

    <div class="stat-card stat-blue">
        <div class="stat-card-top">
            <span class="stat-label">মোট সফল ডেলিভারি</span>
            <span class="stat-icon">📨</span>
        </div>
        <div class="stat-value">{{ bn_num($totalNotificationsSent) }}</div>
        <div class="stat-caption">পাঠকদের ডিভাইসে পৌঁছেছে</div>
    </div>

    <div class="stat-card stat-rose">
        <div class="stat-card-top">
            <span class="stat-label">ব্রেকিং নিউজ অটো-পুশ</span>
            <span class="stat-icon">⚡</span>
        </div>
        <div class="stat-value" style="font-size: 18px; color: #e11d48;">স্বয়ংক্রিয় সক্রিয়</div>
        <div class="stat-caption">ব্রেকিং নিউজ প্রকাশের সাথে সাথে ডেলিভারি</div>
    </div>
</div>

<div class="admin-grid-2col" style="grid-template-columns: 1.2fr 1fr; gap: 24px;">
    <!-- Send Notification Form -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">নতুন পুশ নোটিফিকেশন পাঠান</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.notifications.send') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">সংবাদ থেকে স্বয়ংক্রিয় পূরণ (ঐচ্ছিক)</label>
                    <select id="newsSelector" class="form-control" style="font-size: 13px;" onchange="autoFillFromNews(this)">
                        <option value="">-- সাম্প্রতিক প্রকাশিত সংবাদ নির্বাচন করুন --</option>
                        @foreach($recentNews as $rn)
                            <option value="{{ $rn->id }}" data-title="{{ $rn->title }}" data-body="{{ $rn->excerpt ?: Str::limit(strip_tags($rn->body_html), 120) }}" data-url="{{ $rn->url }}" data-image="{{ $rn->cover_image_url }}">
                                {{ Str::limit($rn->title, 60) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="news_id" id="newsIdInput">

                <div class="form-group">
                    <label class="form-label">নোটিফিকেশনের শিরোনাম *</label>
                    <input type="text" name="title" id="notifTitle" required class="form-control" placeholder="যেমন: ব্রেকিং: ঢাকায় তীব্র ভূমিকম্প অনুভূত">
                </div>

                <div class="form-group">
                    <label class="form-label">বার্তা / সারসংক্ষেপ *</label>
                    <textarea name="body" id="notifBody" rows="3" required class="form-control" placeholder="পাঠকের ডিভাইসে প্রদর্শনের জন্য সংক্ষিপ্ত ২-৩ লাইনের বার্তা..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">ক্লিক ইউআরএল (ট্যাপ করলে যে সংবাদ খুলবে) *</label>
                    <input type="url" name="url" id="notifUrl" required class="form-control" value="{{ url('/') }}" placeholder="https://example.com/news/123/slug">
                </div>

                <div class="form-group">
                    <label class="form-label">ব্যানার ছবির ইউআরএল (ঐচ্ছিক)</label>
                    <input type="url" name="image_url" id="notifImage" class="form-control" placeholder="https://example.com/storage/photo.jpg">
                </div>

                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 12px; color: #64748b;">
                    💡 বাটন চাপামাত্রই সকল সক্রিয় ({{ bn_num($activeSubscribersCount) }} জন) পাঠকের ব্রাউজারে নোটিফিকেশন সম্প্রচার হবে।
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;" onclick="return confirm('আপনি কি নিশ্চিত এই নোটিফিকেশনটি পাঠাতে চান?');">
                    🚀 নোটিফিকেশন সম্প্রচার করুন
                </button>
            </form>
        </div>
    </div>

    <!-- Recent Logs -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">সাম্প্রতিক প্রেরিত নোটিফিকেশন</h3>
            <a href="{{ route('admin.notifications.logs') }}" class="btn btn-secondary btn-sm" style="font-size: 11px;">সব দেখুন</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>শিরোনাম</th>
                            <th>ডেলিভারি</th>
                            <th>সময়</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs as $log)
                            <tr>
                                <td>
                                    <div style="font-weight: 600; font-size: 13px; color: #1e293b;">
                                        {{ Str::limit($log->title, 40) }}
                                    </div>
                                    <div style="font-size: 11px; color: #94a3b8;">
                                        প্রেরক: {{ $log->sender?->name ?? 'সিস্টেম' }}
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: #059669; font-size: 12px;">
                                        ✓ {{ bn_num($log->successful_sent) }}
                                    </span>
                                </td>
                                <td style="font-size: 11px; color: #64748b; white-space: nowrap;">
                                    {{ bn_time_ago($log->created_at) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: #94a3b8; padding: 24px;">
                                    এখনো কোনো নোটিফিকেশন পাঠানো হয়নি।
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function autoFillFromNews(select) {
    const selected = select.options[select.selectedIndex];
    if (selected.value) {
        document.getElementById('newsIdInput').value = selected.value;
        document.getElementById('notifTitle').value = selected.getAttribute('data-title') || '';
        document.getElementById('notifBody').value = selected.getAttribute('data-body') || '';
        document.getElementById('notifUrl').value = selected.getAttribute('data-url') || '';
        document.getElementById('notifImage').value = selected.getAttribute('data-image') || '';
    }
}
</script>
@endpush
@endsection

