@extends('layouts.admin')

@section('title', 'নিউজ সোর্স তালিকা')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">অনুমোদিত নিউজ সোর্সসমূহ</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">স্বয়ংক্রিয় সংবাদ সংগ্রহের জন্য অনুমোদিত RSS ও API সোর্স পরিচালনা করুন</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.automation.index') }}" class="btn btn-secondary btn-sm">← অটোমেশন কন্ট্রোল সেন্টার</a>
        <a href="{{ route('admin.automation.sources.create') }}" class="btn btn-primary btn-sm">＋ নতুন সোর্স যোগ করুন</a>
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
                        <th>সোর্সের নাম ও ফিড লিংক</th>
                        <th>টাইপ ও ভাষা</th>
                        <th>ডিফল্ট ক্যাটাগরি</th>
                        <th>সংগ্রহ ব্যবধান</th>
                        <th>সর্বশেষ সংগ্রহ</th>
                        <th>স্ট্যাটাস</th>
                        <th style="text-align: right;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sources as $source)
                        <tr>
                            <td>
                                <div style="font-weight: 700; font-size: 14px; color: #1e293b;">
                                    {{ $source->name }}
                                </div>
                                <div style="font-size: 11px; color: #64748b; font-family: monospace; margin-top: 3px; word-break: break-all;">
                                    {{ $source->feed_url }}
                                </div>
                                @if($source->last_error)
                                    <div style="color: #dc2626; font-size: 11px; margin-top: 3px;">
                                        ⚠ ত্রুটি: {{ Str::limit($source->last_error, 80) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span style="background: #e2e8f0; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase;">
                                    {{ $source->type }}
                                </span>
                                <span style="font-size: 12px; color: #64748b; margin-left: 4px;">({{ $source->language }})</span>
                            </td>
                            <td>
                                {{ $source->defaultCategory?->name ?? 'স্বয়ংক্রিয় ম্যাচিং' }}
                            </td>
                            <td>
                                {{ bn_num($source->fetch_interval_minutes) }} মিনিট
                            </td>
                            <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                {{ $source->last_fetched_at ? bn_date($source->last_fetched_at) : 'এখনো হয়নি' }}
                            </td>
                            <td>
                                @if($source->is_active)
                                    <span class="status-badge status-published">সক্রিয়</span>
                                @else
                                    <span class="status-badge status-draft">বন্ধ</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <form action="{{ route('admin.automation.sources.import-now', $source->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary" title="অন-ডিমান্ড ফিড সংগ্রহ">এখনই আনুন</button>
                                </form>
                                <a href="{{ route('admin.automation.sources.edit', $source->id) }}" class="btn btn-secondary btn-sm">সম্পাদনা</a>
                                <form action="{{ route('admin.automation.sources.toggle', $source->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm">
                                        {{ $source->is_active ? 'বন্ধ করুন' : 'চালু করুন' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.automation.sources.destroy', $source->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('আপনি কি নিশ্চিতভাবে এই সোর্সটি মুছে ফেলতে চান?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">&times;</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                এখনো কোনো নিউজ সোর্স যোগ করা হয়নি। উপরের "নতুন সোর্স যোগ করুন" বাটনে ক্লিক করুন।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($sources->hasPages())
    <div style="margin-top: 16px;">
        {{ $sources->links() }}
    </div>
@endif
@endsection
