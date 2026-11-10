@extends('layouts.admin')

@section('title', 'ট্যাগ ব্যবস্থাপনা')

@section('content')
<div>
    <div class="admin-page-header">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">ট্যাগ ব্যবস্থাপনা</h1>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">সংবাদের সম্পর্কিত বিষয়ভিত্তিক কি-ওয়ার্ড ও ট্রেন্ডিং টপিক তৈরি করুন</p>
        </div>
        <a href="{{ route('admin.tags.create') }}" class="btn btn-secondary">
            <span>+ বিস্তারিত ফর্ম</span>
        </a>
    </div>

    @if(session('success'))
        <div style="background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-grid-tag-layout">
        <!-- Left: Quick Tag Create Form -->
        <div class="card" style="position: sticky; top: 80px;">
            <div class="card-header" style="background: #f8fafc;">
                <h3 class="card-title" style="font-size: 15px;">নতুন ট্যাগ যোগ করুন</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tags.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">ট্যাগের নাম *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="form-control" placeholder="যেমন: চ্যাম্পিয়ন্স ট্রফি">
                        <span style="font-size: 11px; color: var(--text-muted); margin-top: 3px; display: block;">ওয়েবসাইটে পাঠকদের সামনে যেভাবে প্রদর্শিত হবে</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">ইউআরএল স্লাগ (ঐচ্ছিক)</label>
                        <input type="text" name="slug" value="{{ old('slug') }}" class="form-control" placeholder="যেমন: champions-trophy">
                        <span style="font-size: 11px; color: var(--text-muted); margin-top: 3px; display: block;">ফাঁকা রাখলে স্বয়ংক্রিয়ভাবে তৈরি হবে</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">বিবরণ (ঐচ্ছিক)</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="ট্যাগের সংক্ষিপ্ত বিবরণ...">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">হোমপেজে ট্রেন্ডিং দেখাবে?</label>
                        <select name="is_featured" class="form-control">
                            <option value="0" selected>না (সাধারণ ট্যাগ)</option>
                            <option value="1">হ্যাঁ (হোমপেজে ট্রেন্ডিং টপিক হিসেবে)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">প্রদর্শন ক্রম (Sort Order)</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 1) }}" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 10px;">
                        + নতুন ট্যাগ সংরক্ষণ করুন
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Tag List Table -->
        <div>
            <div class="card">
                <div class="card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ক্রম</th>
                                    <th>ট্যাগের নাম</th>
                                    <th>স্লাগ</th>
                                    <th>ফিচার্ড / ট্রেন্ডিং</th>
                                    <th>সংবাদ</th>
                                    <th style="text-align: right; width: 140px;">অ্যাকশন</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tags as $tag)
                                    <tr>
                                        <td>{{ bn_num($tag->sort_order) }}</td>
                                        <td>
                                            <strong style="color: var(--primary);">#{{ $tag->name }}</strong>
                                            @if($tag->description)
                                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">{{ Str::limit($tag->description, 40) }}</div>
                                            @endif
                                        </td>
                                        <td><code>{{ $tag->slug }}</code></td>
                                        <td>
                                            @if($tag->is_featured)
                                                <span class="status-badge status-published">ট্রেন্ডিংয়ে আছে</span>
                                            @else
                                                <span class="status-badge status-draft">সাধারণ</span>
                                            @endif
                                        </td>
                                        <td>{{ bn_num($tag->news_count) }} টি</td>
                                        <td style="text-align: right;">
                                            <a href="{{ route('admin.tags.edit', $tag->id) }}" class="btn btn-secondary btn-sm">সম্পাদনা</a>
                                            <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিতভাবে এই ট্যাগটি মুছে ফেলতে চান?');" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">মুছুন</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">কোনো ট্যাগ পাওয়া যায়নি। বাঁ পাশের ফর্ম দিয়ে প্রথম ট্যাগ যুক্ত করুন।</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div style="margin-top: 20px;">
                {{ $tags->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
