@extends('layouts.admin')

@section('title', 'নতুন লেখক প্রোফাইল')

@section('content')
<div style="max-width: 650px; margin: 0 auto;">
    <div class="admin-page-header">
        <h1 style="font-size: 22px; font-weight: 700;">নতুন লেখক প্রোফাইল তৈরি</h1>
        <a href="{{ route('admin.authors.index') }}" class="btn btn-secondary">&larr; লেখক তালিকা</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.authors.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">লেখকের পুরো নাম *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-control" placeholder="যেমন: কামাল আহমেদ">
                </div>

                <div class="form-group">
                    <label class="form-label">ইউআরএল স্লাগ (ঐচ্ছিক)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="form-control" placeholder="যেমন: kamal-ahmed">
                </div>

                <div class="form-group">
                    <label class="form-label">পদবি / উপাধি</label>
                    <input type="text" name="designation" value="{{ old('designation') }}" class="form-control" placeholder="যেমন: বিশেষ প্রতিনিধি / কলামিস্ট">
                </div>

                <div class="form-group">
                    <label class="form-label">সংযুক্ত ইউজার অ্যাকাউন্ট (যদি স্টাফ সদস্য হন)</label>
                    <select name="user_id" class="form-control">
                        <option value="">-- কোনো অ্যাকাউন্ট নেই (গেস্ট রাইটার) --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">সংক্ষিপ্ত পরিচিতি / বায়ো</label>
                    <textarea name="bio" rows="4" class="form-control" placeholder="লেখকের অভিজ্ঞতা ও পরিচিতি...">{{ old('bio') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">অবস্থা *</label>
                    <select name="is_active" class="form-control">
                        <option value="1">সক্রিয় (Active)</option>
                        <option value="0">নিষ্ক্রিয় (Inactive)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">প্রোফাইল সংরক্ষণ করুন</button>
            </form>
        </div>
    </div>
</div>
@endsection

