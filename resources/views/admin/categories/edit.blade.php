@extends('layouts.admin')

@section('title', 'ক্যাটাগরি সম্পাদনা')

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="font-size: 22px; font-weight: 700;">ক্যাটাগরি সম্পাদনা</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">&larr; ক্যাটাগরি তালিকা</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">ক্যাটাগরির নাম *</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">ইউআরএল স্লাগ *</label>
                    <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" required class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">প্যারেন্ট ক্যাটাগরি</label>
                    <select name="parent_id" class="form-control">
                        <option value="">-- কোনো প্যারেন্ট নেই (মূল ক্যাটাগরি) --</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ $category->parent_id == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">সংক্ষিপ্ত বিবরণ</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">প্রদর্শন ক্রম (Sort Order) *</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">অবস্থা *</label>
                        <select name="is_active" class="form-control">
                            <option value="1" {{ $category->is_active ? 'selected' : '' }}>সক্রিয় (Active)</option>
                            <option value="0" {{ !$category->is_active ? 'selected' : '' }}>নিষ্ক্রিয় (Inactive)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">এসইও মেটা শিরোনাম</label>
                    <input type="text" name="seo_title" value="{{ old('seo_title', $category->seo_title) }}" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">এসইও মেটা বিবরণ</label>
                    <input type="text" name="seo_description" value="{{ old('seo_description', $category->seo_description) }}" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">পরিবর্তন সংরক্ষণ করুন</button>
            </form>
        </div>
    </div>
</div>
@endsection

