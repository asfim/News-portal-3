@extends('layouts.admin')

@section('title', 'নিউজলেটার গ্রাহক তালিকা')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">নিউজলেটার গ্রাহক তালিকা</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">ইমেইল নিউজলেটার সাবস্ক্রিপশন ও ভেরিফিকেশন স্ট্যাটাস</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.newsletter.index') }}" class="btn btn-secondary btn-sm">&larr; নিউজলেটার ক্যাম্পেইন</a>
        <a href="{{ route('admin.newsletter.create') }}" class="btn btn-primary btn-sm">＋ নতুন ক্যাম্পেইন</a>
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
                        <th>ইমেইল ঠিকানা</th>
                        <th>নাম</th>
                        <th>ভেরিফিকেশন অবস্থা</th>
                        <th>সাবস্ক্রিপশন স্ট্যাটাস</th>
                        <th>নিবন্ধনের তারিখ</th>
                        <th style="text-align: right;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $sub)
                        <tr>
                            <td>{{ bn_num($sub->id) }}</td>
                            <td>
                                <strong style="font-size: 14px; color: #1e293b;">{{ $sub->email }}</strong>
                            </td>
                            <td style="font-size: 13px; color: #475569;">
                                {{ $sub->name ?: 'অনুল্লিখিত' }}
                            </td>
                            <td>
                                @if($sub->verified_at)
                                    <span class="status-badge status-published">✓ ভেরিফাইড ({{ bn_date($sub->verified_at) }})</span>
                                @else
                                    <span class="status-badge status-review_pending">অপেক্ষমাণ</span>
                                @endif
                            </td>
                            <td>
                                @if($sub->is_active)
                                    <span class="status-badge status-published">সক্রিয়</span>
                                @else
                                    <span class="status-badge status-archived">আনসাবস্ক্রাইবড</span>
                                @endif
                            </td>
                            <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                {{ bn_date($sub->created_at) }}
                            </td>
                            <td style="text-align: right;">
                                <form action="{{ route('admin.newsletter.subscribers.toggle', $sub->id) }}" method="POST" style="display: inline;">
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
                                এখনো কোনো গ্রাহক নিউজলেটারে সাবস্ক্রাইব করেননি।
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

