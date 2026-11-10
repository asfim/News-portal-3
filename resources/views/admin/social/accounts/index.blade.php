@extends('layouts.admin')

@section('title', 'সংযুক্ত সোশ্যাল মিডিয়া অ্যাকাউন্ট')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">সোশ্যাল মিডিয়া অ্যাকাউন্ট ও ফেসবুক পেজ</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">অফিসিয়াল মেটা গ্রাফ এপিআই (Meta Graph API) দ্বারা অনুমোদিত পেজ কানেকশন</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.social.posts.index') }}" class="btn btn-secondary btn-sm">পোস্ট হিস্টোরি ও লগ →</a>
        <a href="{{ route('admin.social.accounts.create') }}" class="btn btn-primary btn-sm">＋ নতুন পেজ যুক্ত করুন</a>
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
                        <th>প্ল্যাটফর্ম</th>
                        <th>পেজের নাম ও আইডি</th>
                        <th>স্বয়ংক্রিয় প্রকাশ (Auto Publish)</th>
                        <th>মোট পোস্ট</th>
                        <th>স্ট্যাটাস</th>
                        <th style="text-align: right;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $acc)
                        <tr>
                            <td>
                                <span style="background: #1877f2; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 700;">
                                    Facebook
                                </span>
                            </td>
                            <td>
                                <strong style="font-size: 14px; color: #1e293b;">{{ $acc->account_name }}</strong>
                                <div style="font-size: 11px; color: #64748b; font-family: monospace; margin-top: 2px;">
                                    Page ID: {{ $acc->account_id }}
                                </div>
                            </td>
                            <td>
                                @if($acc->auto_publish)
                                    <span class="status-badge status-published">চালু রয়েছে</span>
                                @else
                                    <span class="status-badge status-draft">ম্যানুয়াল মোড</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight: 700; color: #0284c7;">{{ bn_num($acc->posts_count) }}</span>টি
                            </td>
                            <td>
                                @if($acc->is_active)
                                    <span class="status-badge status-published">সংযুক্ত</span>
                                @else
                                    <span class="status-badge status-archived">সংযোগ বিচ্ছিন্ন</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('admin.social.posts.create') }}?account_id={{ $acc->id }}" class="btn btn-sm btn-primary">পোস্ট করুন</a>
                                <form action="{{ route('admin.social.accounts.toggle', $acc->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm">
                                        {{ $acc->is_active ? 'নিষ্ক্রিয়' : 'সক্রিয়' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.social.accounts.destroy', $acc->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('পেজের সংযোগ বিচ্ছিন্ন করতে চান?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">&times;</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                এখনো কোনো ফেসবুক পেজ সংযুক্ত করা হয়নি। উপরের "নতুন পেজ যুক্ত করুন" বাটনে ক্লিক করুন।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
