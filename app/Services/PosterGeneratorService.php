<?php

namespace App\Services;

use App\Models\News;
use App\Models\PosterTemplate;
use App\Models\GeneratedPoster;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use mirazmac\Unicode2Bijoy;
use Exception;

class PosterGeneratorService
{
    protected string $bengaliFontPath;
    protected string $englishFontPath;

    public function __construct()
    {
        $bundledSutonny = resource_path('fonts/SutonnyMJ-Bold.ttf');
        if (file_exists($bundledSutonny)) {
            $this->bengaliFontPath = $bundledSutonny;
        } elseif (file_exists('C:\\Windows\\Fonts\\SutonnyMJ-Bold.ttf')) {
            $this->bengaliFontPath = 'C:\\Windows\\Fonts\\SutonnyMJ-Bold.ttf';
        } else {
            $this->bengaliFontPath = resource_path('fonts/NotoSansBengali-Bold.ttf');
        }

        $bundledEnglish = resource_path('fonts/NotoSansBengali-Bold.ttf');
        if (file_exists($bundledEnglish)) {
            $this->englishFontPath = $bundledEnglish;
        } elseif (file_exists('C:\\Windows\\Fonts\\arialbd.ttf')) {
            $this->englishFontPath = 'C:\\Windows\\Fonts\\arialbd.ttf';
        } else {
            $this->englishFontPath = 'C:\\Windows\\Fonts\\arial.ttf';
        }
    }

    /**
     * Convert standard Unicode Bengali text into Bijoy ANSI for rendering with SutonnyMJ.
     * Prevents disconnected/broken Bengali conjuncts (যুক্তাক্ষর, ই-কার, এ-কার).
     */
    public function toBijoy(?string $str): string
    {
        if (empty($str)) {
            return '';
        }

        // 1. Handle ra + ya-phala variants (র‌্যা / র্র্যা / র‍্যা)
        $normalized = preg_replace('/র[\x{200B}-\x{200F}\x{FEFF}]?্[যয়]/u', 'i¨', $str);

        // 2. Strip all invisible zero-width and directional Unicode control characters
        $normalized = preg_replace('/[\x{200B}-\x{200F}\x{FEFF}]/u', '', $normalized);

        // 3. Normalize decomposed Bengali nuktas and dashes
        $normalized = str_replace(
            ['য' . "\u{09BC}", 'ড' . "\u{09BC}", 'ঢ' . "\u{09BC}", '—', '–'],
            ['য়', 'ড়', 'ঢ়', '-', '-'],
            $normalized
        );

        $converted = Unicode2Bijoy::convert($normalized);

        // 3. Fix SutonnyMJ glyphs for specific Bengali conjuncts (স্ত, ন্ত, ইত্যাদি)
        return str_replace(
            ['¯—', 'š—', '¯’', 'š’'],
            ['¯Í', 'šÍ', '¯’', 'š’'],
            $converted
        );
    }

    /**
     * Generate a social poster for a news article.
     *
     * @param News $news
     * @param string $dimensions 1080x1080 or 1200x630
     * @param string|null $customHeadline
     * @param PosterTemplate|null $template
     * @param User|null $user
     * @return GeneratedPoster
     */
    public function generate(
        News $news,
        string $dimensions = '1080x1080',
        ?string $customHeadline = null,
        ?PosterTemplate $template = null,
        ?User $user = null
    ): GeneratedPoster {
        [$targetWidth, $targetHeight] = $dimensions === '1200x630' ? [1200, 630] : [1080, 1080];
        $headline = $customHeadline ?: $news->title;

        // 1. Create truecolor canvas
        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);

