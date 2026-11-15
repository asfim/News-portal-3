@extends('layouts.admin')

@section('title', 'নতুন নিউজলেটার ক্যাম্পেইন তৈরি')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="admin-page-header">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">নতুন নিউজলেটার ক্যাম্পেইন তৈরি</h1>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">পাঠকদের জন্য আকর্ষণীয় সংবাদ বুলেটিন লিখুন অথবা সাম্প্রতিক সংবাদ থেকে স্বয়ংক্রিয় তৈরি করুন</p>
        </div>
        <a href="{{ route('admin.newsletter.index') }}" class="btn btn-secondary">&larr; ক্যাম্পেইন তালিকায় ফিরে যান</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.newsletter.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">ক্যাম্পেইনের অভ্যন্তরীণ নাম *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="form-control" placeholder="যেমন: সাপ্তাহিক প্রযুক্তি বুলেটিন - ১২ নভেম্বর">
                </div>

                <div class="form-group">
                    <label class="form-label">ইমেইলের বিষয় (Email Subject) *</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required class="form-control" placeholder="যেমন: এইচ টেক প্রোভিশন: সপ্তাহের আলোচিত শীর্ষ ৫টি সংবাদ">
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label class="form-label" style="margin-bottom: 0;">ইমেইল কনটেন্ট (HTML Body) *</label>
                        <button type="button" class="btn btn-secondary btn-sm" style="font-size: 11px;" onclick="loadRecentNewsTemplate()">
                            📋 সাম্প্রতিক সেরা সংবাদগুলো দিয়ে স্বয়ংক্রিয় ফরম্যাট তৈরি করুন
                        </button>
                    </div>
                    <textarea name="content_html" id="newsletterEditor" rows="14" required class="form-control" style="font-family: inherit; line-height: 1.6;" placeholder="এখানে ইমেইলের মূল বডি বা বার্তা লিখুন...">{{ old('content_html') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">নির্ধারিত সময়ে স্বয়ংক্রিয় প্রেরণ (ঐচ্ছিক শিডিউল)</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="form-control">
                    <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 4px;">ভবিষ্যতের কোনো সময়ে স্বয়ংক্রিয় পাঠাতে চাইলে তারিখ ও সময় দিন, অন্যথায় খালি রাখুন।</span>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                    <button type="submit" name="action" value="draft" class="btn btn-secondary">
                        খসড়া হিসেবে সংরক্ষণ
                    </button>
                    <button type="submit" name="action" value="send_now" class="btn btn-primary" onclick="return confirm('আপনি কি নিশ্চিত এই নিউজলেটারটি সকল ভেরিফাইড গ্রাহককে পাঠাতে চান?');">
                        🚀 অবিলম্বে পাঠিয়ে দিন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadRecentNewsTemplate() {
    const template = `<h2>প্রিয় পাঠক, আজকের নির্বাচিত গুরুত্বপূর্ণ সংবাদ</h2>
<p>এইচ টেক প্রোভিশন আইটি পোর্টালে প্রকাশিত আজকের শীর্ষ সংবাদগুলোর সারসংক্ষেপ নিচে দেওয়া হলো:</p>
<hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;" />
@foreach($recentNews->take(5) as $rn)
<div style="margin-bottom: 16px;">
    <h3 style="margin: 0 0 6px 0;"><a href="{{ $rn->url }}" style="color: #c62828; text-decoration: none;">{{ $rn->title }}</a></h3>
    <p style="margin: 0; color: #555; font-size: 13px;">{{ $rn->excerpt ?: Str::limit(strip_tags($rn->body_html), 120) }}</p>
</div>
@endforeach
<p style="margin-top: 20px; font-size: 12px; color: #888;">এইচ টেক প্রোভিশন আইটি নিউজ টিম</p>`;

    document.getElementById('newsletterEditor').value = template;
}
</script>
@endpush
@endsection

