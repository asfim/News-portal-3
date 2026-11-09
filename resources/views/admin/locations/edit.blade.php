@extends('layouts.admin')

@section('title', 'লোকেশন সম্পাদনা')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="font-size: 22px; font-weight: 700;">লোকেশন সম্পাদনা</h1>
        <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">&larr; লোকেশন তালিকা</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.locations.update', $location->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">লোকেশন বা এলাকার নাম *</label>
                    <input type="text" name="name" value="{{ old('name', $location->name) }}" required class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">ইউআরএল স্লাগ *</label>
                    <input type="text" name="slug" value="{{ old('slug', $location->slug) }}" required class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">ধরন *</label>
                    <select name="type" required class="form-control">
                        <option value="district" {{ $location->type === 'district' ? 'selected' : '' }}>জেলা (District)</option>
                        <option value="division" {{ $location->type === 'division' ? 'selected' : '' }}>বিভাগ (Division)</option>
                        <option value="upazila" {{ $location->type === 'upazila' ? 'selected' : '' }}>উপজেলা (Upazila)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">মূল বিভাগ (Parent Division)</label>
                    <select name="parent_id" class="form-control">
                        <option value="">-- কোনো প্যারেন্ট নেই --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ $location->parent_id == $div->id ? 'selected' : '' }}>
                                {{ $div->name }} বিভাগ
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">কোড</label>
                    <input type="text" name="code" value="{{ old('code', $location->code) }}" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">পরিবর্তন সংরক্ষণ করুন</button>
            </form>
        </div>
    </div>
</div>
@endsection