        // 2. Default dark background
        $bgColor = imagecolorallocate($canvas, 7, 11, 20); // #070b14
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $bgColor);

        // 3. Load & Draw Featured Image
        $coverPath = $this->resolveCoverPath($news);
        if ($coverPath && file_exists($coverPath)) {
            $coverImage = $this->loadImage($coverPath);
            if ($coverImage) {
                $srcW = imagesx($coverImage);
                $srcH = imagesy($coverImage);

                // Image covers upper 92% in 1080x1080, or full background in 1200x630
                $imgAreaH = $dimensions === '1080x1080' ? (int) ($targetHeight * 0.92) : $targetHeight;

                $ratioSrc = $srcW / $srcH;
                $ratioDst = $targetWidth / $imgAreaH;

                if ($ratioSrc > $ratioDst) {
                    $cropW = (int) ($srcH * $ratioDst);
                    $cropH = $srcH;
                    $cropX = (int) (($srcW - $cropW) / 2);
                    $cropY = 0;
                } else {
                    $cropW = $srcW;
                    $cropH = (int) ($srcW / $ratioDst);
                    $cropX = 0;
                    $cropY = (int) (($srcH - $cropH) / 2);
                }

                imagecopyresampled($canvas, $coverImage, 0, 0, $cropX, $cropY, $targetWidth, $imgAreaH, $cropW, $cropH);
                imagedestroy($coverImage);
            }
        }

        // 4. Draw Gradient / Shading Overlay (smooth ease-in gradient)
        $this->drawGradientOverlay($canvas, $targetWidth, $targetHeight, $dimensions);

        // 5. Draw Header Branding (Logo / Portal Name & Date)
        $this->drawHeaderBranding($canvas, $targetWidth, $news);

        // 6 & 7. Draw Category Badge & Bengali Headline (Dynamic bottom-anchored placement)
        $this->drawCategoryBadgeAndHeadline($canvas, 60, $targetWidth, $targetHeight, $news, $headline, $dimensions);

        // 8. Draw Footer Branding Band
        $this->drawFooterBand($canvas, $targetWidth, $targetHeight, $news);

        // 9. Save to Storage
        $filename = 'posters/poster_' . $news->id . '_' . time() . '_' . Str::random(6) . '.jpg';
        Storage::disk('public')->makeDirectory('posters');
        $savePath = Storage::disk('public')->path($filename);

        imagejpeg($canvas, $savePath, 94);
        imagedestroy($canvas);

        $fileSize = file_exists($savePath) ? filesize($savePath) : 0;

        return GeneratedPoster::create([
            'news_id' => $news->id,
            'template_id' => $template?->id,
            'headline_text' => $headline,
            'file_path' => $filename,
            'dimensions' => $dimensions,
            'file_size_bytes' => $fileSize,
            'generated_by' => $user?->id ?? auth()->id(),
        ]);
    }

    /**
     * Draw smooth gradient overlay for readable text.
     */
    protected function drawGradientOverlay($canvas, int $w, int $h, string $dim): void
    {
        $startY = $dim === '1080x1080' ? (int) ($h * 0.52) : (int) ($h * 0.35);
        $totalSteps = $h - $startY;

        for ($y = $startY; $y < $h; $y++) {
            $progress = ($y - $startY) / $totalSteps;
            $alpha = (int) (127 - (127 * ($progress * $progress * 1.3)));
            $alpha = max(0, min(127, $alpha));
            $color = imagecolorallocatealpha($canvas, 7, 11, 20, $alpha);
            imageline($canvas, 0, $y, $w, $y, $color);
        }

        // Top subtle vignette for header branding contrast
        for ($y = 0; $y < 125; $y++) {
            $progress = 1 - ($y / 125);
            $alpha = (int) (127 - (127 * 0.65 * $progress));
            $color = imagecolorallocatealpha($canvas, 0, 0, 0, max(0, min(127, $alpha)));
            imageline($canvas, 0, $y, $w, $y, $color);
        }
    }

    /**
     * Draw Header Portal Logo & Timestamp with modern broadcast design.
     */
    protected function drawHeaderBranding($canvas, int $w, News $news): void
    {
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $red = imagecolorallocate($canvas, 220, 38, 38); // #dc2626
        $rose = imagecolorallocate($canvas, 244, 63, 94); // #f43f5e
        $subGray = imagecolorallocate($canvas, 203, 213, 225); // #cbd5e1

        $isSquare = $w === 1080;
        $topY = $isSquare ? 46 : 40;
        $iconH = $isSquare ? 32 : 30;

        // Red square icon
        imagefilledrectangle($canvas, 60, $topY, 60 + $iconH, $topY + $iconH, $red);
        imagettftext($canvas, $isSquare ? 19 : 18, 0, 68, $topY + ($isSquare ? 26 : 24), $white, $this->bengaliFontPath, $this->toBijoy("প্র"));

        // Portal brand name
        imagettftext($canvas, $isSquare ? 24 : 22, 0, 104, $topY + ($isSquare ? 26 : 24), $white, $this->bengaliFontPath, $this->toBijoy("প্রথম আলো"));

        // Subtle vertical separator
        $sepColor = imagecolorallocatealpha($canvas, 255, 255, 255, 80);
        $sepX = $isSquare ? 248 : 238;
        imageline($canvas, $sepX, $topY + 6, $sepX, $topY + $iconH - 6, $sepColor);

        // Edition subtitle
        imagettftext($canvas, $isSquare ? 13 : 12, 0, $sepX + 12, $topY + ($isSquare ? 23 : 22), $subGray, $this->bengaliFontPath, $this->toBijoy("অনলাইন সংস্করণ"));

        // Right side: Live red dot + Publication date
        $dateText = bn_date($news->published_at ?? now());
        $dateBijoy = $this->toBijoy($dateText);
        $bbox = imagettfbbox($isSquare ? 15 : 14, 0, $this->bengaliFontPath, $dateBijoy);
        $textW = abs($bbox[4] - $bbox[0]);
        $dateX = $w - 60 - $textW;

        imagefilledellipse($canvas, $dateX - 14, $topY + ($isSquare ? 18 : 17), $isSquare ? 10 : 9, $isSquare ? 10 : 9, $rose);
        imagettftext($canvas, $isSquare ? 15 : 14, 0, $dateX, $topY + ($isSquare ? 24 : 23), $subGray, $this->bengaliFontPath, $dateBijoy);
    }

    /**
     * Draw Category Badge & Bengali Headline bottom-anchored right above the footer line.
     * Leaves maximum vertical space for the hero photo.
     */
    protected function drawCategoryBadgeAndHeadline($canvas, int $x, int $w, int $h, News $news, string $headline, string $dimensions): void
    {
        $isSquare = $dimensions === '1080x1080';
        $fontSize = $isSquare ? 36 : 28;
        $lineHeight = $isSquare ? 52 : 42;
        $maxW = $w - ($isSquare ? 150 : 140);
        $accentBarW = 5;
        $textSpacing = $isSquare ? 16 : 14;
        $textStartX = $x + $accentBarW + $textSpacing;
        $availableW = $maxW - $accentBarW - $textSpacing;

        // Word wrap
        $words = preg_split('/\s+/u', $headline);
        $lines = [];
        $curLine = '';

        foreach ($words as $word) {
            $testLine = $curLine ? "{$curLine} {$word}" : $word;
            $testW = $this->measureMixedTextWidth($fontSize, $testLine);

            if ($testW > $availableW && !empty($curLine)) {
                $lines[] = $curLine;
                $curLine = $word;
            } else {
                $curLine = $testLine;
            }
        }
        if ($curLine) {
            $lines[] = $curLine;
        }

        $lines = array_slice($lines, 0, 3);
        if (count($words) > 16 && count($lines) === 3) {
            $lines[2] = rtrim($lines[2], '.') . '...';
        }
        $lineCount = count($lines);

        // Dynamic Bottom Anchor: Last line baseline rests right above the footer line
        $lastLineY = $isSquare ? ($h - 85) : ($h - 65);
        $headlineY = $lastLineY - (($lineCount - 1) * $lineHeight);

        // Precise top boundary of headline glyphs & Category badge positioning
        // 48px / 38px accounts for high vowels/matras/reph; badgeGap ensures generous separation
        $textTop = $headlineY - ($isSquare ? 48 : 38);
        $badgeGap = $isSquare ? 32 : 24;
        $badgeH = $isSquare ? 36 : 30;
        $badgeBottom = $textTop - $badgeGap;
        $badgeY = $badgeBottom - $badgeH;

        $categoryName = $news->primaryCategory?->name ?? 'জাতীয়';
        $this->drawCategoryBadge($canvas, $x, $badgeY, $categoryName, $dimensions, $badgeH);

        // Vertical crimson accent bar framing ONLY the headline text (clean gap below badge)
        $red = imagecolorallocate($canvas, 220, 38, 38);
        $accentTop = $textTop - 2;
        $accentBottom = $lastLineY + ($isSquare ? 8 : 6);
        imagefilledrectangle($canvas, $x, $accentTop, $x + $accentBarW, $accentBottom, $red);

        $white = imagecolorallocate($canvas, 255, 255, 255);
        $shadowColor = imagecolorallocatealpha($canvas, 0, 0, 0, 75);

        foreach ($lines as $i => $line) {
            $lineY = $headlineY + ($i * $lineHeight);
            // Subtle drop shadow
            $this->drawMixedText($canvas, $textStartX + 2, $lineY + 2, $fontSize, $shadowColor, $line);
            // Double strike for ultra-bold
            $this->drawMixedText($canvas, $textStartX + 1, $lineY, $fontSize, $white, $line);
            $this->drawMixedText($canvas, $textStartX, $lineY, $fontSize, $white, $line);
        }
    }

    /**
     * Draw Category Pill Badge.
     */
    protected function drawCategoryBadge($canvas, int $x, int $y, string $categoryName, string $dimensions = '1080x1080', int $badgeH = 36): void
    {
        $fontSize = $dimensions === '1080x1080' ? 14 : 13;
        $categoryBijoy = $this->toBijoy($categoryName);
        $bbox = imagettfbbox($fontSize, 0, $this->bengaliFontPath, $categoryBijoy);
        $textW = abs($bbox[4] - $bbox[0]);
        $badgeW = $textW + 32;

        $red = imagecolorallocate($canvas, 220, 38, 38); // #dc2626
        $white = imagecolorallocate($canvas, 255, 255, 255);

        // Badge rectangle
        imagefilledrectangle($canvas, $x, $y, $x + $badgeW, $y + $badgeH, $red);
        // Clean vertical centering
        $textY = $y + (int) round(($badgeH + $fontSize) / 2) - 2;
        imagettftext($canvas, $fontSize, 0, $x + 16, $textY, $white, $this->bengaliFontPath, $categoryBijoy);
    }

    /**
     * Draw mixed text (Bengali converted to Bijoy, English rendered in English font).
     */
    protected function drawMixedText($canvas, int $x, int $y, int $fontSize, int $color, string $text): int
    {
        $tokens = preg_split('/(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $curX = $x;

        foreach ($tokens as $token) {
            if ($token === '') continue;
            if (ctype_space($token)) {
                $bbox = imagettfbbox($fontSize, 0, $this->englishFontPath, ' ');
                $curX += abs($bbox[4] - $bbox[0]);
                continue;
            }

            // Pure English / ASCII token
            if (preg_match('/^[a-zA-Z0-9_\-\+\#\&\.\,\:\;\!\?\"\'\(\)\/]+$/', $token) && preg_match('/[a-zA-Z]/', $token)) {
                imagettftext($canvas, $fontSize, 0, $curX, $y, $color, $this->englishFontPath, $token);
                $bbox = imagettfbbox($fontSize, 0, $this->englishFontPath, $token);
                $curX += abs($bbox[4] - $bbox[0]);
            } else {
                $bijoyToken = $this->toBijoy($token);
                imagettftext($canvas, $fontSize, 0, $curX, $y, $color, $this->bengaliFontPath, $bijoyToken);
                $bbox = imagettfbbox($fontSize, 0, $this->bengaliFontPath, $bijoyToken);
                $curX += abs($bbox[4] - $bbox[0]);
            }
        }
        return $curX;
    }

    /**
     * Measure width of mixed text.
     */
    protected function measureMixedTextWidth(int $fontSize, string $text): int
    {
        $tokens = preg_split('/(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $totalW = 0;

        foreach ($tokens as $token) {
            if ($token === '') continue;
            if (ctype_space($token)) {
                $bbox = imagettfbbox($fontSize, 0, $this->englishFontPath, ' ');
                $totalW += abs($bbox[4] - $bbox[0]);
                continue;
            }
            if (preg_match('/^[a-zA-Z0-9_\-\+\#\&\.\,\:\;\!\?\"\'\(\)\/]+$/', $token) && preg_match('/[a-zA-Z]/', $token)) {
                $bbox = imagettfbbox($fontSize, 0, $this->englishFontPath, $token);
                $totalW += abs($bbox[4] - $bbox[0]);
            } else {
                $bijoyToken = $this->toBijoy($token);
                $bbox = imagettfbbox($fontSize, 0, $this->bengaliFontPath, $bijoyToken);
                $totalW += abs($bbox[4] - $bbox[0]);
            }
        }
        return $totalW;
    }

    /**
     * Draw modern footer branding bar with glowing divider and site branding.
     */
    protected function drawFooterBand($canvas, int $w, int $h, News $news): void
    {
        $isSquare = $w === 1080;
        $footerLineY = $h - ($isSquare ? 55 : 45);
        $red = imagecolorallocate($canvas, 220, 38, 38); // #dc2626
        $lineColor = imagecolorallocatealpha($canvas, 255, 255, 255, 80);

        // Glowing red accent bar at start of divider (60 to 180)
        imagefilledrectangle($canvas, 60, $footerLineY, 180, $footerLineY + 2, $red);
        // Translucent white hairline for remaining width
        imageline($canvas, 182, $footerLineY + 1, $w - 60, $footerLineY + 1, $lineColor);

        $cyan = imagecolorallocate($canvas, 56, 189, 248); // #38bdf8
        $footerTextY = $h - ($isSquare ? 24 : 18);

        // Small glowing cyan dot before domain
        imagefilledellipse($canvas, 66, $footerTextY - 5, 8, 8, $cyan);
        imagettftext($canvas, $isSquare ? 14 : 13, 0, 78, $footerTextY, $cyan, $this->englishFontPath, "htechprovision.com");

        // Reporter credit or attribution on right
        $credit = $news->authors->first()?->name ?? $news->source_name ?? 'অনলাইন ডেস্ক';
        $creditBijoy = $this->toBijoy("প্রতিবেদন: " . $credit);
        $bbox = imagettfbbox($isSquare ? 14 : 13, 0, $this->bengaliFontPath, $creditBijoy);
        $textW = abs($bbox[4] - $bbox[0]);
        $silver = imagecolorallocate($canvas, 203, 213, 225);

        imagettftext($canvas, $isSquare ? 14 : 13, 0, $w - 60 - $textW, $footerTextY, $silver, $this->bengaliFontPath, $creditBijoy);
    }

    /**
     * Resolve image file path on local filesystem.
     */
    protected function resolveCoverPath(News $news): ?string
    {
        if ($news->coverMedia) {
            $mediaPath = $news->coverMedia->path;
            if (file_exists($mediaPath)) {
                return $mediaPath;
            }
            if (file_exists(public_path($mediaPath))) {
                return public_path($mediaPath);
            }
            $cleanStoragePath = str_replace(['storage/', 'public/'], '', $mediaPath);
            if (file_exists(storage_path('app/public/' . $cleanStoragePath))) {
                return storage_path('app/public/' . $cleanStoragePath);
            }

            // Handle remote HTTP/HTTPS image URL (Google News / RSS imports)
            if (filter_var($mediaPath, FILTER_VALIDATE_URL)) {
                try {
                    $cacheDir = storage_path('app/public/feed_covers');
                    if (!is_dir($cacheDir)) {
                        @mkdir($cacheDir, 0755, true);
                    }
                    $cachedPath = $cacheDir . DIRECTORY_SEPARATOR . md5($mediaPath) . '.jpg';
                    if (file_exists($cachedPath) && filesize($cachedPath) > 0) {
                        return $cachedPath;
                    }

                    $response = \Illuminate\Support\Facades\Http::timeout(10)->get($mediaPath);
                    if ($response->successful() && !empty($response->body())) {
                        file_put_contents($cachedPath, $response->body());
                        return $cachedPath;
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Could not download remote cover image {$mediaPath}: " . $e->getMessage());
                }
            }
        }

        return null;
    }

    /**
     * Load image resource from file.
     */
    protected function loadImage(string $path)
    {
        $info = @getimagesize($path);
        if (!$info) return null;

        $mime = $info['mime'];
        return match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/webp' => @imagecreatefromwebp($path),
            default => null,
        };
    }
}
