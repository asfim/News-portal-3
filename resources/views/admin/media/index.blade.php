@extends('layouts.admin')

@section('title', 'মিডিয়া লাইব্রেরি')

@section('content')
<div>
    <div class="admin-page-header">
        <h1 style="font-size: 22px; font-weight: 700;">মিডিয়া লাইব্রেরি</h1>
    </div>

    <!-- Upload Box -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3 class="card-title">নতুন মিডিয়া ফাইল আপলোড করুন</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="admin-upload-grid">
                @csrf
                <div>
                    <label class="form-label">ছবি নির্বাচন করুন (JPEG, PNG, WEBP, AVIF) *</label>
                    <input type="file" name="file" required accept="image/*" class="form-control">
                </div>
                <div>
                    <label class="form-label">ক্যাপশন (ঐচ্ছিক)</label>
                    <input type="text" name="caption" class="form-control" placeholder="ছবির বিবরণ">
                </div>
                <div>
                    <label class="form-label">ক্রেডিট (ঐচ্ছিক)</label>
                    <input type="text" name="credit" class="form-control" placeholder="ফটোগ্রাফারের নাম">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">আপলোড</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Media Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
        @forelse($mediaList as $media)
            <div class="card" style="overflow: hidden; margin-bottom: 0;">
                <div style="height: 140px; background: #eee; overflow: hidden; position: relative;">
                    <img src="{{ $media->path }}" alt="{{ $media->alt_text ?? 'image' }}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="padding: 12px; font-size: 12px;">
                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 600; margin-bottom: 4px;" title="{{ $media->original_name }}">
                        {{ $media->original_name }}
                    </div>
                    <div style="color: var(--text-muted); margin-bottom: 8px;">
                        {{ round($media->size_bytes / 1024) }} KB | {{ bn_date($media->created_at) }}
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="copyUrl('{{ $media->path }}')">লিংক কপি</button>
                        <form action="{{ route('admin.media.destroy', $media->id) }}" method="POST" onsubmit="return confirm('ছবিটি মুছে ফেলতে চান?');" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 8px;">&times;</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 40px;">কোনো মিডিয়া ফাইল এখনো আপলোড করা হয়নি।</p>
        @endforelse
    </div>

    <div style="margin-top: 24px;">
        {{ $mediaList->links() }}
    </div>
</div>

<script>
    function copyUrl(url) {
        navigator.clipboard.writeText(url).then(function() {
            alert('ছবির লিংক ক্লিপবোর্ডে কপি হয়েছে!');
        });
    }
</script>
@endsection

