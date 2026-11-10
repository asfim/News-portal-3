@extends('layouts.admin')

@section('title', 'সোশ্যাল মিডিয়া পোস্ট হিস্টোরি')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">সোশ্যাল মিডিয়া পোস্ট হিস্টোরি</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">ফেসবুক পেজে প্রকাশিত পোস্ট, ব্যর্থ প্রচেষ্টা ও পুনঃচেষ্টার বিবরণ</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.social.posts.create') }}" class="btn btn-primary btn-sm">＋ নতুন সোশ্যাল পোস্ট তৈরি</a>
        <a href="{{ route('admin.social.accounts.index') }}" class="btn btn-secondary btn-sm">সংযুক্ত পেজসমূহ</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>তারিখ ও সময়</th>
                        <th>সংবাদ ও পোস্টের টেক্সট</th>
                        <th>পেজ</th>
                        <th>পোস্ট আইডি</th>
                        <th>স্ট্যাটাস</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                {{ bn_date($post->published_at ?? $post->created_at) }}
                            </td>
                            <td>
                                <div style="font-weight: 600; font-size: 14px; color: #1e293b; line-height: 1.4;">
                                    {{ Str::limit($post->post_text, 100) }}
                                </div>
                                @if($post->news)
                                    <div style="font-size: 11px; margin-top: 3px;">
                                        <a href="{{ route('news.show', [$post->news->public_id, $post->news->slug]) }}" target="_blank" style="color: var(--primary); text-decoration: none;">
                                            সংবাদ: {{ Str::limit($post->news->title, 50) }} ↗
                                        </a>
                                    </div>
                                @endif
                                @if($post->last_error)
                                    <div style="color: #dc2626; font-size: 11px; margin-top: 3px;">
                                        ত্রুটি: {{ Str::limit($post->last_error, 80) }}
                                    </div>
                                @endif
                            </td>
                            <td style="font-size: 13px; font-weight: 600;">
                                {{ $post->account?->account_name ?? 'মুছে ফেলা অ্যাকাউন্ট' }}
                            </td>
                            <td>
                                @if($post->external_post_id)
                                    <code style="font-size: 11px; background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px;">{{ $post->external_post_id }}</code>
                                @else
                                    <span style="color: #94a3b8; font-size: 12px;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($post->status === 'published')
                                    <span class="status-badge status-published">প্রকাশিত</span>
                                @elseif($post->status === 'publishing')
                                    <span class="status-badge status-review_pending">চলমান...</span>
                                @elseif($post->status === 'scheduled')
                                    <span class="status-badge status-scheduled">নির্ধারিত</span>
                                @else
                                    <span class="status-badge status-archived">ব্যর্থ (পুনঃচেষ্টা: {{ bn_num($post->retry_count) }})</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                এখনো কোনো সোশ্যাল পোস্টের তথ্য নেই।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($posts->hasPages())
    <div style="margin-top: 16px;">
        {{ $posts->links() }}
    </div>
@endif
@endsection
