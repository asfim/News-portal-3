@extends('layouts.admin')

@section('title', 'হোমপেজ সেকশন সম্পাদনা ও পিন্ড নিউজ')

@section('content')
<div style="max-width: 1050px; margin: 0 auto;">
    <div class="admin-page-header">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">'{{ $section->title }}' সেকশন সম্পাদনা</h1>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">সেকশনের সেটিংস পরিবর্তন এবং ম্যানুয়ালি নির্দিষ্ট সংবাদ অগ্রাধিকার অনুযায়ী পিন করুন</p>
        </div>
        <a href="{{ route('admin.homepage-sections.index') }}" class="btn btn-secondary">&larr; সেকশন তালিকায় ফিরে যান</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="admin-grid-2col" style="grid-template-columns: 1.2fr 1fr; gap: 24px;">
        <!-- Left: Section Settings Form -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">সেকশন কনফিগারেশন</h3>
                <span class="status-badge {{ $section->is_active ? 'status-published' : 'status-draft' }}">
                    {{ $section->is_active ? 'সক্রিয়' : 'লুকানো' }}
                </span>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.homepage-sections.update', $section->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label">সেকশনের শিরোনাম (Title) *</label>
                        <input type="text" name="title" value="{{ old('title', $section->title) }}" required class="form-control">
                        <small style="color: #94a3b8; font-family: monospace;">Key: {{ $section->key }}</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">লেআউট প্রিসেট (Layout Preset) *</label>
                        <select name="layout" required class="form-control">
                            @foreach($layouts as $key => $label)
                                <option value="{{ $key }}" {{ old('layout', $section->layout) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">সংবাদের উৎস (Source Type) *</label>
                            <select name="source_type" required class="form-control">
                                <option value="latest" {{ old('source_type', $section->source_type) === 'latest' ? 'selected' : '' }}>সর্বশেষ (Latest)</option>
                                <option value="category" {{ old('source_type', $section->source_type) === 'category' ? 'selected' : '' }}>নির্দিষ্ট ক্যাটাগরি (Category)</option>
                                <option value="popular" {{ old('source_type', $section->source_type) === 'popular' ? 'selected' : '' }}>সর্বাধিক পঠিত (Most Read)</option>
                                <option value="manual" {{ old('source_type', $section->source_type) === 'manual' ? 'selected' : '' }}>শুধুমাত্র পিন্ড নিউজ (Manual)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">নির্দিষ্ট ক্যাটাগরি</label>
                            <select name="category_id" class="form-control">
                                <option value="">-- কোনোটি নয় --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $section->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">সংবাদ সীমা (Item Limit) *</label>
                            <input type="number" name="item_limit" value="{{ old('item_limit', $section->item_limit) }}" min="1" max="50" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label class="form-label">প্রদর্শন ক্রম (Sort Order) *</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', $section->sort_order) }}" min="0" required class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $section->is_active) ? 'checked' : '' }}>
                            হোমপেজে অবিলম্বে দৃশ্যমান রাখুন
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">সেকশন সেটিংস সংরক্ষণ করুন</button>
                </form>
            </div>
        </div>

        <!-- Right: Pinned News Management -->
        <div>
            <!-- Add Pinned News Box -->
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <h3 class="card-title">📌 ম্যানুয়াল সংবাদ পিন করুন</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.homepage-sections.items.store', $section->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">সংবাদ নির্বাচন করুন *</label>
                            <select name="news_id" required class="form-control" style="font-size: 13px;">
                                <option value="">-- সংবাদ নির্বাচন করুন --</option>
                                @foreach($recentNews as $rn)
                                    <option value="{{ $rn->id }}">[{{ $rn->primaryCategory?->name ?? 'জাতীয়' }}] {{ Str::limit($rn->title, 60) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label class="form-label">পজিশন</label>
                                <input type="number" name="position" value="{{ $section->items->count() + 1 }}" min="1" class="form-control" style="font-size: 13px;">
                            </div>
                            <div class="form-group">
                                <label class="form-label">শুরু সময়</label>
                                <input type="datetime-local" name="starts_at" class="form-control" style="font-size: 12px;">
                            </div>
                            <div class="form-group">
                                <label class="form-label">শেষ সময়</label>
                                <input type="datetime-local" name="ends_at" class="form-control" style="font-size: 12px;">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-sm" style="width: 100%; justify-content: center;">+ সেকশনে পিন করুন</button>
                    </form>
                </div>
            </div>

            <!-- List of Pinned News -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">বর্তমান পিন্ড সংবাদ ({{ bn_num($section->items->count()) }}টি)</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">ক্রম</th>
                                    <th>সংবাদ</th>
                                    <th style="text-align: right;">অ্যাকশন</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($section->items as $item)
                                    <tr>
                                        <td><strong>{{ bn_num($item->position) }}</strong></td>
                                        <td>
                                            <div style="font-weight: 600; font-size: 13px;">{{ $item->news?->title ?? 'মুছে ফেলা সংবাদ' }}</div>
                                            <div style="font-size: 11px; color: #94a3b8;">
                                                ক্যাটাগরি: {{ $item->news?->primaryCategory?->name ?? '-' }}
                                                @if($item->starts_at) · শুরু: {{ $item->starts_at->format('d/m/Y') }} @endif
                                                @if($item->ends_at) · শেষ: {{ $item->ends_at->format('d/m/Y') }} @endif
                                            </div>
                                        </td>
                                        <td style="text-align: right;">
                                            <form action="{{ route('admin.homepage-sections.items.destroy', [$section->id, $item->id]) }}" method="POST" onsubmit="return confirm('পিন মুছে ফেলতে চান?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" style="padding: 3px 8px; font-size: 11px;">আনপিন</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="text-align: center; padding: 20px; color: #94a3b8; font-size: 13px;">
                                            এই সেকশনে কোনো সংবাদ পিন করা নেই। স্বয়ংক্রিয়ভাবে সংবাদের উৎস থেকে সংবাদ লোড হবে।
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

