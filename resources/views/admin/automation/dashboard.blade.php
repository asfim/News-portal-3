@extends('layouts.admin')

@section('title', 'অটোমেশন কন্ট্রোল সেন্টার')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">নিউজ অটোমেশন ও এআই কন্ট্রোল সেন্টার</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">স্বয়ংক্রিয় সংবাদ সংগ্রহ, এআই প্রসেসিং, সোশ্যাল শেয়ারিং, নোটিফিকেশন ও শিডিউলার নিয়ন্ত্রণ</p>
    </div>
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        <form action="{{ route('admin.automation.trigger-imports') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">⚡ ফিড সংগ্রহ চালান</button>
        </form>
        <form action="{{ route('admin.automation.trigger-scheduled') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm">◷ শিডিউলড প্রকাশ চালান</button>
        </form>
        <a href="{{ route('admin.automation.sources.create') }}" class="btn btn-secondary btn-sm">＋ নতুন সোর্স যোগ</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<!-- 10 Comprehensive Metric Cards -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 24px;">
    <div class="stat-card stat-green">
        <div class="stat-card-top">
            <span class="stat-label">সক্রিয় সোর্স</span>
            <span class="stat-icon">📡</span>
        </div>
        <div class="stat-value">{{ bn_num($activeSourcesCount) }} / {{ bn_num($totalSourcesCount) }}</div>
        <div class="stat-caption">স্বয়ংক্রিয় ফিড সংগ্রহ সক্রিয়</div>
    </div>

    <div class="stat-card stat-blue">
        <div class="stat-card-top">
            <span class="stat-label">আজকের সংগৃহীত</span>
            <span class="stat-icon">📥</span>
        </div>
        <div class="stat-value">{{ bn_num($todayImportsCount) }}</div>
        <div class="stat-caption">ডুপ্লিকেট বাদ: {{ bn_num($todayDuplicatesCount) }}টি</div>
    </div>

    <div class="stat-card stat-amber">
        <div class="stat-card-top">
            <span class="stat-label">পর্যালোচনায় অপেক্ষমাণ</span>
            <span class="stat-icon">⏳</span>
        </div>
        <div class="stat-value">{{ bn_num($pendingEditorialReview) }}</div>
        <div class="stat-caption">সম্পাদকের অনুমোদনের অপেক্ষায়</div>
    </div>

    <div class="stat-card stat-green">
        <div class="stat-card-top">
            <span class="stat-label">আজকে প্রকাশিত</span>
            <span class="stat-icon">✓</span>
        </div>
        <div class="stat-value">{{ bn_num($publishedToday) }}</div>
        <div class="stat-caption">আজকের লাইভ সংবাদ</div>
    </div>

    <div class="stat-card stat-purple">
        <div class="stat-card-top">
            <span class="stat-label">আজকের এআই রিকোয়েস্ট</span>
            <span class="stat-icon">⚡</span>
        </div>
        <div class="stat-value">{{ bn_num($aiRequestsToday) }}</div>
        <div class="stat-caption">মডেল: {{ $aiSetting->model }}</div>
    </div>

    <div class="stat-card stat-blue">
        <div class="stat-card-top">
            <span class="stat-label">ফেসবুক পোস্ট</span>
            <span class="stat-icon">📢</span>
        </div>
        <div class="stat-value">{{ bn_num($socialPostsPublished) }}</div>
        <div class="stat-caption">অপেক্ষমাণ: {{ bn_num($socialPostsPending) }}টি</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-label">সোশ্যাল পোস্টার</span>
            <span class="stat-icon">🖼️</span>
        </div>
        <div class="stat-value">{{ bn_num($postersCount) }}</div>
        <div class="stat-caption">কার্ড ও ব্যানার জেনারেট</div>
    </div>

    <div class="stat-card stat-rose">
        <div class="stat-card-top">
            <span class="stat-label">পুশ নোটিফিকেশন</span>
            <span class="stat-icon">🔔</span>
        </div>
        <div class="stat-value">{{ bn_num($pushNotificationsSent) }}</div>
        <div class="stat-caption">গ্রাহক: {{ bn_num($pushSubscribersCount) }} জন</div>
    </div>

    <div class="stat-card stat-slate">
        <div class="stat-card-top">
            <span class="stat-label">নিউজলেটার ডেলিভারি</span>
            <span class="stat-icon">✉️</span>
        </div>
        <div class="stat-value">{{ bn_num($newsletterDeliveriesSent) }}</div>
        <div class="stat-caption">গ্রাহক: {{ bn_num($newsletterSubscribersCount) }} জন</div>
    </div>

    <div class="stat-card {{ $failedJobsCount > 0 ? 'stat-rose' : 'stat-green' }}">
        <div class="stat-card-top">
            <span class="stat-label">কিউ ও ব্যর্থ জব</span>
            <span class="stat-icon">⚙️</span>
        </div>
        <div class="stat-value">{{ bn_num($failedJobsCount) }}</div>
        <div class="stat-caption">রানিং কিউ জব: {{ bn_num($queueJobsCount) }}টি</div>
    </div>
