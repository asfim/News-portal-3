@extends('layouts.admin')

@section('title', 'ইমেইল নিউজলেটার ব্যবস্থাপনা')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">ইমেইল নিউজলেটার ও ক্যাম্পেইন</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">পাঠকদের ইমেইল সাবস্ক্রিপশন, স্বয়ংক্রিয় দৈনিক সংবাদ বুলেটিন ও ক্যাম্পেইন ডেলিভারি</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <form action="{{ route('admin.newsletter.send-daily-digest') }}" method="POST" style="display: inline;" onsubmit="return confirm('আজকের দৈনিক সংবাদ বুলেটিন সকল ভেরিফাইড গ্রাহককে পাঠাতে চান?');">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                ⚡ দৈনিক ডাইজেস্ট পাঠান
            </button>
        </form>
        <a href="{{ route('admin.newsletter.subscribers') }}" class="btn btn-secondary btn-sm">গ্রাহক তালিকা ({{ bn_num($verifiedCount) }}) →</a>
        <a href="{{ route('admin.newsletter.create') }}" class="btn btn-primary btn-sm">＋ নতুন ক্যাম্পেইন তৈরি</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 24px;">
    <div class="stat-card stat-green">
        <div class="stat-card-top">
            <span class="stat-label">ভেরিফাইড ইমেইল গ্রাহক</span>
            <span class="stat-icon">✉️</span>
        </div>
        <div class="stat-value">{{ bn_num($verifiedCount) }} / {{ bn_num($totalSubscribers) }}</div>
        <div class="stat-caption">সক্রিয় ডাবল অপ্ট-ইন গ্রাহক</div>
    </div>

    <div class="stat-card stat-blue">
        <div class="stat-card-top">
            <span class="stat-label">মোট ক্যাম্পেইন সংখ্যা</span>
            <span class="stat-icon">📑</span>
        </div>
        <div class="stat-value">{{ bn_num($campaignsCount) }}</div>
        <div class="stat-caption">বুলেটিন ও বিশেষ ইমেইল</div>
    </div>

    <div class="stat-card stat-purple">
        <div class="stat-card-top">
            <span class="stat-label">সফল ইমেইল ডেলিভারি</span>
            <span class="stat-icon">✓</span>
        </div>
        <div class="stat-value">{{ bn_num($totalDeliveries) }}</div>
        <div class="stat-caption">মোট প্রেরিত ইমেইল সংখ্যা</div>
    </div>

    <div class="stat-card stat-amber">
        <div class="stat-card-top">
            <span class="stat-label">দৈনিক ডাইজেস্ট শিডিউল</span>
            <span class="stat-icon">◷</span>
        </div>
        <div class="stat-value" style="font-size: 18px; color: #b45309;">প্রতিদিন সকাল ৭:০০</div>
        <div class="stat-caption">Laravel Scheduler দ্বারা পরিচালিত</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">নিউজলেটার ক্যাম্পেইন তালিকা</h3>
        <a href="{{ route('admin.newsletter.deliveries') }}" class="btn btn-secondary btn-sm" style="font-size: 12px;">ডেলিভারি রিপোর্ট দেখুন</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ক্যাম্পেইনের নাম</th>
                        <th>ইমেইল বিষয় (Subject)</th>
                        <th>প্রাপক সংখ্যা</th>
                        <th>ডেলিভারি</th>
                        <th>স্ট্যাটাস</th>
                        <th>তৈরির তারিখ</th>
                        <th>প্রেরক</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $camp)
                        <tr>
                            <td>
                                <strong style="font-size: 14px; color: #1e293b;">{{ $camp->title }}</strong>
                            </td>
                            <td style="font-size: 13px; color: #475569;">
                                {{ $camp->subject }}
                            </td>
                            <td style="font-size: 13px;">
                                {{ bn_num($camp->total_recipients) }} জন
                            </td>
                            <td>
                                <a href="{{ route('admin.newsletter.deliveries') }}?campaign_id={{ $camp->id }}" style="font-weight: 700; color: #0284c7; text-decoration: none;">
                                    {{ bn_num($camp->deliveries_count) }}টি ডেলিভারি
                                </a>
                            </td>
                            <td>
                                @if($camp->status === 'sent')
                                    <span class="status-badge status-published">প্রেরিত</span>
                                @elseif($camp->status === 'scheduled')
                                    <span class="status-badge status-scheduled">নির্ধারিত</span>
                                @else
                                    <span class="status-badge status-draft">খসড়া</span>
                                @endif
                            </td>
                            <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                {{ bn_date($camp->created_at) }}
                            </td>
                            <td style="font-size: 12px; color: #475569;">
                                {{ $camp->creator?->name ?? 'সিস্টেম' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #94a3b8; padding: 40px;">
                                এখনো কোনো ক্যাম্পেইন তৈরি করা হয়নি। "নতুন ক্যাম্পেইন তৈরি" বাটনে ক্লিক করুন।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($campaigns->hasPages())
    <div style="margin-top: 20px;">
        {{ $campaigns->links() }}
    </div>
@endif
@endsection

