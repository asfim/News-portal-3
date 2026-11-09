@extends('layouts.frontend')

@section('title', ($page->seo_title ?? $page->title) . ' | প্রথম আলো')
@section('meta_description', $page->seo_description)

@section('content')
<div class="site-container" style="max-width: 900px; margin: 0 auto; padding: 30px 16px;">
    
    <h1 style="font-size: 32px; font-weight: 700; margin: 0 0 20px 0; border-bottom: 2px solid #d60000; padding-bottom: 12px; color: #111;">
        {{ $page->title }}
    </h1>

    <div class="page-content" style="font-size: 17px; line-height: 1.75; color: #333; margin-bottom: 40px;">
        {!! $page->body_html !!}
    </div>

    @if($page->slug === 'contact')
        <!-- Contact Form -->
        <div style="background: #fafafa; border: 1px solid #e0e0e0; border-radius: 6px; padding: 24px; margin-top: 30px;">
            <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 16px; color: #111;">আমাদের বার্তা পাঠান</h2>

            @if(session('success'))
                <div style="background: #e8f5e9; color: #2e7d32; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px;">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('contact.submit') }}" method="POST">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 14px; font-weight: 700; margin-bottom: 6px; color: #444;">আপনার নাম: *</label>
                    <input type="text" name="name" required style="width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; font-size: 15px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 14px; font-weight: 700; margin-bottom: 6px; color: #444;">ইমেইল ঠিকানা: *</label>
                    <input type="email" name="email" required style="width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; font-size: 15px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 14px; font-weight: 700; margin-bottom: 6px; color: #444;">বিষয়:</label>
                    <input type="text" name="subject" style="width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; font-size: 15px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 700; margin-bottom: 6px; color: #444;">বার্তা: *</label>
                    <textarea name="message" rows="5" required style="width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; font-size: 15px; box-sizing: border-box;"></textarea>
                </div>

                <button type="submit" style="background: #d60000; color: #fff; border: none; padding: 12px 28px; border-radius: 4px; font-weight: 700; font-size: 16px; cursor: pointer; font-family: inherit;">
                    বার্তা পাঠান
                </button>
            </form>
        </div>
    @endif

</div>
@endsection
