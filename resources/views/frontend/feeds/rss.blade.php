{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>{{ $channelTitle }}</title>
    <link>{{ $channelLink }}</link>
    <description>{{ $channelDescription }}</description>
    <language>bn-BD</language>
    <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
    <atom:link href="{{ url('/rss') }}" rel="self" type="application/rss+xml" />

    @foreach($articles as $article)
      <item>
        <title><![CDATA[{{ $article->title }}]]></title>
        <link>{{ url("/news/{$article->public_id}/{$article->slug}") }}</link>
        <guid isPermaLink="true">{{ url("/news/{$article->public_id}/{$article->slug}") }}</guid>
        <description><![CDATA[{{ $article->excerpt ?? Str::limit(strip_tags($article->body_plain ?? $article->body_html), 200) }}]]></description>
        <pubDate>{{ $article->published_at ? $article->published_at->toRfc2822String() : now()->toRfc2822String() }}</pubDate>
        @if($article->primaryCategory)
          <category><![CDATA[{{ $article->primaryCategory->name }}]]></category>
        @endif
        @if($article->authors->isNotEmpty())
          <author><![CDATA[{{ $article->authors->first()->name }}]]></author>
        @endif
      </item>
    @endforeach
  </channel>
</rss>

