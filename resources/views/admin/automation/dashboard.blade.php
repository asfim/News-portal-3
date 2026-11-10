@extends('layouts.admin')

@section('title', 'অটোমেশন কন্ট্রোল সেন্টার')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">নিউজ অটোমেশন ও এআই কন্ট্রোল সেন্টার</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">স্বয়ংক্রিয় সংবাদ সংগ্রহ, এআই প্রসেসিং, সোশ্যাল শেয়ারিং ও পাঠক এনগেজমেন্ট নিয়ন্ত্রণ</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.automation.sources.create') }}" class="btn btn-primary btn-sm">＋ নতুন সোর্স যোগ করুন</a>
        <a href="{{ route('admin.automation.sources.index') }}" class="btn btn-secondary btn-sm">সকল সোর্স তালিকা</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<!-- Stats Grid -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 24px;">
    <div class="stat-card stat-green">
        <div class="stat-card-top">
            <span class="stat-label">সক্রিয় নিউজ সোর্স</span>
            <span class="stat-icon">📡</span>
        </div>
        <div class="stat-value">{{ bn_num($activeSourcesCount) }} / {{ bn_num($totalSourcesCount) }}</div>
        <div class="stat-caption">স্বয়ংক্রিয় ফিড সংগ্রহ সক্রিয়</div>
    </div>

    <div class="stat-card stat-blue">
        <div class="stat-card-top">
            <span class="stat-label">আজকের সংগৃহীত সংবাদ</span>
            <span class="stat-icon">📥</span>
        </div>
        <div class="stat-value">{{ bn_num($todayImportsCount) }}</div>
        <div class="stat-caption">ড্রাফট পর্যালোচনায় অপেক্ষমাণ</div>
    </div>

    <div class="stat-card stat-slate">
        <div class="stat-card-top">
            <span class="stat-label">বাদ দেওয়া ডুপ্লিকেট</span>
            <span class="stat-icon">🛡️</span>
        </div>
        <div class="stat-value">{{ bn_num($todayDuplicatesCount) }}</div>
        <div class="stat-caption">স্বয়ংক্রিয়ভাবে ফিল্টার করা হয়েছে</div>
    </div>

    <div class="stat-card stat-purple">
        <div class="stat-card-top">
            <span class="stat-label">আজকের এআই রিকোয়েস্ট</span>
            <span class="stat-icon">⚡</span>
        </div>
        <div class="stat-value">{{ bn_num($aiRequestsToday) }}</div>
        <div class="stat-caption">মডেল: {{ $aiSetting->model }}</div>
    </div>

    <div class="stat-card stat-amber">
        <div class="stat-card-top">
            <span class="stat-label">সোশ্যাল পোস্ট ও শিডিউল</span>
            <span class="stat-icon">📢</span>
        </div>
        <div class="stat-value">{{ bn_num($socialPostsPublished) }}</div>
        <div class="stat-caption">অপেক্ষমাণ: {{ bn_num($socialPostsPending) }}টি</div>
    </div>

    <div class="stat-card stat-rose">
        <div class="stat-card-top">
            <span class="stat-label">পুশ নোটিফিকেশন গ্রাহক</span>
            <span class="stat-icon">🔔</span>
        </div>
        <div class="stat-value">{{ bn_num($pushSubscribersCount) }}</div>
        <div class="stat-caption">মোট ডেলিভারি: {{ bn_num($pushNotificationsSent) }}</div>
    </div>

    <div class="stat-card stat-blue">
        <div class="stat-card-top">
            <span class="stat-label">নিউজলেটার গ্রাহক</span>
            <span class="stat-icon">✉️</span>
        </div>
        <div class="stat-value">{{ bn_num($newsletterSubscribersCount) }}</div>
        <div class="stat-caption">মোট ক্যাম্পেইন: {{ bn_num($newsletterCampaignsCount) }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-label">তৈরিকৃত সোশ্যাল পোস্টার</span>
            <span class="stat-icon">🖼️</span>
        </div>
        <div class="stat-value">{{ bn_num($postersCount) }}</div>
        <div class="stat-caption">সোশ্যাল মিডিয়ার জন্য রেডি</div>
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
                                    এখনো কোনো সংবাদ সংগ্রহ হয়নি। সোর্স যোগ করে "এখনই সংগ্রহ করুন" চাপুন।
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