</div>

<!-- Scheduler & Queue Management Strip -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div>
            <h3 class="card-title">ব্যাকগ্রাউন্ড অটোমেশন ও শিডিউলার স্থিতি</h3>
            <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">স্বয়ংক্রিয় ব্যাকগ্রাউন্ড প্রসেস ও কিউ জব নিয়ন্ত্রণ</p>
        </div>
        @if($failedJobsCount > 0)
            <div style="display: flex; gap: 8px;">
                <form action="{{ route('admin.automation.retry-all-failed') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">সকল ব্যর্থ জব রিট্রাই করুন</button>
                </form>
                <form action="{{ route('admin.automation.flush-failed') }}" method="POST" onsubmit="return confirm('ব্যর্থ জব হিস্টোরি ক্লিয়ার করতে চান?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary">ক্লিয়ার করুন</button>
                </form>
            </div>
        @endif
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; background: #fafafa;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <strong style="font-size: 14px; color: #0f172a;">১. নিউজ ফিড সংগ্রহ (RSS/API)</strong>
                    <span class="status-badge status-published">প্রতি ৫ মিনিট</span>
                </div>
                <p style="font-size: 12px; color: #64748b; margin: 0 0 10px;">কমান্ড: <code>news:import-feeds</code></p>
                <form action="{{ route('admin.automation.trigger-imports') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary" style="font-size: 12px; width: 100%; justify-content: center;">
                        ▶ এখনই চালান (Manual Trigger)
                    </button>
                </form>
            </div>

            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; background: #fafafa;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <strong style="font-size: 14px; color: #0f172a;">২. শিডিউলড সংবাদ প্রকাশ</strong>
                    <span class="status-badge status-published">প্রতি ১ মিনিট</span>
                </div>
                <p style="font-size: 12px; color: #64748b; margin: 0 0 10px;">কমান্ড: <code>news:publish-scheduled</code></p>
                <form action="{{ route('admin.automation.trigger-scheduled') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary" style="font-size: 12px; width: 100%; justify-content: center;">
                        ▶ এখনই চালান (Manual Trigger)
                    </button>
                </form>
            </div>

            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; background: #fafafa;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <strong style="font-size: 14px; color: #0f172a;">৩. দৈনিক সংবাদ বুলেটিন</strong>
                    <span class="status-badge status-published">প্রতিদিন সকাল ৭:০০</span>
                </div>
                <p style="font-size: 12px; color: #64748b; margin: 0 0 10px;">কমান্ড: <code>news:send-daily-digest</code></p>
                <form action="{{ route('admin.newsletter.send-daily-digest') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary" style="font-size: 12px; width: 100%; justify-content: center;">
                        ▶ এখনই পাঠান (Manual Trigger)
                    </button>
                </form>
            </div>
        </div>

        @if($failedJobs->isNotEmpty())
            <div style="margin-top: 20px;">
                <h4 style="font-size: 13px; font-weight: 700; color: #dc2626; margin-bottom: 8px;">সাম্প্রতিক ব্যর্থ জব তালিকা:</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>আইডি</th>
                                <th>কানেকশন / কিউ</th>
                                <th>ব্যর্থ হওয়ার কারণ</th>
                                <th>ব্যর্থতার সময়</th>
                                <th style="text-align: right;">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($failedJobs as $fj)
                                <tr>
                                    <td>#{{ $fj->id }}</td>
                                    <td><code>{{ $fj->connection }} / {{ $fj->queue }}</code></td>
                                    <td style="font-size: 12px; color: #dc2626;">{{ Str::limit($fj->exception, 80) }}</td>
                                    <td style="font-size: 11px; color: #64748b;">{{ $fj->failed_at }}</td>
                                    <td style="text-align: right;">
                                        <form action="{{ route('admin.automation.retry-failed-job', $fj->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary" style="font-size: 11px;">রিট্রাই</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Two Columns Layout for Recent Activity -->
