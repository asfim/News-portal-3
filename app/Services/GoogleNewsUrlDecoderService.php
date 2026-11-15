<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleNewsUrlDecoderService
{
    protected string $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36';

    /**
     * Decode a Google News RSS/redirect URL into the original publisher article URL.
     *
     * @param string $googleNewsUrl
     * @return string|null
     */
    public function decode(string $googleNewsUrl): ?string
    {
        try {
            $parsed = parse_url($googleNewsUrl);
            $path = $parsed['path'] ?? '';
            $parts = explode('/', trim($path, '/'));
            $artId = end($parts);

            if (empty($artId) || !str_starts_with($artId, 'CBM')) {
                return $googleNewsUrl;
            }

            // Step 1: Fetch metadata page to get cryptographic signature & timestamp
            $metaUrl = "https://news.google.com/articles/{$artId}";
            $metaResponse = Http::timeout(6)
                ->withHeaders([
                    'User-Agent' => $this->userAgent,
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->get($metaUrl);

            if (!$metaResponse->successful()) {
                return null;
            }

            $html = $metaResponse->body();
            preg_match('/data-n-a-sg="([^"]+)"/', $html, $mSg);
            preg_match('/data-n-a-ts="([^"]+)"/', $html, $mTs);

            $sg = $mSg[1] ?? '';
            $ts = $mTs[1] ?? '';

            if (empty($sg) || empty($ts)) {
                return null;
            }

            // Step 2: Query Google's internal batchexecute RPC endpoint
            $req = [
                "Fbv4je",
                json_encode([
                    "garturlreq",
                    [
                        ["X", "X", ["X", "X"], null, null, 1, 1, "US:en", null, 1, null, null, null, null, null, 0, 1],
                        "X",
                        "X",
                        1,
                        [1, 1, 1],
                        1,
                        1,
                        null,
                        0,
                        0,
                        null,
                        0
                    ],
                    $artId,
                    $ts,
                    $sg
                ])
            ];

            $payload = "f.req=" . urlencode(json_encode([[$req]]));

            $rpcResponse = Http::timeout(6)
                ->asForm()
                ->withHeaders([
                    'User-Agent' => $this->userAgent,
                    'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
                ])
                ->post("https://news.google.com/_/DotsSplashUi/data/batchexecute", [
                    'f.req' => json_encode([[$req]])
                ]);

            if (!$rpcResponse->successful()) {
                return null;
            }

            $rpcBody = $rpcResponse->body();

            // Find non-Google publisher URLs from the RPC response
            if (preg_match_all('/"(https?:\/\/(?!news\.google|www\.google|policies\.google)[^"\\\\]+)/i', $rpcBody, $matches)) {
                $candidates = $matches[1];
                foreach ($candidates as $cand) {
                    if (filter_var($cand, FILTER_VALIDATE_URL) && !str_ends_with($cand, '/amp')) {
                        return stripslashes($cand);
                    }
                }
                return stripslashes($candidates[0] ?? null);
            }

            return null;
        } catch (\Throwable $e) {
            Log::warning("GoogleNewsUrlDecoder error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Resolve the authentic news photo (og:image / twitter:image) from a publisher article URL.
     *
     * @param string $sourceUrl
     * @return string|null
     */
    public function resolveRealPhoto(string $sourceUrl): ?string
    {
        $realArticleUrl = $sourceUrl;

        // If it's a Google News link, resolve to original publisher site first
        if (str_contains($sourceUrl, 'news.google.com')) {
            $decoded = $this->decode($sourceUrl);
            if ($decoded) {
                $realArticleUrl = $decoded;
            }
        }

        try {
            $response = Http::timeout(6)
                ->withHeaders([
                    'User-Agent' => $this->userAgent,
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                ])
                ->get($realArticleUrl);

            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();
            $photo = null;

            if (preg_match('/<meta[^>]+(?:property|name)=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
                $photo = $m[1];
            } elseif (preg_match('/content=["\']([^"\']+)["\'][^>]+(?:property|name)=["\']og:image["\']/i', $html, $m)) {
                $photo = $m[1];
            } elseif (preg_match('/<meta[^>]+(?:property|name)=["\']twitter:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
                $photo = $m[1];
            }

            if ($photo) {
                $photo = html_entity_decode($photo);
                // Reject generic Google logos or icons
                if (str_contains($photo, 'googleusercontent.com') && str_contains($photo, '=s0-w300')) {
                    return null;
                }
                return $photo;
            }

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}

