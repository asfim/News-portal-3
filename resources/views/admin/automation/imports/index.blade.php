@extends('layouts.admin')

@section('title', 'সংগৃহীত সংবাদ তালিকা')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">সংগৃহীত সংবাদসমূহ</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">অনুমোদিত ফিড ও এপিআই থেকে সংগৃহীত সংবাদ ও সম্পাদকীয় অনুমোদন</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.automation.duplicates.index') }}" class="btn btn-secondary btn-sm">ডুপ্লিকেট সংবাদ তালিকা →</a>
        <a href="{{ route('admin.automation.index') }}" class="btn btn-secondary btn-sm">অটোমেশন কন্ট্রোল সেন্টার</a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 14px 20px;">
        <form method="GET" action="{{ route('admin.automation.imports.index') }}" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <select name="source_id" class="form-control" onchange="this.form.submit()">
                    <option value="">সকল নিউজ সোর্স</option>
                    @foreach($sources as $s)
                        <option value="{{ $s->id }}" {{ $sourceId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="width: 180px;">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">খসড়া ও প্রকাশিত</option>
                    <option value="draft_created" {{ $status === 'draft_created' ? 'selected' : '' }}>খসড়া (Draft)</option>
                    <option value="published" {{ $status === 'published' ? 'selected' : '' }}>প্রকাশিত</option>
                </select>
            </div>

            @if($sourceId || $status)
                <a href="{{ route('admin.automation.imports.index') }}" class="btn btn-secondary btn-sm">ফিল্টার মুছুন</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>শিরোনাম ও সোর্স</th>
                        <th>ক্যাটাগরি</th>
                        <th>অবস্থা</th>
                        <th>প্রকাশের তারিখ</th>
                        <th>সংগ্রহের তারিখ</th>
                        <th style="text-align: right;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($imports as $imp)
                        <tr>
                            <td>
                                <div style="font-weight: 700; font-size: 14px; color: #1e293b; line-height: 1.4;">
                                    {{ $imp->title }}
                                </div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 4px; display: flex; gap: 8px;">
                                    <span style="font-weight: 600; color: #c62828;">{{ $imp->source?->name ?? 'সোর্স' }}</span>
                                    <span>•</span>
                                    <a href="{{ $imp->source_url }}" target="_blank" rel="noopener" style="color: #64748b; text-decoration: none;">মূল লিংক ↗</a>
                                </div>
                            </td>
                            <td>
                                {{ $imp->news?->primaryCategory?->name ?? '-' }}
                            </td>
                            <td>
                                @if($imp->status === 'draft_created')
                                    <span class="status-badge status-draft">খসড়া (ড্রাফট)</span>
                                @elseif($imp->status === 'published')
                                    <span class="status-badge status-published">প্রকাশিত</span>
                                @else
                                    <span class="status-badge status-archived">{{ $imp->status }}</span>
                                @endif
                            </td>
                            <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                {{ $imp->published_at ? bn_date($imp->published_at) : '-' }}
                            </td>
                            <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                {{ bn_date($imp->created_at) }}
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                @if($imp->news)
                                    <a href="{{ route('admin.news.edit', $imp->news->id) }}" class="btn btn-primary btn-sm">সম্পাদনা ও প্রকাশ</a>
                                    @if($imp->news->status === 'published')
                                        <a href="{{ route('news.show', [$imp->news->public_id, $imp->news->slug]) }}" target="_blank" class="btn btn-secondary btn-sm">লাইভ</a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                কোনো সংগৃহীত সংবাদ পাওয়া যায়নি।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($imports->hasPages())
    <div style="margin-top: 16px;">
        {{ $imports->links() }}
    </div>
@endif
@endsection
