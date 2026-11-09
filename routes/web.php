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
    });
});
