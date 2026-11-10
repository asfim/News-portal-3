<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\NewsController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\TagController;
use App\Http\Controllers\Frontend\AuthorController;
use App\Http\Controllers\Frontend\LocationController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\AuthController as FrontendAuthController;
use App\Http\Controllers\Frontend\BookmarkController;
use App\Http\Controllers\Frontend\FeedController;

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\LocationController as AdminLocationController;
use App\Http\Controllers\Admin\AuthorController as AdminAuthorController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\BreakingNewsController as AdminBreakingNewsController;
use App\Http\Controllers\Admin\AdvertisementController as AdminAdvertisementController;
use App\Http\Controllers\Admin\HomepageSectionController as AdminHomepageSectionController;
use App\Http\Controllers\Admin\AutomationDashboardController as AdminAutomationDashboardController;
use App\Http\Controllers\Admin\NewsSourceController as AdminNewsSourceController;
use App\Http\Controllers\Admin\NewsImportController as AdminNewsImportController;
use App\Http\Controllers\Admin\AIAssistantController as AdminAIAssistantController;
use App\Http\Controllers\Admin\PosterController as AdminPosterController;
use App\Http\Controllers\Admin\SocialAccountController as AdminSocialAccountController;
use App\Http\Controllers\Admin\SocialPostController as AdminSocialPostController;

