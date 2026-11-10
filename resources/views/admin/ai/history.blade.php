@extends('layouts.admin')

@section('title', 'এআই জেনারেশন হিস্টোরি')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">এআই জেনারেশন হিস্টোরি ও লগ</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">এআই অ্যাসিস্ট্যান্ট ব্যবহারের অডিট লগ, মডেল তথ্য ও টোকেন খরচ</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.ai.settings') }}" class="btn btn-secondary btn-sm">এআই সেটিংস</a>
        <a href="{{ route('admin.automation.index') }}" class="btn btn-secondary btn-sm">অটোমেশন সেন্টার</a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>তারিখ ও সময়</th>
                        <th>অ্যাকশন</th>
                        <th>ব্যবহারকারী</th>
                        <th>মডেল ও টোকেন</th>
                        <th>আউটপুট প্রিভিউ</th>
                        <th>স্ট্যাটাস</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                {{ bn_date($log->created_at) }}
                            </td>
                            <td>
                                <span style="background: #e0e7ff; color: #3730a3; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td style="font-size: 13px;">
                                {{ $log->user?->name ?? 'সিস্টেম' }}
                            </td>
                            <td style="font-size: 12px; color: #64748b;">
                                <code>{{ $log->model }}</code>
                                <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                                    টোকেন: {{ bn_num($log->prompt_tokens + $log->completion_tokens) }}
                                </div>
                            </td>
                            <td style="font-size: 12px; color: #334155; max-width: 300px;">
                                {{ Str::limit(strip_tags($log->output_text ?? $log->error_message), 100) }}
                            </td>
                            <td>
                                @if($log->status === 'success')
                                    <span class="status-badge status-published">সফল</span>
                                @else
                                    <span class="status-badge status-archived">ব্যর্থ</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                এখনো কোনো এআই রিকোয়েস্ট লগ পাওয়া যায়নি।
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
