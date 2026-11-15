@extends('layouts.admin')

@section('title', 'নোটিফিকেশন ডেলিভারি লগ')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">পুশ নোটিফিকেশন ডেলিভারি রিপোর্ট</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">প্রেরিত নোটিফিকেশন, লক্ষ্যমাত্রা ও ডেলিভারি সফলতার পরিসংখ্যান</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary btn-sm">&larr; নোটিফিকেশন কেন্দ্র</a>
        <a href="{{ route('admin.notifications.subscribers') }}" class="btn btn-secondary btn-sm">গ্রাহক তালিকা</a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>শিরোনাম ও বার্তা</th>
                        <th>সম্পর্কিত সংবাদ</th>
                        <th>লক্ষ্যমাত্রা</th>
                        <th>সফল ডেলিভারি</th>
                        <th>ব্যর্থ</th>
                        <th>স্ট্যাটাস</th>
                        <th>প্রেরক</th>
                        <th>তারিখ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <strong style="font-size: 13px; color: #1e293b;">{{ $log->title }}</strong>
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">{{ Str::limit($log->body, 60) }}</div>
                            </td>
                            <td>
                                @if($log->news)
                                    <a href="{{ route('news.show', [$log->news->public_id, $log->news->slug]) }}" target="_blank" style="color: var(--primary); font-size: 12px; text-decoration: none;">
                                        {{ Str::limit($log->news->title, 35) }} ↗
                                    </a>
                                @else
                                    <span style="color: #94a3b8; font-size: 12px;">কাস্টম ইউআরএল</span>
                                @endif
                            </td>
                            <td style="font-size: 13px;">{{ bn_num($log->target_subscribers) }}</td>
                            <td>
                                <span style="font-weight: 700; color: #059669; font-size: 13px;">
                                    {{ bn_num($log->successful_sent) }}
                                </span>
                            </td>
                            <td style="font-size: 13px; color: {{ $log->failed_count > 0 ? '#dc2626' : '#94a3b8' }};">
                                {{ bn_num($log->failed_count) }}
                            </td>
                            <td>
                                @if($log->status === 'sent')
                                    <span class="status-badge status-published">সম্পন্ন</span>
                                @elseif($log->status === 'partial_failed')
                                    <span class="status-badge status-review_pending">আংশিক ব্যর্থ</span>
                                @else
                                    <span class="status-badge status-archived">ব্যর্থ</span>
                                @endif
                            </td>
                            <td style="font-size: 12px; color: #475569;">
                                {{ $log->sender?->name ?? 'স্বয়ংক্রিয়' }}
                            </td>
                            <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                {{ bn_date($log->created_at) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: #94a3b8; padding: 40px;">
                                এখনো কোনো নোটিফিকেশন লগ সংরক্ষিত নেই।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($logs->hasPages())
    <div style="margin-top: 20px;">
        {{ $logs->links() }}
    </div>
@endif
@endsection

