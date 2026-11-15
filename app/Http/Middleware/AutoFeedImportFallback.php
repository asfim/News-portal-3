<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\NewsSource;
use App\Services\NewsImportService;

class AutoFeedImportFallback
{
    /**
     * Handle an incoming request.
     * When traffic visits the site, periodically fetch due feeds in background
     * without delaying the HTTP response to the visitor.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Run after the response has been sent to client (terminating hook)
        app()->terminating(function () use ($request) {
            // Ignore asset requests, API checks or static files
            if ($request->is('build/*', 'storage/*', 'favicon.ico', '*.png', '*.jpg', '*.css', '*.js')) {
                return;
            }

            // At most once every 10 minutes across the portal
            if (Cache::add('auto_feed_fallback_lock', 1, now()->addMinutes(10))) {
                try {
                    $dueSources = NewsSource::active()
                        ->where(function ($q) {
                            $q->whereNull('last_fetched_at')
                              ->orWhereRaw('TIMESTAMPDIFF(MINUTE, last_fetched_at, NOW()) >= fetch_interval_minutes');
                        })
                        ->get();

                    if ($dueSources->isNotEmpty()) {
                        $importService = app(NewsImportService::class);
                        foreach ($dueSources as $source) {
                            $importService->importFromSource($source, 20);
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('AutoFeedImportFallback error: ' . $e->getMessage());
                }
            }
        });

        return $response;
    }
}

