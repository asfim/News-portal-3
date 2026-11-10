@extends('layouts.admin')

@section('title', 'নতুন ট্যাগ')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div class="admin-page-header">
        <h1 style="font-size: 22px; font-weight: 700;">নতুন ট্যাগ তৈরি</h1>
        <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">&larr; ট্যাগ তালিকা</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.tags.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">ট্যাগের নাম *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-control" placeholder="যেমন: বিশ্বকাপ ক্রিকেট">
                </div>

                <div class="form-group">
                    <label class="form-label">ইউআরএল স্লাগ (ঐচ্ছিক)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="form-control" placeholder="যেমন: world-cup-cricket">
                </div>

                <div class="form-group">
                    <label class="form-label">সংক্ষিপ্ত বিবরণ</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">প্রদর্শন ক্রম (Sort Order) *</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 1) }}" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">হোমপেজে ট্রেন্ডিং দেখাবে? *</label>
                        <select name="is_featured" class="form-control">
                            <option value="1">হ্যাঁ (Featured/Trending)</option>
                            <option value="0" selected>না (General)</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">ট্যাগ সংরক্ষণ করুন</button>
            </form>
        </div>
    </div>
</div>
@endsection

