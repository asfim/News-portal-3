@extends('layouts.admin')

@section('title', 'নতুন হোমপেজ সেকশন তৈরি')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="admin-page-header">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">নতুন হোমপেজ সেকশন তৈরি করুন</h1>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">হোমপেজে প্রদর্শনের জন্য নতুন সংবাদ ব্লক কনফিগার করুন</p>
        </div>
        <a href="{{ route('admin.homepage-sections.index') }}" class="btn btn-secondary">&larr; সেকশন তালিকায় ফিরে যান</a>
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
            <form action="{{ route('admin.homepage-sections.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">সেকশনের শিরোনাম (Title) *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="form-control" placeholder="যেমন: বিশেষ প্রতিবেদন / খেলাধুলা / বিনোদন">
                </div>

                <div class="form-group">
                    <label class="form-label">ইউনিক আইডি / Key (ইংরেজি ছোট হাতের অক্ষর, ঐচ্ছিক)</label>
                    <input type="text" name="key" value="{{ old('key') }}" class="form-control" placeholder="যেমন: special_reports (খালি রাখলে স্বয়ংক্রিয় তৈরি হবে)">
                </div>

                <div class="form-group">
                    <label class="form-label">লেআউট প্রিসেট (Layout Preset) *</label>
                    <select name="layout" required class="form-control">
                        @foreach($layouts as $key => $label)
                            <option value="{{ $key }}" {{ old('layout') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">সংবাদের উৎস (Source Type) *</label>
                        <select name="source_type" id="sourceTypeSelect" required class="form-control">
                            <option value="latest" {{ old('source_type') === 'latest' ? 'selected' : '' }}>সর্বশেষ প্রকাশিত (Latest)</option>
                            <option value="category" {{ old('source_type') === 'category' ? 'selected' : '' }}>নির্দিষ্ট ক্যাটাগরি (Category)</option>
                            <option value="popular" {{ old('source_type') === 'popular' ? 'selected' : '' }}>সর্বাধিক পঠিত (Most Read)</option>
                            <option value="manual" {{ old('source_type') === 'manual' ? 'selected' : '' }}>শুধুমাত্র ম্যানুয়াল পিন্ড নিউজ (Manual)</option>
                        </select>
                    </div>

                    <div class="form-group" id="categoryGroup">
                        <label class="form-label">নির্দিষ্ট ক্যাটাগরি (যদি ক্যাটাগরি উৎস হয়)</label>
                        <select name="category_id" class="form-control">
                            <option value="">-- ক্যাটাগরি নির্বাচন করুন --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">সংবাদ প্রদর্শন সীমা (Item Limit) *</label>
                        <input type="number" name="item_limit" value="{{ old('item_limit', 6) }}" min="1" max="50" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">প্রদর্শন ক্রম (Sort Order)</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 10) }}" min="0" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        হোমপেজে অবিলম্বে দৃশ্যমান রাখুন (Active)
                    </label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <a href="{{ route('admin.homepage-sections.index') }}" class="btn btn-secondary">বাতিল</a>
                    <button type="submit" class="btn btn-primary">সেকশন তৈরি করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

