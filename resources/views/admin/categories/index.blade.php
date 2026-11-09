@extends('layouts.admin')

@section('title', 'ক্যাটাগরি ব্যবস্থাপনা')

@section('content')
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 22px; font-weight: 700;">ক্যাটাগরি তালিকা</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <span>+ নতুন ক্যাটাগরি তৈরি করুন</span>
        </a>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ক্রম</th>
                            <th>ক্যাটাগরির নাম</th>
                            <th>স্লাগ</th>
                            <th>প্যারেন্ট</th>
                            <th>সংবাদ সংখ্যা</th>
                            <th>অবস্থা</th>
                            <th style="text-align: right;">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                            <tr>
                                <td>{{ bn_num($cat->sort_order) }}</td>
                                <td><strong>{{ $cat->name }}</strong></td>
                                <td><code>{{ $cat->slug }}</code></td>
                                <td>{{ $cat->parent ? $cat->parent->name : '-' }}</td>
                                <td>{{ bn_num($cat->news_count) }} টি</td>
                                <td>
                                    @if($cat->is_active)
                                        <span class="status-badge status-published">সক্রিয়</span>
                                    @else
                                        <span class="status-badge status-archived">নিষ্ক্রিয়</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-secondary btn-sm">সম্পাদনা</a>
                                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিতভাবে এই ক্যাটাগরিটি মুছে ফেলতে চান?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">মুছুন</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">কোনো ক্যাটাগরি পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="margin-top: 20px;">
        {{ $categories->links() }}
    </div>
</div>
@endsection