/*
|--------------------------------------------------------------------------
| Public Frontend Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/latest', [NewsController::class, 'latest'])->name('news.latest');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/news/{public_id}/{slug?}', [NewsController::class, 'show'])->name('news.show');
Route::post('/news/{public_id}/comment', [NewsController::class, 'storeComment'])->name('news.comment.store');
Route::get('/topic/{slug}', [TagController::class, 'show'])->name('tag.show');
Route::get('/author/{slug}', [AuthorController::class, 'show'])->name('author.show');
Route::get('/location/{slug}', [LocationController::class, 'show'])->name('location.show');
Route::get('/api/locations/districts', [LocationController::class, 'getDistricts'])->name('api.locations.districts');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');
Route::post('/contact', [PageController::class, 'submitContact'])->name('contact.submit');

// Reader Authentication
Route::get('/login', [FrontendAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [FrontendAuthController::class, 'login'])->name('login.submit');
Route::get('/register', [FrontendAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [FrontendAuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [FrontendAuthController::class, 'logout'])->name('logout');

// Reader Bookmarks
Route::middleware('auth')->group(function () {
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/news/{public_id}/bookmark', [BookmarkController::class, 'toggle'])->name('news.bookmark.toggle');
});

// Feeds & SEO
Route::get('/rss', [FeedController::class, 'rss'])->name('feed.rss');
Route::get('/feed', [FeedController::class, 'rss']);
Route::get('/sitemap.xml', [FeedController::class, 'sitemap'])->name('feed.sitemap');

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Protected Staff Editorial CMS Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('staff')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        // News Editorial CMS
        Route::resource('news', AdminNewsController::class);

        // Taxonomies
        Route::resource('categories', AdminCategoryController::class);
        Route::post('tags/quick-store', [AdminTagController::class, 'quickStore'])->name('tags.quick-store');
        Route::resource('tags', AdminTagController::class);
        Route::resource('locations', AdminLocationController::class);
        Route::resource('authors', AdminAuthorController::class);

        // Media Library
        Route::get('/media', [AdminMediaController::class, 'index'])->name('media.index');
        Route::post('/media', [AdminMediaController::class, 'store'])->name('media.store');
        Route::post('/media/editor-upload', [AdminMediaController::class, 'editorUpload'])->name('media.editor-upload');
        Route::delete('/media/{media}', [AdminMediaController::class, 'destroy'])->name('media.destroy');

        // Comments Moderation
        Route::get('/comments', [AdminCommentController::class, 'index'])->name('comments.index');
        Route::post('/comments/{comment}/approve', [AdminCommentController::class, 'approve'])->name('comments.approve');
        Route::post('/comments/{comment}/reject', [AdminCommentController::class, 'reject'])->name('comments.reject');
        Route::delete('/comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');

        // Breaking News (Frontend Ticker binding)
        Route::get('/breaking-news', [AdminBreakingNewsController::class, 'index'])->name('breaking-news.index');
        Route::post('/breaking-news', [AdminBreakingNewsController::class, 'store'])->name('breaking-news.store');
        Route::post('/breaking-news/{item}/toggle', [AdminBreakingNewsController::class, 'toggle'])->name('breaking-news.toggle');
        Route::delete('/breaking-news/{item}', [AdminBreakingNewsController::class, 'destroy'])->name('breaking-news.destroy');

        // Advertisements & Banners (Frontend Ad Slots binding)
        Route::get('/advertisements', [AdminAdvertisementController::class, 'index'])->name('advertisements.index');
        Route::post('/advertisements', [AdminAdvertisementController::class, 'store'])->name('advertisements.store');
        Route::post('/advertisements/{advertisement}/toggle', [AdminAdvertisementController::class, 'toggle'])->name('advertisements.toggle');
        Route::delete('/advertisements/{advertisement}', [AdminAdvertisementController::class, 'destroy'])->name('advertisements.destroy');

        // Homepage Sections & Grid Config
        Route::get('/homepage-sections', [AdminHomepageSectionController::class, 'index'])->name('homepage-sections.index');
        Route::put('/homepage-sections/{section}', [AdminHomepageSectionController::class, 'update'])->name('homepage-sections.update');
        Route::post('/homepage-sections/{section}/toggle', [AdminHomepageSectionController::class, 'toggle'])->name('homepage-sections.toggle');

        // Module 01 & 11: News Automation & Sources
        Route::get('/automation', [AdminAutomationDashboardController::class, 'index'])->name('automation.index');
        Route::get('/automation/sources', [AdminNewsSourceController::class, 'index'])->name('automation.sources.index');
        Route::get('/automation/sources/create', [AdminNewsSourceController::class, 'create'])->name('automation.sources.create');
        Route::post('/automation/sources', [AdminNewsSourceController::class, 'store'])->name('automation.sources.store');
        Route::get('/automation/sources/{source}/edit', [AdminNewsSourceController::class, 'edit'])->name('automation.sources.edit');
        Route::put('/automation/sources/{source}', [AdminNewsSourceController::class, 'update'])->name('automation.sources.update');
        Route::post('/automation/sources/{source}/toggle', [AdminNewsSourceController::class, 'toggle'])->name('automation.sources.toggle');
        Route::post('/automation/sources/{source}/import-now', [AdminNewsSourceController::class, 'importNow'])->name('automation.sources.import-now');
        Route::delete('/automation/sources/{source}', [AdminNewsSourceController::class, 'destroy'])->name('automation.sources.destroy');

        Route::get('/automation/imports', [AdminNewsImportController::class, 'index'])->name('automation.imports.index');
        Route::get('/automation/duplicates', [AdminNewsImportController::class, 'duplicates'])->name('automation.duplicates.index');
        Route::get('/automation/logs', [AdminNewsImportController::class, 'logs'])->name('automation.logs.index');

        // Module 02: AI News Assistant
        Route::post('/ai/generate', [AdminAIAssistantController::class, 'generate'])->name('ai.generate');
        Route::get('/ai/settings', [AdminAIAssistantController::class, 'settings'])->name('ai.settings');
        Route::put('/ai/settings', [AdminAIAssistantController::class, 'updateSettings'])->name('ai.settings.update');
        Route::get('/ai/history', [AdminAIAssistantController::class, 'history'])->name('ai.history');

        // Module 04: News Poster Generator
        Route::get('/posters', [AdminPosterController::class, 'index'])->name('posters.index');
        Route::get('/posters/create', [AdminPosterController::class, 'create'])->name('posters.create');
        Route::post('/posters', [AdminPosterController::class, 'store'])->name('posters.store');
        Route::delete('/posters/{poster}', [AdminPosterController::class, 'destroy'])->name('posters.destroy');

        // Module 03: Automatic Facebook Page Publishing
        Route::get('/social/accounts', [AdminSocialAccountController::class, 'index'])->name('social.accounts.index');
        Route::get('/social/accounts/create', [AdminSocialAccountController::class, 'create'])->name('social.accounts.create');
        Route::post('/social/accounts', [AdminSocialAccountController::class, 'store'])->name('social.accounts.store');
        Route::get('/social/accounts/{account}/edit', [AdminSocialAccountController::class, 'edit'])->name('social.accounts.edit');
        Route::put('/social/accounts/{account}', [AdminSocialAccountController::class, 'update'])->name('social.accounts.update');
        Route::post('/social/accounts/{account}/toggle', [AdminSocialAccountController::class, 'toggle'])->name('social.accounts.toggle');
        Route::delete('/social/accounts/{account}', [AdminSocialAccountController::class, 'destroy'])->name('social.accounts.destroy');

        Route::get('/social/posts', [AdminSocialPostController::class, 'index'])->name('social.posts.index');
        Route::get('/social/posts/create', [AdminSocialPostController::class, 'create'])->name('social.posts.create');
        Route::post('/social/posts/publish', [AdminSocialPostController::class, 'publish'])->name('social.posts.publish');
    });
});
