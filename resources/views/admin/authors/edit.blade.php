@extends('layouts.admin')

@section('title', 'লেখক প্রোফাইল সম্পাদনা')

@section('content')
<div style="max-width: 650px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="font-size: 22px; font-weight: 700;">লেখক প্রোফাইল সম্পাদনা</h1>
        <a href="{{ route('admin.authors.index') }}" class="btn btn-secondary">&larr; লেখক তালিকা</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.authors.update', $author->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">লেখকের পুরো নাম *</label>
                    <input type="text" name="name" value="{{ old('name', $author->name) }}" required class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">ইউআরএল স্লাগ *</label>
                    <input type="text" name="slug" value="{{ old('slug', $author->slug) }}" required class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">পদবি / উপাধি</label>
                    <input type="text" name="designation" value="{{ old('designation', $author->designation) }}" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">সংযুক্ত ইউজার অ্যাকাউন্ট</label>
                    <select name="user_id" class="form-control">
                        <option value="">-- কোনো অ্যাকাউন্ট নেই (গেস্ট রাইটার) --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ $author->user_id == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">সংক্ষিপ্ত পরিচিতি / বায়ো</label>
                    <textarea name="bio" rows="4" class="form-control">{{ old('bio', $author->bio) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">অবস্থা *</label>
                    <select name="is_active" class="form-control">
                        <option value="1" {{ $author->is_active ? 'selected' : '' }}>সক্রিয় (Active)</option>
                        <option value="0" {{ !$author->is_active ? 'selected' : '' }}>নিষ্ক্রিয় (Inactive)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">পরিবর্তন সংরক্ষণ করুন</button>
            </form>
        </div>
    </div>
</div>
@endsection

