<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CreatorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Admin\DashboardController as AdminDash;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use App\Http\Controllers\Admin\CreatorController as AdminCreator;
use App\Http\Controllers\Admin\CompanyController as AdminCompany;
use App\Http\Controllers\Admin\ServiceController as AdminService;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\PayoutController as AdminPayout;
use App\Http\Controllers\Admin\SettingController as AdminSetting;
use App\Http\Controllers\Admin\BlogController as AdminBlog;
use App\Http\Controllers\Admin\FaqController as AdminFaq;

/*
|--------------------------------------------------------------------------
| QuickContent — India's First Quick Content Delivery Platform
| Hostinger Shared Ready: file/database, Tailwind CDN, no Redis
|--------------------------------------------------------------------------
*/

// ── SEO: sitemap & robots ──
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// ── Landing — clear positioning + JSON-LD FAQ/Blog ──
Route::get('/', [LandingController::class, 'index'])->name('landing');

// ── Onboarding — beautiful 3-step (business & creator) — easy like ordering food ──
Route::get('/onboarding/business', [OnboardingController::class, 'business'])->name('onboarding.business');
Route::post('/onboarding/business', [OnboardingController::class, 'storeBusiness'])->name('onboarding.business.store');
Route::get('/onboarding/creator', [OnboardingController::class, 'creator'])->name('onboarding.creator');
Route::post('/onboarding/creator', [OnboardingController::class, 'storeCreator'])->name('onboarding.creator.store');
Route::get('/onboarding', fn()=> redirect()->route('onboarding.business'))->name('onboarding');

// ── Blog (SEO/AEO) — public ──
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// ── Auth (file session, no Redis) ──
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Business — marketplace + PROFILE (real DB) ──
Route::get('/business', [BusinessController::class, 'home'])->name('business.home');
Route::get('/app', [BusinessController::class, 'home'])->name('app');
Route::post('/business/switch', [BusinessController::class, 'switch'])->name('business.switch');
Route::get('/business/profile', [BusinessController::class, 'profile'])->name('business.profile');
Route::post('/business/profile', [BusinessController::class, 'updateProfile'])->name('business.profile.update');

// Services
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

// Orders — file queue, no Redis
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::post('/teams', [OrderController::class, 'storeTeam'])->name('teams.store');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
Route::post('/orders/{order}/message', [OrderController::class, 'message'])->name('orders.message');

// Creator — dashboard + PROFILE + portfolio (real DB)
Route::get('/creator', [CreatorController::class, 'dashboard'])->name('creator.dashboard');
Route::get('/creator/profile', [CreatorController::class, 'profile'])->name('creator.profile');
Route::post('/creator/profile', [CreatorController::class, 'updateProfile'])->name('creator.profile.update');
Route::post('/creator/availability', [CreatorController::class, 'toggleAvailability'])->name('creator.availability');
Route::post('/creator/portfolio', [CreatorController::class, 'storePortfolio'])->name('creator.portfolio.store');
Route::delete('/creator/portfolio/{id}', [CreatorController::class, 'destroyPortfolio'])->name('creator.portfolio.destroy');
Route::get('/creator/orders/{order}', [CreatorController::class, 'order'])->name('creator.order');
Route::post('/creator/orders/{order}/deliver', [CreatorController::class, 'deliver'])->name('creator.deliver');
// Public creator profile (SEO Person JSON-LD) — numeric only so /creator/profile stays safe
Route::get('/creator/{id}', function($id){ $c=\App\Models\Creator::findOrFail($id); return view('creator.public', compact('c')); })->where('id','[0-9]+')->name('creator.public');

// Health
Route::get('/health', fn() => response()->json(['status'=>'ok','app'=>"QuickContent — India's First Quick Content Delivery",'host'=>'hostinger-shared-ready']));

