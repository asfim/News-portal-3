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

        // Normalize decomposed Bengali nuktas and unicode dashes
        $normalized = str_replace(
            ['য' . "\u{09BC}", 'ড' . "\u{09BC}", 'ঢ' . "\u{09BC}", '—', '–'],
            ['য়', 'ড়', 'ঢ়', '-', '-'],
            $str
        );

        $converted = Unicode2Bijoy::convert($normalized);

        // Fix SutonnyMJ glyphs for specific Bengali conjuncts (স্ত, ন্ত, ইত্যাদি)
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
        $bgColor = imagecolorallocate($canvas, 11, 17, 32); // #0b1120
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $bgColor);

        // 3. Load & Draw Featured Image
        $coverPath = $this->resolveCoverPath($news);
        if ($coverPath && file_exists($coverPath)) {
            $coverImage = $this->loadImage($coverPath);
            if ($coverImage) {
                $srcW = imagesx($coverImage);
                $srcH = imagesy($coverImage);

                // Image covers upper 76% in 1080x1080, or full background in 1200x630
                $imgAreaH = $dimensions === '1080x1080' ? (int) ($targetHeight * 0.76) : $targetHeight;

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

        // 6. Draw Category Badge (Positioned lower down to keep photo visible)
        $categoryName = $news->primaryCategory?->name ?? 'জাতীয়';
        $badgeY = $dimensions === '1080x1080' ? (int) ($targetHeight * 0.73) : (int) ($targetHeight * 0.67);
        $this->drawCategoryBadge($canvas, 60, $badgeY, $categoryName, $dimensions);

        // 7. Draw Bengali Headline (Bold, positioned lower down, unbroken text)
        $headlineY = $badgeY + ($dimensions === '1080x1080' ? 64 : 50);
        $this->drawHeadline($canvas, 60, $headlineY, $targetWidth - 120, $headline, $dimensions);

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
        $startY = $dim === '1080x1080' ? (int) ($h * 0.40) : (int) ($h * 0.28);
        $totalSteps = $h - $startY;

        for ($y = $startY; $y < $h; $y++) {
            $progress = ($y - $startY) / $totalSteps;
            // Ease-in quadratic curve for deep solid bottom
            $alpha = (int) (127 - (127 * ($progress * $progress)));
            $color = imagecolorallocatealpha($canvas, 11, 17, 32, max(0, min(127, $alpha)));
            imageline($canvas, 0, $y, $w, $y, $color);
        }

        // Top subtle vignette for header branding contrast
        for ($y = 0; $y < 120; $y++) {
            $progress = 1 - ($y / 120);
            $alpha = (int) (127 - (127 * 0.5 * $progress));
            $color = imagecolorallocatealpha($canvas, 0, 0, 0, max(0, min(127, $alpha)));
            imageline($canvas, 0, $y, $w, $y, $color);
        }
    }

    /**
     * Draw Header Portal Logo & Timestamp.
     */
    protected function drawHeaderBranding($canvas, int $w, News $news): void
    {
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $red = imagecolorallocate($canvas, 220, 38, 38); // #dc2626

        // Red square icon
        imagefilledrectangle($canvas, 60, 48, 88, 76, $red);
        imagettftext($canvas, 18, 0, 68, 70, $white, $this->bengaliFontPath, $this->toBijoy("প্র"));

        // Portal brand name
        imagettftext($canvas, 22, 0, 102, 70, $white, $this->bengaliFontPath, $this->toBijoy("প্রথম আলো"));

        // Publication date on right
        $dateText = bn_date($news->published_at ?? now());
        $dateBijoy = $this->toBijoy($dateText);
        $bbox = imagettfbbox(15, 0, $this->bengaliFontPath, $dateBijoy);
        $textW = abs($bbox[4] - $bbox[0]);
        $lightGray = imagecolorallocate($canvas, 226, 232, 240);
        imagettftext($canvas, 15, 0, $w - 60 - $textW, 70, $lightGray, $this->bengaliFontPath, $dateBijoy);
    }

    /**
     * Draw Category Pill Badge.
     */
    protected function drawCategoryBadge($canvas, int $x, int $y, string $categoryName, string $dimensions = '1080x1080'): void
    {
        $fontSize = $dimensions === '1080x1080' ? 16 : 14;
        $categoryBijoy = $this->toBijoy($categoryName);
        $bbox = imagettfbbox($fontSize, 0, $this->bengaliFontPath, $categoryBijoy);
        $textW = abs($bbox[4] - $bbox[0]);
        $badgeW = $textW + 36;
        $badgeH = $dimensions === '1080x1080' ? 42 : 36;

        $red = imagecolorallocate($canvas, 220, 38, 38); // #dc2626
        $white = imagecolorallocate($canvas, 255, 255, 255);

        // Badge rectangle
        imagefilledrectangle($canvas, $x, $y, $x + $badgeW, $y + $badgeH, $red);
        imagettftext($canvas, $fontSize, 0, $x + 18, $y + ($dimensions === '1080x1080' ? 29 : 25), $white, $this->bengaliFontPath, $categoryBijoy);
    }

    /**
     * Draw multi-line wrapped Bengali headline with proper bold typography and mixed language support.
     */
    protected function drawHeadline($canvas, int $x, int $y, int $maxW, string $headline, string $dimensions): void
    {
        $fontSize = $dimensions === '1080x1080' ? 38 : 30;
        $lineHeight = $dimensions === '1080x1080' ? 58 : 46;
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $shadowColor = imagecolorallocatealpha($canvas, 0, 0, 0, 70);

        // Word wrap text by measuring width
        $words = preg_split('/\s+/u', $headline);
        $lines = [];
        $curLine = '';

        foreach ($words as $word) {
            $testLine = $curLine ? "{$curLine} {$word}" : $word;
            $testW = $this->measureMixedTextWidth($fontSize, $testLine);

            if ($testW > $maxW && !empty($curLine)) {
                $lines[] = $curLine;
                $curLine = $word;
            } else {
                $curLine = $testLine;
            }
        }
        if ($curLine) {
            $lines[] = $curLine;
        }

        // Limit to max 3 lines to avoid clipping
        $lines = array_slice($lines, 0, 3);
        if (count($words) > 16 && count($lines) === 3) {
            $lines[2] = rtrim($lines[2], '.') . '...';
        }

        foreach ($lines as $i => $line) {
            $lineY = $y + ($i * $lineHeight);
            // Draw subtle shadow for crisp contrast
            $this->drawMixedText($canvas, $x + 2, $lineY + 2, $fontSize, $shadowColor, $line);
            // Draw bold white text with 1px horizontal strike for extra heavyweight boldness
            $this->drawMixedText($canvas, $x + 1, $lineY, $fontSize, $white, $line);
            $this->drawMixedText($canvas, $x, $lineY, $fontSize, $white, $line);
        }
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
     * Draw footer branding bar.
     */
    protected function drawFooterBand($canvas, int $w, int $h, News $news): void
    {
        $lineColor = imagecolorallocatealpha($canvas, 255, 255, 255, 100);
        $footerLineY = $h - ($w === 1080 ? 55 : 45);
        imageline($canvas, 60, $footerLineY, $w - 60, $footerLineY, $lineColor);

        $cyan = imagecolorallocate($canvas, 56, 189, 248);
        $footerTextY = $h - ($w === 1080 ? 25 : 18);

        // Website URL on left
        imagettftext($canvas, 14, 0, 60, $footerTextY, $cyan, $this->englishFontPath, "htechprovision.com");

        // Reporter credit or attribution on right
        $credit = $news->authors->first()?->name ?? $news->source_name ?? 'অনলাইন ডেস্ক';
        $creditBijoy = $this->toBijoy("প্রতিবেদন: " . $credit);
        $bbox = imagettfbbox(14, 0, $this->bengaliFontPath, $creditBijoy);
        $textW = abs($bbox[4] - $bbox[0]);
        $gray = imagecolorallocate($canvas, 148, 163, 184);

        imagettftext($canvas, 14, 0, $w - 60 - $textW, $footerTextY, $gray, $this->bengaliFontPath, $creditBijoy);
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
