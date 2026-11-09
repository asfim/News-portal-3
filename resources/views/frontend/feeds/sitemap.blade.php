{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <!-- Homepage -->
  <url>
    <loc>{{ url('/') }}</loc>
    <changefreq>always</changefreq>
    <priority>1.0</priority>
  </url>

  <!-- Latest News -->
  <url>
    <loc>{{ url('/latest') }}</loc>
    <changefreq>hourly</changefreq>
    <priority>0.9</priority>
  </url>

  <!-- Categories -->
  @foreach($categories as $cat)
    <url>
      <loc>{{ url('/category/' . $cat->slug) }}</loc>
      <changefreq>hourly</changefreq>
      <priority>0.8</priority>
    </url>
  @endforeach

  <!-- Published Articles -->
  @foreach($articles as $news)
    <url>
      <loc>{{ url("/news/{$news->public_id}/{$news->slug}") }}</loc>
      <lastmod>{{ ($news->content_updated_at ?? $news->published_at ?? $news->updated_at)->toAtomString() }}</lastmod>
      <changefreq>weekly</changefreq>
      <priority>0.8</priority>
    </url>
  @endforeach

  <!-- Pages -->
  @foreach($pages as $page)
    <url>
      <loc>{{ url('/page/' . $page->slug) }}</loc>
      <changefreq>monthly</changefreq>
      <priority>0.5</priority>
    </url>
  @endforeach
</urlset>

