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
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
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
                                <form action="{{ route('admin.social.accounts.test', $acc->id) }}" method="POST" style="display: inline;" title="মেটা গ্রাফ এপিআই কানেকশন টেস্ট করুন">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm" style="color: #0284c7; font-weight: 600;">
                                        ⚡ টেস্ট
                                    </button>
                                </form>
                                <a href="{{ route('admin.social.accounts.edit', $acc->id) }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 4px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    এডিট
                                </a>
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
                                    <button type="submit" class="btn btn-danger btn-sm" title="মুছে ফেলুন">&times;</button>
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

<div class="card" style="margin-top: 24px; border-left: 4px solid #1877f2;">
    <div class="card-body" style="padding: 20px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0 0 10px 0; display: flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1877f2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
            ফেসবুকে অটো-পোস্ট সংক্রান্ত নির্দেশিকা ও সমাধান
        </h3>
        <div style="font-size: 13px; color: #475569; line-height: 1.6;">
            <ol style="margin: 0; padding-left: 20px;">
                <li style="margin-bottom: 6px;">
                    <strong>টোকেন ভ্যালিডেশন:</strong> সংযুক্ত পেজের পাশে থাকা <strong>"⚡ টেস্ট"</strong> বাটনে ক্লিক করে নিশ্চিত হোন মেটা গ্রাফ এপিআই কানেকশন সচল আছে কিনা।
                </li>
                <li style="margin-bottom: 6px;">
                    <strong>টোকেন মেয়াদোত্তীর্ণ ত্রুটি (Error 190):</strong> ফেসবুক ইউজার লগআউট করলে বা পাসওয়ার্ড পরিবর্তন করলে পূর্বের টোকেন বাতিল হয়ে যায়। সেক্ষেত্রে <a href="https://developers.facebook.com/tools/explorer/" target="_blank" style="color: #1877f2; text-decoration: underline;">Meta Graph API Explorer</a> থেকে নতুন <em>Page Access Token</em> তৈরি করে <strong>"এডিট"</strong> বাটনে গিয়ে পেস্ট করুন।
                </li>
                <li style="margin-bottom: 6px;">
                    <strong>প্রয়োজনীয় পারমিশন:</strong> টোকেন তৈরি করার সময় অবশ্যই <code>pages_manage_posts</code>, <code>pages_read_engagement</code> এবং <code>pages_show_list</code> পারমিশনগুলো টিক দিন।
                </li>
                <li>
                    <strong>স্বয়ংক্রিয় প্রকাশ:</strong> পেজে <em>"স্বয়ংক্রিয় প্রকাশ (Auto Publish)"</em> চালু থাকলে নতুন অনুমোদিত সংবাদ প্রকাশিত হওয়ার সাথে সাথেই ব্যাকগ্রাউন্ডে স্বয়ংক্রিয়ভাবে পোস্টার সহ পোস্ট হয়ে যাবে। ব্যর্থ পোস্ট দেখতে <a href="{{ route('admin.social.posts.index') }}" style="color: #1877f2; font-weight: 600;">পোস্ট হিস্টোরি ও লগ</a> দেখুন।
                </li>
            </ol>
        </div>
    </div>
</div>
@endsection
