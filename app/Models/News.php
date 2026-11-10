<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class News extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'public_id',
        'slug',
        'title',
        'kicker',
        'subtitle',
        'excerpt',
        'body_html',
        'body_plain',
        'type',
        'status',
        'primary_category_id',
        'location_id',
        'cover_media_id',
        'cover_alt',
        'cover_caption',
        'cover_credit',
        'source_name',
        'source_url',
        'created_by',
        'updated_by',
        'approved_by',
        'published_by',
        'scheduled_at',
        'published_at',
        'content_updated_at',
        'comments_enabled',
        'seo_title',
        'seo_description',
        'og_media_id',
        'lock_version',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'content_updated_at' => 'datetime',
        'comments_enabled' => 'boolean',
        'lock_version' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($news) {
            if (empty($news->public_id)) {
                $news->public_id = (string) Str::ulid();
            }
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title) . '-' . substr($news->public_id, -6);
            }
        });
    }

    /**
     * Centralized Published Eligibility Scope for all public surfaces.
     * status = published, published_at <= now, deleted_at is null
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope to attach total views aggregated from daily stats.
     */
    public function scopeWithTotalViews($query, ?int $days = null)
    {
        $statSubQuery = NewsDailyStat::selectRaw('COALESCE(SUM(views), 0)')
            ->whereColumn('news_daily_stats.news_id', 'news.id');

        if ($days !== null && $days > 0) {
            $statSubQuery->where('stat_date', '>=', now()->subDays($days)->toDateString());
        }

        return $query->select('news.*')
            ->selectSub($statSubQuery, 'total_views');
    }

    /**
     * Scope to order news by most read views in the specified window.
     */
    public function scopeMostRead($query, ?int $days = 7)
    {
        return $query->withTotalViews($days)
            ->orderByDesc('total_views')
            ->orderByDesc('published_at');
    }

    public function primaryCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'primary_category_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'news_category');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'news_tag');
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(AuthorProfile::class, 'news_author')
            ->withPivot('sort_order', 'role')
            ->orderByPivot('sort_order');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function coverMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    public function ogMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'og_media_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function relatedNews(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_related', 'news_id', 'related_news_id')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(NewsRevision::class)->orderByDesc('version');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(NewsStatusHistory::class)->orderByDesc('created_at');
    }

    public function corrections(): HasMany
    {
        return $this->hasMany(NewsCorrection::class)->orderByDesc('corrected_at');
    }

    public function video(): HasOne
    {
        return $this->hasOne(NewsVideo::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('status', 'approved');
    }

    public function dailyStats(): HasMany
    {
        return $this->hasMany(NewsDailyStat::class);
    }

    public function getUrlAttribute(): string
    {
        return url("/news/{$this->public_id}/{$this->slug}");
    }

    public function getCoverUrlAttribute(): ?string
    {
        if ($this->coverMedia) {
            return $this->coverMedia->url;
        }

        return null;
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_url;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at && $this->published_at->isPast();
    }

    public function getTotalViewsAttribute(): int
    {
        if (array_key_exists('total_views', $this->attributes)) {
            return (int) $this->attributes['total_views'];
        }

        return (int) $this->dailyStats()->sum('views');
    }

    public function getUniqueViewsAttribute(): int
    {
        return (int) $this->dailyStats()->sum('unique_views');
    }
}

