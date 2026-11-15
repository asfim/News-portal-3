@extends('layouts.admin')

@section('title', 'নিউজলেটার ডেলিভারি রিপোর্ট')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">নিউজলেটার ডেলিভারি রিপোর্ট</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">প্রতিটি গ্রাহকের ইমেইলে ক্যাম্পেইন পৌঁছানোর অবস্থা ও ত্রুটি রিপোর্ট</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.newsletter.index') }}" class="btn btn-secondary btn-sm">&larr; নিউজলেটার ক্যাম্পেইন</a>
        <a href="{{ route('admin.newsletter.subscribers') }}" class="btn btn-secondary btn-sm">গ্রাহক তালিকা</a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ক্যাম্পেইন</th>
                        <th>গ্রাহকের ইমেইল</th>
                        <th>ডেলিভারি অবস্থা</th>
                        <th>ত্রুটি (যদি থাকে)</th>
                        <th>প্রেরণের সময়</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $del)
                        <tr>
                            <td>
                                <strong style="font-size: 13px; color: #1e293b;">{{ $del->campaign?->title ?? 'ক্যাম্পেইন #' . $del->campaign_id }}</strong>
                            </td>
                            <td style="font-size: 13px; color: #475569;">
                                {{ $del->subscriber?->email ?? '-' }}
                            </td>
                            <td>
                                @if($del->status === 'sent')
                                    <span class="status-badge status-published">✓ সফলভাবে প্রেরিত</span>
                                @else
                                    <span class="status-badge status-archived">ব্যর্থ</span>
                                @endif
                            </td>
                            <td style="font-size: 12px; color: #dc2626;">
                                {{ $del->error_message ?: '-' }}
                            </td>
                            <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                {{ bn_date($del->sent_at ?? $del->created_at) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94a3b8; padding: 40px;">
                                এখনো কোনো ডেলিভারি রেকর্ড পাওয়া যায়নি।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($deliveries->hasPages())
    <div style="margin-top: 20px;">
        {{ $deliveries->links() }}
    </div>
@endif
@endsection

