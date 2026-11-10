<?php

namespace App\Services;

use App\Models\News;
use App\Models\PosterTemplate;
use App\Models\GeneratedPoster;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Exception;

class PosterGeneratorService
{
    protected string $fontPath;

    public function __construct()
    {
        $bundledFont = resource_path('fonts/NotoSansBengali-Bold.ttf');
        if (file_exists($bundledFont)) {
            $this->fontPath = $bundledFont;
        } elseif (file_exists('C:\\Windows\\Fonts\\ARIALUNI.TTF')) {
            $this->fontPath = 'C:\\Windows\\Fonts\\ARIALUNI.TTF';
        } else {
            $this->fontPath = 'C:\\Windows\\Fonts\\arial.ttf';
        }
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
        $bgColor = imagecolorallocate($canvas, 15, 23, 42); // #0f172a
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $bgColor);

        // 3. Load & Draw Featured Image
        $coverLoaded = false;
        $coverPath = $this->resolveCoverPath($news);
        if ($coverPath && file_exists($coverPath)) {
            $coverImage = $this->loadImage($coverPath);
            if ($coverImage) {
                $srcW = imagesx($coverImage);
                $srcH = imagesy($coverImage);

                // Image covers upper 70% of canvas in 1080x1080, or full background in 1200x630
                $imgAreaH = $dimensions === '1080x1080' ? (int) ($targetHeight * 0.68) : $targetHeight;

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
                $coverLoaded = true;
            }
        }

        // 4. Draw Gradient / Shading Overlay
        $this->drawGradientOverlay($canvas, $targetWidth, $targetHeight, $dimensions);

        // 5. Draw Header Branding (Logo / Portal Name & Date)
        $this->drawHeaderBranding($canvas, $targetWidth, $news);

        // 6. Draw Category Badge
        $categoryName = $news->primaryCategory?->name ?? 'জাতীয়';
        $badgeY = $dimensions === '1080x1080' ? (int) ($targetHeight * 0.62) : (int) ($targetHeight * 0.48);
        $this->drawCategoryBadge($canvas, 60, $badgeY, $categoryName);

        // 7. Draw Bengali Headline
        $headlineY = $badgeY + 68;
        $this->drawHeadline($canvas, 60, $headlineY, $targetWidth - 120, $headline);

        // 8. Draw Footer Branding Band
        $this->drawFooterBand($canvas, $targetWidth, $targetHeight, $news);

        // 9. Save to Storage
        $filename = 'posters/poster_' . $news->id . '_' . time() . '_' . Str::random(6) . '.jpg';
        Storage::disk('public')->makeDirectory('posters');
        $savePath = Storage::disk('public')->path($filename);

        imagejpeg($canvas, $savePath, 92);
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
        $startY = $dim === '1080x1080' ? (int) ($h * 0.45) : (int) ($h * 0.35);
        $totalSteps = $h - $startY;

        for ($y = $startY; $y < $h; $y++) {
            $progress = ($y - $startY) / $totalSteps;
            // Ease-in quadratic curve
            $alpha = (int) (127 - (127 * ($progress * $progress)));
            $color = imagecolorallocatealpha($canvas, 15, 23, 42, max(0, min(127, $alpha)));
            imageline($canvas, 0, $y, $w, $y, $color);
        }

        // Top subtle vignette for header contrast
        for ($y = 0; $y < 120; $y++) {
            $progress = 1 - ($y / 120);
            $alpha = (int) (127 - (127 * 0.45 * $progress));
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
        $red = imagecolorallocate($canvas, 214, 0, 0); // #d60000

        // Red square icon
        imagefilledrectangle($canvas, 60, 48, 88, 76, $red);
        imagettftext($canvas, 16, 0, 68, 70, $white, $this->fontPath, "প্র");

        // Portal brand name
        imagettftext($canvas, 20, 0, 102, 70, $white, $this->fontPath, "প্রথম আলো");

        // Publication date on right
        $dateText = bn_date($news->published_at ?? now());
        $bbox = imagettfbbox(14, 0, $this->fontPath, $dateText);
        $textW = abs($bbox[4] - $bbox[0]);
        $lightGray = imagecolorallocate($canvas, 226, 232, 240);
        imagettftext($canvas, 14, 0, $w - 60 - $textW, 70, $lightGray, $this->fontPath, $dateText);
    }

    /**
     * Draw Category Pill Badge.
     */
    protected function drawCategoryBadge($canvas, int $x, int $y, string $categoryName): void
    {
        $fontSize = 15;
        $bbox = imagettfbbox($fontSize, 0, $this->fontPath, $categoryName);
        $textW = abs($bbox[4] - $bbox[0]);
        $badgeW = $textW + 28;
        $badgeH = 34;

        $red = imagecolorallocate($canvas, 198, 40, 40); // #c62828
        $white = imagecolorallocate($canvas, 255, 255, 255);

        // Badge rounded box
        imagefilledrectangle($canvas, $x, $y, $x + $badgeW, $y + $badgeH, $red);
        imagettftext($canvas, $fontSize, 0, $x + 14, $y + 24, $white, $this->fontPath, $categoryName);
    }

    /**
     * Draw multi-line wrapped Bengali headline with proper spacing.
     */
    protected function drawHeadline($canvas, int $x, int $y, int $maxW, string $headline): void
    {
        $fontSize = 32;
        $lineHeight = 52;
        $white = imagecolorallocate($canvas, 255, 255, 255);

        // Word wrap text
        $words = preg_split('/\s+/u', $headline);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine ? "{$currentLine} {$word}" : $word;
            $bbox = imagettfbbox($fontSize, 0, $this->fontPath, $testLine);
            $testW = abs($bbox[4] - $bbox[0]);

            if ($testW > $maxW && !empty($currentLine)) {
                $lines[] = $currentLine;
                $currentLine = $word;
            } else {
                $currentLine = $testLine;
            }
        }
        if ($currentLine) {
            $lines[] = $currentLine;
        }

        // Limit to max 3 lines to avoid clipping
        $lines = array_slice($lines, 0, 3);
        if (count($words) > 18 && count($lines) === 3) {
            $lines[2] = rtrim($lines[2], '.') . '...';
        }

        foreach ($lines as $i => $line) {
            imagettftext($canvas, $fontSize, 0, $x, $y + ($i * $lineHeight), $white, $this->fontPath, $line);
        }
    }

    /**
     * Draw footer branding bar.
     */
    protected function drawFooterBand($canvas, int $w, int $h, News $news): void
    {
        $lineColor = imagecolorallocatealpha($canvas, 255, 255, 255, 100);
        imageline($canvas, 60, $h - 60, $w - 60, $h - 60, $lineColor);

        $gray = imagecolorallocate($canvas, 148, 163, 184);
        $cyan = imagecolorallocate($canvas, 56, 189, 248);

        // Website URL on left
        imagettftext($canvas, 13, 0, 60, $h - 32, $cyan, $this->fontPath, "htechprovision.com");

        // Reporter credit or attribution on right
        $credit = $news->authors->first()?->name ?? $news->source_name ?? 'অনলাইন ডেস্ক';
        $creditText = "প্রতিবেদন: " . $credit;
        $bbox = imagettfbbox(13, 0, $this->fontPath, $creditText);
        $textW = abs($bbox[4] - $bbox[0]);

        imagettftext($canvas, 13, 0, $w - 60 - $textW, $h - 32, $gray, $this->fontPath, $creditText);
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
            if (file_exists(storage_path('app/public/' . str_replace(['storage/', 'public/'], '', $mediaPath)))) {
                return storage_path('app/public/' . str_replace(['storage/', 'public/'], '', $mediaPath));
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
