<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>এইচ টেক প্রোভিশন আইটি - দৈনিক সংবাদ বুলেটিন</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 20px; color: #1e293b;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
        <div style="background: #c62828; padding: 24px 20px; text-align: center; color: #ffffff;">
            <h1 style="margin: 0; font-size: 22px; font-weight: 700;">এইচ টেক প্রোভিশন আইটি</h1>
            <p style="margin: 6px 0 0; font-size: 13px; opacity: 0.9;">দৈনিক সংবাদ বুলেটিন · {{ bn_date() }}</p>
        </div>

        <div style="padding: 24px 20px;">
            <p style="font-size: 14px; margin-top: 0; color: #475569;">প্রিয় পাঠক, আজকের নির্বাচিত ও গুরুত্বপূর্ণ সংবাদগুলো নিচে তুলে ধরা হলো:</p>

            <div style="margin-top: 20px;">
                @foreach($newsItems as $news)
                    <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 16px;">
                        @if($news->kicker)
                            <span style="color: #c62828; font-size: 12px; font-weight: 700; text-transform: uppercase;">{{ $news->kicker }}</span>
                        @endif
                        <h3 style="margin: 4px 0 8px; font-size: 16px; font-weight: 700;">
                            <a href="{{ $news->url }}" style="color: #0f172a; text-decoration: none;">{{ $news->title }}</a>
                        </h3>
                        <p style="margin: 0 0 10px; font-size: 13px; color: #64748b; line-height: 1.5;">
                            {{ $news->excerpt ?: Str::limit(strip_tags($news->body_html), 140) }}
                        </p>
                        <a href="{{ $news->url }}" style="color: #c62828; font-size: 13px; font-weight: 700; text-decoration: none;">সম্পূর্ণ খবর পড়ুন &rarr;</a>
                    </div>
                @endforeach
            </div>
        </div>

        <div style="background: #f1f5f9; padding: 16px 20px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0;">
            <p style="margin: 0 0 4px;">এইচ টেক প্রোভিশন আইটি নিউজ পোর্টাল © {{ date('Y') }} সর্বস্বত্ব সংরক্ষিত।</p>
            <p style="margin: 0;">আপনি আর ইমেইল পেতে না চাইলে <a href="{{ url('/newsletter/unsubscribe') }}" style="color: #64748b;">আনসাবস্ক্রাইব করুন</a></p>
        </div>
    </div>
</body>
</html>

