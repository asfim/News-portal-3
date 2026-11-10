@extends('layouts.admin')

@section('title', 'ডুপ্লিকেট সংবাদ তালিকা')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">বাদ দেওয়া ডুপ্লিকেট সংবাদসমূহ</h1>
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">ইউআরএল হ্যাশ ও কনটেন্ট ফিঙ্গারপ্রিন্ট দ্বারা ফিল্টারকৃত ডুপ্লিকেট সংবাদের তালিকা</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.automation.imports.index') }}" class="btn btn-secondary btn-sm">&larr; সংগৃহীত সংবাদের তালিকা</a>
        <a href="{{ route('admin.automation.index') }}" class="btn btn-secondary btn-sm">অটোমেশন কন্ট্রোল সেন্টার</a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ডুপ্লিকেট শিরোনাম</th>
                        <th>সোর্স</th>
                        <th>ফিঙ্গারপ্রিন্ট / হ্যাশ</th>
                        <th>মূল সংবাদের সাথে মিল</th>
                        <th>তারিখ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($duplicates as $dup)
                        <tr>
                            <td>
                                <div style="font-weight: 600; font-size: 14px; color: #1e293b;">
                                    {{ $dup->title }}
                                </div>
                                <div style="font-size: 11px; margin-top: 3px;">
                                    <a href="{{ $dup->source_url }}" target="_blank" rel="noopener" style="color: #64748b; text-decoration: none;">{{ Str::limit($dup->source_url, 60) }} ↗</a>
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 13px; font-weight: 600; color: #475569;">{{ $dup->source?->name ?? 'সোর্স' }}</span>
                            </td>
                            <td>
                                <code style="font-size: 11px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">{{ substr($dup->content_fingerprint, 0, 16) }}...</code>
                            </td>
                            <td>
                                @if($dup->duplicateOfNews)
                                    <a href="{{ route('admin.news.edit', $dup->duplicateOfNews->id) }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                        {{ Str::limit($dup->duplicateOfNews->title, 40) }}
                                    </a>
                                @else
                                    <span style="color: #64748b;">পূর্ববর্তী বিদ্যমান সংবাদ</span>
                                @endif
                            </td>
                            <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                {{ bn_date($dup->created_at) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                কোনো ডুপ্লিকেট সংবাদ পাওয়া যায়নি।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($duplicates->hasPages())
    <div style="margin-top: 16px;">
        {{ $duplicates->links() }}
    </div>
@endif
@endsection
