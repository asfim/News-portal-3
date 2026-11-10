@extends('layouts.admin')

@section('title', 'ফেসবুকে সংবাদ পোস্ট করুন')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="admin-page-header">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">ফেসবুক পেজে পোস্ট করুন</h1>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">সংবাদ ও পোস্টারসহ ফেসবুক পেজে তাৎক্ষণিক প্রকাশ করুন</p>
        </div>
        <a href="{{ route('admin.social.posts.index') }}" class="btn btn-secondary">&larr; পোস্ট তালিকায় ফিরে যান</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($accounts->isEmpty())
                <div style="text-align: center; padding: 30px; color: #b91c1c;">
                    <div style="font-size: 16px; font-weight: 700; margin-bottom: 8px;">কোনো সক্রিয় ফেসবুক পেজ পাওয়া যায়নি!</div>
                    <p style="font-size: 13px; color: #64748b; margin-bottom: 16px;">পোস্ট করার পূর্বে অন্তত একটি ফেসবুক পেজ সংযুক্ত করতে হবে।</p>
                    <a href="{{ route('admin.social.accounts.create') }}" class="btn btn-primary">＋ নতুন ফেসবুক পেজ সংযোগ করুন</a>
                </div>
            @else
                <form action="{{ route('admin.social.posts.publish') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">ফেসবুক পেজ নির্বাচন করুন *</label>
                        <select name="social_account_id" required class="form-control">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->account_name }} (Page ID: {{ $acc->account_id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">সংবাদ নির্বাচন করুন *</label>
                        <select name="news_id" id="newsSelect" required class="form-control">
                            <option value="">সংবাদ নির্বাচন করুন...</option>
                            @foreach($publishedNews as $pNews)
                                <option value="{{ $pNews->id }}" 
                                        data-title="{{ $pNews->title }}"
                                        data-excerpt="{{ $pNews->excerpt }}"
                                        data-cat="{{ $pNews->primaryCategory?->name ?? 'জাতীয়' }}"
                                        {{ ($selectedNews && $selectedNews->id == $pNews->id) ? 'selected' : '' }}>
                                    [{{ $pNews->primaryCategory?->name ?? 'জাতীয়' }}] {{ Str::limit($pNews->title, 70) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">পোস্টের ক্যাপশন / বার্তা</label>
                        <textarea name="custom_message" id="captionTextarea" rows="5" class="form-control" placeholder="খালি রাখলে স্বয়ংক্রিয়ভাবে শিরোনাম ও সারসংক্ষেপ তৈরি হবে...">{{ $selectedNews ? "【" . ($selectedNews->primaryCategory?->name ?? 'জাতীয়') . "】 " . $selectedNews->title . "\n\n" . $selectedNews->excerpt : '' }}</textarea>
                    </div>

                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" style="background: #1877f2; border-color: #1877f2; padding: 10px 24px; font-size: 15px;">
                            🚀 ফেসবুকে এখনই পোস্ট করুন
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const newsSelect = document.getElementById('newsSelect');
    const captionTextarea = document.getElementById('captionTextarea');

    if (newsSelect && captionTextarea) {
        newsSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.value) {
                const title = opt.getAttribute('data-title') || '';
                const excerpt = opt.getAttribute('data-excerpt') || '';
                const cat = opt.getAttribute('data-cat') || 'জাতীয়';
                captionTextarea.value = `【${cat}】 ${title}\n\n${excerpt}`;
            }
        });
    }
});
</script>
@endsection