// ── Admin — roles: super_admin, admin, manager, support, finance ──
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('/', [AdminDash::class, 'index'])->name('dashboard');

        Route::get('/orders', [AdminOrder::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [AdminOrder::class, 'show'])->name('orders.show');
        Route::post('/orders/{id}/status', [AdminOrder::class, 'updateStatus'])->middleware('role:super_admin,admin,manager')->name('orders.status');
        Route::post('/orders/{id}/assign', [AdminOrder::class, 'assign'])->middleware('role:super_admin,admin,manager')->name('orders.assign');
        Route::post('/orders/{id}/release', [AdminOrder::class, 'releaseEscrow'])->middleware('role:super_admin,admin,finance')->name('orders.release');

        Route::get('/creators', [AdminCreator::class, 'index'])->name('creators.index');
        Route::get('/creators/{id}', [AdminCreator::class, 'show'])->name('creators.show');
        Route::post('/creators/{id}/verify', [AdminCreator::class, 'toggleVerify'])->middleware('role:super_admin,admin,manager')->name('creators.verify');
        Route::post('/creators/{id}/availability', [AdminCreator::class, 'toggleAvailability'])->middleware('role:super_admin,admin,manager')->name('creators.availability');
        Route::post('/creators/{id}/featured', [AdminCreator::class, 'toggleFeatured'])->middleware('role:super_admin,admin')->name('creators.featured');
        Route::post('/creators/{id}/profile-type', [AdminCreator::class, 'updateProfileType'])->middleware('role:super_admin,admin,manager')->name('creators.profileType');
        Route::delete('/creators/{id}', [AdminCreator::class, 'destroy'])->middleware('role:super_admin,admin')->name('creators.destroy');

        Route::get('/companies', [AdminCompany::class, 'index'])->name('companies.index');

        // Services — dynamic, includes UGC & Barter, managed by category + profile type
        Route::get('/services', [AdminService::class, 'index'])->name('services.index');
        Route::get('/services/create', [AdminService::class, 'create'])->middleware('role:super_admin,admin,manager')->name('services.create');
        Route::post('/services', [AdminService::class, 'store'])->middleware('role:super_admin,admin,manager')->name('services.store');
        Route::get('/services/{id}/edit', [AdminService::class, 'edit'])->middleware('role:super_admin,admin,manager')->name('services.edit');
        Route::put('/services/{id}', [AdminService::class, 'update'])->middleware('role:super_admin,admin,manager')->name('services.update');
        Route::post('/services/{id}/toggle', [AdminService::class, 'toggle'])->middleware('role:super_admin,admin,manager')->name('services.toggle');
        Route::delete('/services/{id}', [AdminService::class, 'destroy'])->middleware('role:super_admin,admin')->name('services.destroy');

        // Blogs — Hostinger CDN editor (Quill), file uploads
        Route::get('/blogs', [AdminBlog::class, 'index'])->middleware('role:super_admin,admin,manager')->name('blogs.index');
        Route::get('/blogs/create', [AdminBlog::class, 'create'])->middleware('role:super_admin,admin,manager')->name('blogs.create');
        Route::post('/blogs', [AdminBlog::class, 'store'])->middleware('role:super_admin,admin,manager')->name('blogs.store');
        Route::get('/blogs/{blog}/edit', [AdminBlog::class, 'edit'])->middleware('role:super_admin,admin,manager')->name('blogs.edit');
        Route::put('/blogs/{blog}', [AdminBlog::class, 'update'])->middleware('role:super_admin,admin,manager')->name('blogs.update');
        Route::delete('/blogs/{blog}', [AdminBlog::class, 'destroy'])->middleware('role:super_admin,admin')->name('blogs.destroy');
        Route::post('/blogs/upload', [AdminBlog::class, 'uploadImage'])->middleware('role:super_admin,admin,manager')->name('blogs.upload');

        // FAQs — JSON-LD auto updates landing FAQPage
        Route::get('/faqs', [AdminFaq::class, 'index'])->middleware('role:super_admin,admin,manager')->name('faqs.index');
        Route::post('/faqs', [AdminFaq::class, 'store'])->middleware('role:super_admin,admin,manager')->name('faqs.store');
        Route::put('/faqs/{faq}', [AdminFaq::class, 'update'])->middleware('role:super_admin,admin,manager')->name('faqs.update');
        Route::delete('/faqs/{faq}', [AdminFaq::class, 'destroy'])->middleware('role:super_admin,admin')->name('faqs.destroy');

        Route::get('/users', [AdminUser::class, 'index'])->middleware('role:super_admin,admin')->name('users.index');
        Route::post('/users', [AdminUser::class, 'store'])->middleware('role:super_admin,admin')->name('users.store');
        Route::post('/users/{id}/role', [AdminUser::class, 'updateRole'])->middleware('role:super_admin,admin')->name('users.role');
        Route::post('/users/{id}/toggle', [AdminUser::class, 'toggleActive'])->middleware('role:super_admin,admin')->name('users.toggle');
        Route::delete('/users/{id}', [AdminUser::class, 'destroy'])->middleware('role:super_admin')->name('users.destroy');

        Route::get('/payouts', [AdminPayout::class, 'index'])->middleware('role:super_admin,admin,finance')->name('payouts.index');
        Route::post('/payouts/{id}/paid', [AdminPayout::class, 'markPaid'])->middleware('role:super_admin,admin,finance')->name('payouts.paid');
        Route::post('/payouts/{id}/hold', [AdminPayout::class, 'hold'])->middleware('role:super_admin,admin,finance')->name('payouts.hold');

        Route::get('/settings', [AdminSetting::class, 'index'])->middleware('role:super_admin')->name('settings.index');
        Route::post('/settings', [AdminSetting::class, 'update'])->middleware('role:super_admin')->name('settings.update');
    });
});
