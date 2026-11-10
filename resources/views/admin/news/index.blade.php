@extends('layouts.admin')

@section('title', 'সংবাদ ব্যবস্থাপনা')

@section('content')
<div>
    <div class="admin-page-header">
        <h1 style="font-size: 22px; font-weight: 700;">সংবাদ তালিকা</h1>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
            <span>+ নতুন সংবাদ তৈরি করুন</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-body" style="padding: 16px;">
            <form action="{{ route('admin.news.index') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                <div style="flex: 1; min-width: 200px;">
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="শিরোনাম দিয়ে খুঁজুন...">
                </div>

                <div style="flex: 1; min-width: 140px;">
                    <select name="status" class="form-control">
                        <option value="">-- সকল অবস্থা --</option>
                        <option value="published" {{ $status === 'published' ? 'selected' : '' }}>প্রকাশিত</option>
                        <option value="review_pending" {{ $status === 'review_pending' ? 'selected' : '' }}>পর্যালোচনায়</option>
                        <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>খসড়া</option>
                        <option value="scheduled" {{ $status === 'scheduled' ? 'selected' : '' }}>নির্ধারিত</option>
                        <option value="archived" {{ $status === 'archived' ? 'selected' : '' }}>আর্কাইভ</option>
                    </select>
                </div>

                <div style="flex: 1; min-width: 140px;">
                    <select name="category_id" class="form-control">
                        <option value="">-- সকল ক্যাটাগরি --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-secondary">ফিল্টার</button>
                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary" style="background: transparent; border: 1px solid #d1d5db;">রিসেট</a>
            </form>
        </div>
    </div>

    <!-- News Table -->
    <div class="card">
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ছবি</th>
                            <th>শিরোনাম</th>
                            <th>ক্যাটাগরি</th>
                            <th>লেখক</th>
                            <th>অবস্থা</th>
                            <th>তারিখ</th>
                            <th style="text-align: right; width: 140px;">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($newsList as $news)
                            <tr>
                                <td>
                                    @if($news->cover_image_url)
                                        <img src="{{ $news->cover_image_url }}" alt="thumb" style="width: 48px; height: 34px; object-fit: cover; border-radius: 4px; display: block;">
                                    @else
                                        <div style="width: 48px; height: 34px; background: #eee; border-radius: 4px;"></div>
                                    @endif
                                </td>
                                <td>
                                    @if($news->kicker)
                                        <span style="font-size: 11px; color: var(--primary); font-weight: 700;">{{ $news->kicker }}</span><br>
                                    @endif
                                    <strong style="color: #111;">{{ $news->title }}</strong>
                                </td>
                                <td>{{ $news->primaryCategory ? $news->primaryCategory->name : '-' }}</td>
                                <td>{{ $news->authors->first()?->name ?? 'ডেস্ক' }}</td>
                                <td>
                                    <span class="status-badge status-{{ $news->status }}">
                                        @if($news->status === 'published') প্রকাশিত
                                        @elseif($news->status === 'draft') খসড়া
                                        @elseif($news->status === 'review_pending') পর্যালোচনায়
                                        @elseif($news->status === 'scheduled') নির্ধারিত
                                        @else আর্কাইভ @endif
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size: 13px;">{{ bn_date($news->published_at ?? $news->created_at) }}</span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                        <a href="{{ route('admin.news.edit', $news->id) }}" class="btn btn-secondary btn-sm">সম্পাদনা</a>
                                        <form action="{{ route('admin.news.destroy', $news->id) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিতভাবে এই সংবাদটি মুছে ফেলতে চান?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">মুছুন</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">কোনো সংবাদ পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="margin-top: 20px;">
        {{ $newsList->links() }}
    </div>
</div>
@endsection

