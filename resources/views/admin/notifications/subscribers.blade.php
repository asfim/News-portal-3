@extends('layouts.admin')

@section('title', 'পুশ নোটিফিকেশন গ্রাহক তালিকা')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">পুশ নোটিফিকেশন গ্রাহক তালিকা</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">পোর্টালের পুশ অ্যালার্ট গ্রহণকারী পাঠকদের ব্রাউজার সাবস্ক্রিপশন রেকর্ড</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary btn-sm">&larr; নোটিফিকেশন কেন্দ্র</a>
        <a href="{{ route('admin.notifications.logs') }}" class="btn btn-secondary btn-sm">ডেলিভারি হিস্টোরি</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">আইডি</th>
                        <th>পাঠক / ব্যবহারকারী</th>
                        <th>ডিভাইস / ব্রাউজার</th>
                        <th>এন্ডপয়েন্ট হ্যাশ</th>
                        <th>অবস্থা</th>
                        <th>নিবন্ধনের সময়</th>
                        <th style="text-align: right;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $sub)
                        <tr>
                            <td>{{ bn_num($sub->id) }}</td>
                            <td>
                                <strong>{{ $sub->user?->name ?? 'অতিথি পাঠক (Guest)' }}</strong>
                                @if($sub->user)
                                    <div style="font-size: 11px; color: #64748b;">{{ $sub->user->email }}</div>
                                @endif
                            </td>
                            <td style="font-size: 12px; color: #475569;">
                                {{ Str::limit($sub->user_agent, 45) }}
                            </td>
                            <td>
                                <code style="font-size: 11px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">{{ substr($sub->endpoint_hash, 0, 14) }}...</code>
                            </td>
                            <td>
                                @if($sub->is_active)
                                    <span class="status-badge status-published">সক্রিয়</span>
                                @else
                                    <span class="status-badge status-draft">নিষ্ক্রিয় / আনসাবস্ক্রাইবড</span>
                                @endif
                            </td>
                            <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                {{ bn_date($sub->created_at) }}
                            </td>
                            <td style="text-align: right;">
                                <form action="{{ route('admin.notifications.subscribers.toggle', $sub->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 11px;">
                                        {{ $sub->is_active ? 'নিষ্ক্রিয় করুন' : 'সক্রিয় করুন' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #94a3b8; padding: 40px;">
                                এখনো কোনো পাঠক ব্রাউজার নোটিফিকেশনে সম্মতি দেননি।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($subscribers->hasPages())
    <div style="margin-top: 20px;">
        {{ $subscribers->links() }}
    </div>
@endif
@endsection

