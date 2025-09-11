<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    AnnouncementController,
    HouseholdController,
    ResidentController,
    DueController,
    PaymentController,
    LetterController,
    DashboardController,
    LandingController,
    StatisticsController,
    GoogleAuthController,
    ActivityController,
    SettingController
};
use App\Http\Controllers\Admin\BusinessController;

// Landing Page Routes (Public)
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/pengumuman', [LandingController::class, 'announcements'])->name('landing.announcements');
Route::get('/umkm', [LandingController::class, 'businesses'])->name('landing.businesses');
Route::get('/umkm/{business}', [LandingController::class, 'businessDetail'])->name('landing.business-detail');
Route::get('/transparansi', [LandingController::class, 'transparency'])->name('landing.transparency');
Route::get('/statistik', [StatisticsController::class, 'index'])->name('landing.statistics');
Route::get('/kegiatan', [ActivityController::class, 'publicIndex'])->name('landing.activities');
Route::get('/kegiatan/{activity}', [ActivityController::class, 'show'])->name('landing.activity-detail');

// Public API endpoints
Route::get('/api/landing/stats', [LandingController::class, 'getLandingStats'])->name('api.landing.stats');
Route::get('/api/statistics/data', [StatisticsController::class, 'apiData'])->name('api.statistics.data');

// Auth (simple session)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// Google OAuth Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::post('/auth/google/unlink', [GoogleAuthController::class, 'unlinkGoogle'])->middleware('auth')->name('auth.google.unlink');

// Dashboard (guard)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->middleware('can:isAdmin')->name('dashboard.analytics');

    // Real-time API endpoints
    Route::get('/api/dashboard/stats', [DashboardController::class, 'getDashboardStats'])->name('api.dashboard.stats');

    // Mobile-specific routes
    Route::get('/mobile/dashboard', [DashboardController::class, 'mobileDashboard'])->name('mobile.dashboard');
    Route::get('/mobile/check', function() {
        return response()->json(['mobile' => request()->header('User-Agent')]);
    })->name('mobile.check');

    // Admin only
    Route::middleware('can:isAdmin')->group(function () {
        Route::resource('households', HouseholdController::class);
        Route::resource('residents', ResidentController::class);
        Route::resource('announcements', AnnouncementController::class)->except(['show']);
        Route::resource('activities', ActivityController::class);
        Route::post('activities/{activity}/toggle-status', [ActivityController::class, 'toggleStatus'])->name('activities.toggle-status');
        Route::post('activities/{activity}/toggle-featured', [ActivityController::class, 'toggleFeatured'])->name('activities.toggle-featured');

        // Settings/Content Management
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('settings/create', [SettingController::class, 'create'])->name('settings.create');
        Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
        Route::delete('settings/{setting}', [SettingController::class, 'destroy'])->name('settings.destroy');

        // Business/UMKM Management
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('businesses', BusinessController::class);
            Route::post('businesses/{business}/toggle-status', [BusinessController::class, 'toggleStatus'])->name('businesses.toggle-status');
            Route::post('businesses/{business}/toggle-featured', [BusinessController::class, 'toggleFeatured'])->name('businesses.toggle-featured');
        });

        // Dues management
        Route::get('dues', [DueController::class, 'index'])->name('dues.index');
        Route::post('dues/generate', [DueController::class, 'generate'])->name('dues.generate');

        // Payments
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

        // Letters - Admin management
        Route::get('letters', [LetterController::class, 'index'])->name('letters.index');
        Route::post('letters/{letter}/approve', [LetterController::class, 'approve'])->name('letters.approve');
        Route::post('letters/{letter}/reject', [LetterController::class, 'reject'])->name('letters.reject');
        Route::get('letters/{letter}', [LetterController::class, 'show'])->name('letters.show');
        Route::get('letters/{letter}/download', [LetterController::class, 'downloadPdf'])->name('letters.download');

        // Mobile admin routes
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('dashboard', [DashboardController::class, 'adminMobileDashboard'])->name('dashboard.mobile');
            Route::get('residents/create', [ResidentController::class, 'mobileCreate'])->name('residents.create');
            Route::get('dues/create', [DueController::class, 'mobileCreate'])->name('dues.create');
            Route::get('letters/create', [LetterController::class, 'mobileCreate'])->name('letters.create');
            Route::get('reports', [DashboardController::class, 'mobileReports'])->name('reports');
            Route::get('activities', [DashboardController::class, 'mobileActivities'])->name('activities');
        });
    });

    // Resident (warga)
    Route::get('my-household', [HouseholdController::class, 'mine'])->name('households.mine');
    Route::get('my-dues', [DueController::class, 'mine'])->name('dues.mine');

    // Letters - Resident access
    Route::get('my-letters', [LetterController::class, 'mine'])->name('letters.mine');
    Route::get('letters/create', [LetterController::class, 'create'])->name('letters.create');
    Route::post('letters', [LetterController::class, 'store'])->name('letters.store');
    Route::get('letters/{letter}/download', [LetterController::class, 'downloadPdf'])->name('letters.download');
});
