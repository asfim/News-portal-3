<?php

namespace App\Services;

use App\Models\News;
use App\Models\Category;
use App\Models\User;
use App\Models\ReadingHistory;
use App\Models\ReaderPreference;
use Illuminate\Support\Collection;

class NewsRecommendationService
{
    /**
     * Record an article read event and update category affinities.
     */
    public function recordReading(News $news, ?User $user, string $sessionId, int $readSeconds = 15): void
    {
        $catId = $news->primary_category_id;
        if (!$catId) return;

        // 1. Insert history record
        ReadingHistory::create([
            'user_id' => $user?->id,
            'session_id' => $sessionId,
            'news_id' => $news->id,
            'category_id' => $catId,
            'read_seconds' => $readSeconds,
            'created_at' => now(),
        ]);

        // 2. Update category affinity weight
        $preference = null;
        if ($user) {
            $preference = ReaderPreference::where('user_id', $user->id)->first();
        }
        if (!$preference) {
            $preference = ReaderPreference::where('session_id', $sessionId)->first();
        }

        $affinities = $preference ? ($preference->category_affinity ?? []) : [];
        $affinities[$catId] = ($affinities[$catId] ?? 0) + 1;

        if ($preference) {
            $preference->update([
                'category_affinity' => $affinities,
                'updated_at' => now(),
            ]);
        } else {
            ReaderPreference::create([
                'user_id' => $user?->id,
                'session_id' => $sessionId,
                'category_affinity' => $affinities,
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Get personalized news recommendations based on reader preferences.
     *
     * @param User|null $user
     * @param string|null $sessionId
     * @param int $limit
     * @return Collection
     */
    public function getRecommendations(?User $user = null, ?string $sessionId = null, int $limit = 4): Collection
    {
        $preference = null;
        if ($user) {
            $preference = ReaderPreference::where('user_id', $user->id)->first();
        }
        if (!$preference && $sessionId) {
            $preference = ReaderPreference::where('session_id', $sessionId)->first();
        }

        $affinities = $preference ? ($preference->category_affinity ?? []) : [];

        if (!empty($affinities)) {
            arsort($affinities);
            $topCategoryIds = array_keys(array_slice($affinities, 0, 3, true));

            $recommended = News::published()
                ->whereIn('primary_category_id', $topCategoryIds)
                ->with(['primaryCategory', 'coverMedia'])
                ->orderByDesc('published_at')
                ->take($limit)
                ->get();

            if ($recommended->count() >= $limit) {
                return $recommended;
            }

            // Fill remainder with most read or latest
            $missing = $limit - $recommended->count();
            $fillers = News::published()
                ->whereNotIn('id', $recommended->pluck('id'))
                ->with(['primaryCategory', 'coverMedia'])
                ->mostRead(7)
                ->take($missing)
                ->get();

            return $recommended->concat($fillers);
        }

        // Fallback for new visitors: Curated most read & trending stories
        return News::published()
            ->with(['primaryCategory', 'coverMedia'])
            ->mostRead(7)
            ->take($limit)
            ->get();
    }
}
