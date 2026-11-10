@extends('layouts.admin')

@section('title', 'লেখক ও রিপোর্টার ব্যবস্থাপনা')

@section('content')
<div>
    <div class="admin-page-header">
        <h1 style="font-size: 22px; font-weight: 700;">লেখক ও রিপোর্টার তালিকা</h1>
        <a href="{{ route('admin.authors.create') }}" class="btn btn-primary">
            <span>+ নতুন লেখক প্রোফাইল তৈরি</span>
        </a>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>নাম</th>
                            <th>পদবি / ডেজিগনেশন</th>
                            <th>স্লাগ</th>
                            <th>ইউজার অ্যাকাউন্ট</th>
                            <th>মোট সংবাদ</th>
                            <th>অবস্থা</th>
                            <th style="text-align: right;">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($authors as $author)
                            <tr>
                                <td><strong>{{ $author->name }}</strong></td>
                                <td>{{ $author->designation ?? '-' }}</td>
                                <td><code>{{ $author->slug }}</code></td>
                                <td>{{ $author->user ? $author->user->email : 'গেস্ট রাইটার' }}</td>
                                <td>{{ bn_num($author->news_count) }} টি</td>
                                <td>
                                    @if($author->is_active)
                                        <span class="status-badge status-published">সক্রিয়</span>
                                    @else
                                        <span class="status-badge status-archived">নিষ্ক্রিয়</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('admin.authors.edit', $author->id) }}" class="btn btn-secondary btn-sm">সম্পাদনা</a>
                                    <form action="{{ route('admin.authors.destroy', $author->id) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিতভাবে এই লেখক প্রোফাইলটি মুছে ফেলতে চান?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">মুছুন</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">কোনো লেখক প্রোফাইল পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="margin-top: 20px;">
        {{ $authors->links() }}
    </div>
</div>
@endsection

