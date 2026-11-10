@extends('layouts.admin')

@section('title', 'নিউজ অটোমেশন লগ')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">নিউজ অটোমেশন ও ফিড পার্সার লগ</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">স্বয়ংক্রিয় শিডিউলার ও অন-ডিমান্ড ফিড সংগ্রহের ইতিহাস ও ত্রুটি বিবরণ</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.automation.sources.index') }}" class="btn btn-secondary btn-sm">নিউজ সোর্সসমূহ</a>
        <a href="{{ route('admin.automation.index') }}" class="btn btn-secondary btn-sm">অটোমেশন কন্ট্রোল সেন্টার</a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>তারিখ ও সময়</th>
                        <th>সোর্স</th>
                        <th>ফলাফল</th>
                        <th>সংবাদ সংখ্যা (নতুন / ডুপ্লিকেট / ব্যর্থ)</th>
                        <th>কার্যকাল</th>
                        <th>বার্তা / ত্রুটি</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="font-size: 12px; color: #475569; white-space: nowrap;">
                                {{ bn_date($log->created_at) }}
                            </td>
                            <td>
                                <strong>{{ $log->source?->name ?? 'মুছে ফেলা সোর্স' }}</strong>
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
                            <td style="font-size: 13px;">
                                <span style="color: #16a34a; font-weight: 600;">+{{ bn_num($log->items_imported) }}</span> / 
                                <span style="color: #64748b;">{{ bn_num($log->items_skipped_duplicate) }}</span> / 
                                <span style="color: #dc2626; font-weight: 600;">{{ bn_num($log->items_failed) }}</span>
                            </td>
                            <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                {{ bn_num($log->execution_time_seconds) }} সেকেন্ড
                            </td>
                            <td style="font-size: 12px; color: #334155; max-width: 320px;">
                                {{ $log->message }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                কোনো লগ এন্ট্রি পাওয়া যায়নি।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($logs->hasPages())
    <div style="margin-top: 16px;">
        {{ $logs->links() }}
    </div>
@endif
@endsection
