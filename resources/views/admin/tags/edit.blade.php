@extends('layouts.admin')

@section('title', 'ট্যাগ সম্পাদনা')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="font-size: 22px; font-weight: 700;">ট্যাগ সম্পাদনা</h1>
        <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">&larr; ট্যাগ তালিকা</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.tags.update', $tag->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">ট্যাগের নাম *</label>
                    <input type="text" name="name" value="{{ old('name', $tag->name) }}" required class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">ইউআরএল স্লাগ *</label>
                    <input type="text" name="slug" value="{{ old('slug', $tag->slug) }}" required class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">সংক্ষিপ্ত বিবরণ</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $tag->description) }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">প্রদর্শন ক্রম (Sort Order) *</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $tag->sort_order) }}" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">হোমপেজে ট্রেন্ডিং দেখাবে? *</label>
                        <select name="is_featured" class="form-control">
                            <option value="1" {{ $tag->is_featured ? 'selected' : '' }}>হ্যাঁ (Featured/Trending)</option>
                            <option value="0" {{ !$tag->is_featured ? 'selected' : '' }}>না (General)</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">পরিবর্তন সংরক্ষণ করুন</button>
            </form>
        </div>
    </div>
</div>
@endsection