<div class="admin-grid-2col" style="grid-template-columns: 1.2fr 1fr;">
    <!-- Left Column: Recent Imported Drafts -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">সাম্প্রতিক সংগৃহীত সংবাদ (ড্রাফট)</h3>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">ফিড থেকে সংগৃহীত সর্বশেষ সংবাদগুলো পর্যালোচনা ও সম্পাদনা করুন</p>
            </div>
            <a href="{{ route('admin.automation.imports.index') }}" class="btn btn-secondary btn-sm">সব দেখুন →</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>শিরোনাম ও সোর্স</th>
                            <th>সংগ্রহের সময়</th>
                            <th style="text-align: right;">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentImports as $import)
                            <tr>
                                <td>
                                    <div style="font-weight: 600; font-size: 14px; color: #1e293b; line-height: 1.4;">
                                        {{ $import->title }}
                                    </div>
                                    <div style="font-size: 11px; color: #64748b; margin-top: 4px; display: flex; gap: 8px;">
                                        <span style="font-weight: 600; color: #c62828;">{{ $import->source?->name ?? 'সোর্স' }}</span>
                                        <span>•</span>
                                        <a href="{{ $import->source_url }}" target="_blank" rel="noopener" style="color: #64748b; text-decoration: none;">মূল লিংক ↗</a>
                                    </div>
                                </td>
                                <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                    {{ bn_date($import->created_at) }}
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    @if($import->news)
                                        <a href="{{ route('admin.news.edit', $import->news->id) }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 8px;">সম্পাদনা / প্রকাশ</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                    এখনো কোনো সংবাদ সংগ্রহ হয়নি। সোর্স যোগ করে "ফিড সংগ্রহ চালান" চাপুন।
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Recent Import Logs -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">সাম্প্রতিক ফিড সংগ্রহের লগ</h3>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">ফিড পার্সারের সাফল্য ও ত্রুটি বিবরণ</p>
            </div>
            <a href="{{ route('admin.automation.logs.index') }}" class="btn btn-secondary btn-sm">সব লগ →</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>সোর্স</th>
                            <th>ফলাফল</th>
                            <th>সময়কাল</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentImportLogs as $log)
                            <tr>
                                <td>
                                    <div style="font-weight: 600; font-size: 13px;">{{ $log->source?->name ?? 'সোর্স' }}</div>
                                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                        নতুন: {{ bn_num($log->items_imported) }} | ডুপ্লিকেট: {{ bn_num($log->items_skipped_duplicate) }}
                                    </div>
                                </td>
                                <td>
                                    @if($log->status === 'success')
                                        <span class="status-badge status-published">সফল</span>
                                    @elseif($log->status === 'warning')
                                        <span class="status-badge status-review_pending">সতর্কতা</span>
                                    @else
                                        <span class="status-badge status-archived">ব্যর্থ</span>
                                    @endif
                                </td>
                                <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                    {{ bn_num($log->execution_time_seconds) }} সে.
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                    কোনো লগ পাওয়া যায়নি।
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
