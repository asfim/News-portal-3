@extends('layouts.admin')

@section('title', 'নতুন ক্যাটাগরি')

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="font-size: 22px; font-weight: 700;">নতুন ক্যাটাগরি তৈরি</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">&larr; ক্যাটাগরি তালিকা</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">ক্যাটাগরির নাম *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-control" placeholder="যেমন: বাংলাদেশ">
                </div>

                <div class="form-group">
                    <label class="form-label">ইউআরএল স্লাগ (ঐচ্ছিক)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="form-control" placeholder="যেমন: bangladesh (খালি রাখলে স্বয়ংক্রিয় তৈরি হবে)">
                </div>

                <div class="form-group">
                    <label class="form-label">প্যারেন্ট ক্যাটাগরি (সাব-ক্যাটাগরি করতে চাইলে)</label>
                    <select name="parent_id" class="form-control">
                        <option value="">-- কোনো প্যারেন্ট নেই (মূল ক্যাটাগরি) --</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
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
                        <label class="form-label">অবস্থা *</label>
                        <select name="is_active" class="form-control">
                            <option value="1">সক্রিয় (Active)</option>
                            <option value="0">নিষ্ক্রিয় (Inactive)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">এসইও মেটা শিরোনাম</label>
                    <input type="text" name="seo_title" value="{{ old('seo_title') }}" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">এসইও মেটা বিবরণ</label>
                    <input type="text" name="seo_description" value="{{ old('seo_description') }}" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">ক্যাটাগরি সংরক্ষণ করুন</button>
            </form>
        </div>
    </div>
</div>
@endsection

