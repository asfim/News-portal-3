@extends('layouts.admin')

@section('title', 'তৈরিকৃত সোশ্যাল পোস্টারসমূহ')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">সোশ্যাল মিডিয়া পোস্টার ও ব্যানার</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">ফেসবুক ও ইনস্টাগ্রামের জন্য প্রস্তুতকৃত ডায়নামিক বাংলা নিউজ পোস্টার</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.posters.create') }}" class="btn btn-primary btn-sm">＋ নতুন পোস্টার তৈরি করুন</a>
        <a href="{{ route('admin.automation.index') }}" class="btn btn-secondary btn-sm">অটোমেশন সেন্টার</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px;">
    @forelse($posters as $poster)
        <div class="card" style="margin-bottom: 0; display: flex; flex-direction: column;">
            <div style="aspect-ratio: {{ $poster->dimensions === '1200x630' ? '1200/630' : '1/1' }}; overflow: hidden; background: #0f172a; position: relative;">
                <img src="{{ $poster->url }}" alt="{{ $poster->headline_text }}" style="width: 100%; height: 100%; object-fit: contain;" loading="lazy">
                <span style="position: absolute; top: 8px; right: 8px; background: rgba(0,0,0,0.7); color: #fff; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                    {{ $poster->dimensions }}
                </span>
            </div>
            <div class="card-body" style="padding: 14px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <h4 style="font-size: 13px; font-weight: 700; color: #1e293b; line-height: 1.4; margin-bottom: 6px;">
                        {{ Str::limit($poster->headline_text, 60) }}
                    </h4>
                    <div style="font-size: 11px; color: #64748b;">
                        তৈরি: {{ bn_date($poster->created_at) }} | সাইজ: {{ round($poster->file_size_bytes / 1024) }} KB
                    </div>
                </div>
                <div style="margin-top: 12px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eee; padding-top: 10px;">
                    <a href="{{ $poster->url }}" download class="btn btn-sm btn-secondary" style="font-size: 11px;">⬇ ডাউনলোড</a>
                    <form action="{{ route('admin.posters.destroy', $poster->id) }}" method="POST" onsubmit="return confirm('পোস্টারটি মুছে ফেলতে চান?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" style="font-size: 11px;">মুছুন</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="card" style="grid-column: 1 / -1; padding: 50px; text-align: center; color: var(--text-muted);">
            এখনো কোনো পোস্টার তৈরি করা হয়নি। "নতুন পোস্টার তৈরি করুন" বাটনে ক্লিক করে সংবাদ নির্বাচন করুন।
        </div>
    @endforelse
</div>

@if($posters->hasPages())
    <div style="margin-top: 20px;">
        {{ $posters->links() }}
    </div>
@endif
@endsection
