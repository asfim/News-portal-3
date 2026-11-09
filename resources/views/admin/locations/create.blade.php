@extends('layouts.admin')

@section('title', 'নতুন লোকেশন')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="font-size: 22px; font-weight: 700;">নতুন লোকেশন তৈরি</h1>
        <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">&larr; লোকেশন তালিকা</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.locations.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">লোকেশন বা এলাকার নাম *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-control" placeholder="যেমন: কক্সবাজার">
                </div>

                <div class="form-group">
                    <label class="form-label">ইউআরএল স্লাগ (ঐচ্ছিক)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="form-control" placeholder="যেমন: cox-bazar">
                </div>

                <div class="form-group">
                    <label class="form-label">ধরন *</label>
                    <select name="type" required class="form-control" onchange="toggleParent(this.value)">
                        <option value="district" selected>জেলা (District)</option>
                        <option value="division">বিভাগ (Division)</option>
                        <option value="upazila">উপজেলা (Upazila)</option>
                    </select>
                </div>

                <div class="form-group" id="parentGroup">
                    <label class="form-label">মূল বিভাগ (Parent Division)</label>
                    <select name="parent_id" class="form-control">
                        <option value="">-- বিভাগ নির্বাচন করুন --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->name }} বিভাগ</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">কোড (ঐচ্ছিক)</label>
                    <input type="text" name="code" value="{{ old('code') }}" class="form-control" placeholder="যেমন: BD-10">
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">লোকেশন সংরক্ষণ করুন</button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleParent(type) {
        var group = document.getElementById('parentGroup');
        if (type === 'division') {
            group.style.display = 'none';
        } else {
            group.style.display = 'block';
        }
    }
</script>
@endsection

