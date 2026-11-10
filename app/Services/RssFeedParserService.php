<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

class RssFeedParserService
{
    protected int $timeout = 15;

    /**
     * Fetch and parse an RSS, Atom or JSON news feed safely.
     *
     * @param string $url
     * @return array
     * @throws Exception
     */
    public function fetch(string $url): array
    {
        $this->validateSafeUrl($url);

        $response = Http::timeout($this->timeout)
            ->retry(2, 200, throw: false)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 NewsBot/1.0',
                'Accept' => 'application/rss+xml, application/xml, text/xml, application/atom+xml, application/json, text/html;q=0.9',
            ])
            ->get($url);

        if (!$response->successful()) {
            throw new Exception("Feed request failed with HTTP status {$response->status()}: " . Str::limit($response->body(), 100));
        }

        $body = trim($response->body());
        if (empty($body)) {
            throw new Exception("Feed response body is empty.");
        }

        // Detect JSON Feed
        if (str_starts_with($body, '{') || str_starts_with($body, '[')) {
            $data = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($data['items'])) {
                return $this->parseJsonFeed($data);
            }
        }

        // XML parser (RSS 2.0 / Atom)
        return $this->parseXmlFeed($body);
    }

    /**
     * SSRF URL Protection: Blocks internal IPs and localhost.
     */
    protected function validateSafeUrl(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception("Invalid feed URL format.");
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            throw new Exception("Unable to extract host from URL.");
        }

        if (in_array(strtolower($host), ['localhost', '127.0.0.1', '::1', '0.0.0.0'])) {
            throw new Exception("SSRF protection: Requests to localhost are prohibited.");
        }

        $ip = gethostbyname($host);
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            // Internal private IP range detected
            if ($ip !== $host && !app()->environment('testing', 'local')) {
                throw new Exception("SSRF protection: Private network IP addresses are prohibited.");
            }
        }
    }

    /**
     * Parse XML Feed (RSS 2.0 or Atom)
     */
    protected function parseXmlFeed(string $xmlString): array
    {
        // Suppress libxml errors and load safely
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new Exception("XML parsing failed: " . ($errors[0]->message ?? 'Unknown format'));
        }

        $items = [];

        // RSS 2.0 (<channel><item>)
        if (isset($xml->channel->item)) {
            foreach ($xml->channel->item as $entry) {
                $items[] = $this->parseRssItem($entry);
            }
        }
        // Atom (<feed><entry>)
        elseif (isset($xml->entry)) {
            foreach ($xml->entry as $entry) {
                $items[] = $this->parseAtomEntry($entry);
            }
        }
        // Fallback: direct item children
        elseif (isset($xml->item)) {
            foreach ($xml->item as $entry) {
                $items[] = $this->parseRssItem($entry);
            }
        }

        return $items;
    }

    /**
     * Parse standard RSS 2.0 item.
     */
    protected function parseRssItem(\SimpleXMLElement $item): array
    {
        $namespaces = $item->getNamespaces(true);
        $mediaNs = $namespaces['media'] ?? null;
        $contentNs = $namespaces['content'] ?? null;
        $dcNs = $namespaces['dc'] ?? null;

        $title = trim((string) $item->title);
        $link = trim((string) $item->link);
        $guid = trim((string) ($item->guid ?? $link));
        
        // Excerpt or Description
        $summary = '';
        if (isset($item->description)) {
            $summary = trim(strip_tags((string) $item->description));
        }

        // Full content or content:encoded
        $bodyHtml = '';
        if ($contentNs && isset($item->children($contentNs)->encoded)) {
            $bodyHtml = (string) $item->children($contentNs)->encoded;
        } elseif (isset($item->description)) {
            $bodyHtml = (string) $item->description;
        }

        // Publication date
        $pubDate = null;
        if (isset($item->pubDate)) {
            try {
                $pubDate = Carbon::parse((string) $item->pubDate)->setTimezone('UTC');
            } catch (\Throwable $e) {}
        } elseif ($dcNs && isset($item->children($dcNs)->date)) {
            try {
                $pubDate = Carbon::parse((string) $item->children($dcNs)->date)->setTimezone('UTC');
            } catch (\Throwable $e) {}
        }

        // Featured Image Extraction
        $imageUrl = null;
        // 1. Check enclosure
        if (isset($item->enclosure) && isset($item->enclosure['url'])) {
            $type = (string) ($item->enclosure['type'] ?? '');
            if (empty($type) || str_contains($type, 'image')) {
                $imageUrl = (string) $item->enclosure['url'];
            }
        }
        // 2. Check media:content
        if (!$imageUrl && $mediaNs) {
            $media = $item->children($mediaNs);
            if (isset($media->content) && isset($media->content['url'])) {
                $imageUrl = (string) $media->content['url'];
            } elseif (isset($media->thumbnail) && isset($media->thumbnail['url'])) {
                $imageUrl = (string) $media->thumbnail['url'];
            }
        }
        // 3. Fallback: Parse <img> tag from HTML body
        if (!$imageUrl && !empty($bodyHtml)) {
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $bodyHtml, $matches)) {
                $imageUrl = $matches[1];
            }
        }

        // Category tags
        $categories = [];
        if (isset($item->category)) {
            foreach ($item->category as $cat) {
                $c = trim((string) $cat);
                if (!empty($c)) {
                    $categories[] = $c;
                }
            }
        }

        return [
            'guid' => $guid ?: $link,
            'title' => $title,
            'source_url' => $this->normalizeUrl($link),
            'summary' => Str::limit($summary, 500),
            'body_html' => $this->sanitizeImportedHtml($bodyHtml),
            'image_url' => $imageUrl,
            'published_at' => $pubDate ?? now(),
            'categories' => $categories,
            'raw_payload' => [
                'guid' => $guid,
                'title' => $title,
                'link' => $link,
            ],
        ];
    }

    /**
     * Parse Atom feed entry.
     */
    protected function parseAtomEntry(\SimpleXMLElement $entry): array
    {
        $title = trim((string) $entry->title);
        $link = '';
        if (isset($entry->link)) {
            foreach ($entry->link as $l) {
                $rel = (string) ($l['rel'] ?? 'alternate');
                if ($rel === 'alternate' || empty($link)) {
                    $link = (string) $l['href'];
                }
            }
        }

        $guid = trim((string) ($entry->id ?? $link));
        $summary = isset($entry->summary) ? trim(strip_tags((string) $entry->summary)) : '';
        $bodyHtml = isset($entry->content) ? (string) $entry->content : '';

        $pubDate = null;
        if (isset($entry->published)) {
            try { $pubDate = Carbon::parse((string) $entry->published)->setTimezone('UTC'); } catch (\Throwable $e) {}
        } elseif (isset($entry->updated)) {
            try { $pubDate = Carbon::parse((string) $entry->updated)->setTimezone('UTC'); } catch (\Throwable $e) {}
        }

        $imageUrl = null;
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $bodyHtml, $matches)) {
            $imageUrl = $matches[1];
        }

        return [
            'guid' => $guid ?: $link,
            'title' => $title,
            'source_url' => $this->normalizeUrl($link),
            'summary' => Str::limit($summary, 500),
            'body_html' => $this->sanitizeImportedHtml($bodyHtml),
            'image_url' => $imageUrl,
            'published_at' => $pubDate ?? now(),
            'categories' => [],
            'raw_payload' => [
                'guid' => $guid,
                'title' => $title,
                'link' => $link,
            ],
        ];
    }

    /**
     * Parse JSON Feed 1.1 format.
     */
    protected function parseJsonFeed(array $json): array
    {
        $items = [];
        foreach ($json['items'] as $item) {
            $title = trim($item['title'] ?? '');
            $link = trim($item['url'] ?? $item['id'] ?? '');
            $guid = trim($item['id'] ?? $link);
            $summary = trim($item['summary'] ?? strip_tags($item['content_html'] ?? ''));
            $bodyHtml = $item['content_html'] ?? $item['content_text'] ?? '';
            $imageUrl = $item['image'] ?? $item['banner_image'] ?? null;

            $pubDate = null;
            if (!empty($item['date_published'])) {
                try { $pubDate = Carbon::parse($item['date_published'])->setTimezone('UTC'); } catch (\Throwable $e) {}
            }

            $items[] = [
                'guid' => $guid ?: $link,
                'title' => $title,
                'source_url' => $this->normalizeUrl($link),
                'summary' => Str::limit($summary, 500),
                'body_html' => $this->sanitizeImportedHtml($bodyHtml),
                'image_url' => $imageUrl,
                'published_at' => $pubDate ?? now(),
                'categories' => $item['tags'] ?? [],
                'raw_payload' => $item,
            ];
        }

        return $items;
    }

    /**
     * Normalize URL by stripping tracking query params like utm_source, fbclid, etc.
     */
    public function normalizeUrl(string $url): string
    {
        $parsed = parse_url(trim($url));
        if (!$parsed || empty($parsed['host'])) {
            return trim($url);
        }

        $scheme = strtolower($parsed['scheme'] ?? 'http');
        $host = strtolower($parsed['host']);
        $path = $parsed['path'] ?? '/';

        $query = '';
        if (!empty($parsed['query'])) {
            parse_str($parsed['query'], $queryParams);
            // Strip marketing trackers
            $trackingParams = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'fbclid', 'gclid', 'ref'];
            foreach ($trackingParams as $param) {
                unset($queryParams[$param]);
            }
            if (!empty($queryParams)) {
                ksort($queryParams);
                $query = '?' . http_build_query($queryParams);
            }
        }

        return "{$scheme}://{$host}{$path}{$query}";
    }

    /**
     * Sanitize imported HTML to preserve editorial integrity and safety.
     */
    protected function sanitizeImportedHtml(string $html): string
    {
        if (empty($html)) return '';

        // Strip scripts, iframes, styles
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        $html = preg_replace('/<iframe\b[^>]*>(.*?)<\/iframe>/is', '', $html);
        $html = preg_replace('/\s*on\w+\s*=\s*(["\']).*?\1/i', '', $html);

        return trim($html);
    }
}
