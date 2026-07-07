<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LogController as AdminLogController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AccountStatusController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactImportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomainHealthController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\SmtpController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UnsubscribeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'))->name('home');

// Per-recipient unsubscribe. The GET link is a signed URL embedded in emails;
// the confirm POST re-validates the same signature.
Route::middleware('signed')->group(function () {
    Route::get('/unsubscribe/{contact}', [UnsubscribeController::class, 'show'])->name('unsubscribe');
    Route::post('/unsubscribe/{contact}', [UnsubscribeController::class, 'confirm'])->name('unsubscribe.confirm');
});

/*
|--------------------------------------------------------------------------
| Authenticated, any status (status holding screens)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/waiting-approval', [AccountStatusController::class, 'pending'])->name('status.pending');
    Route::get('/account-rejected', [AccountStatusController::class, 'rejected'])->name('status.rejected');
    Route::get('/account-suspended', [AccountStatusController::class, 'suspended'])->name('status.suspended');
});

/*
|--------------------------------------------------------------------------
| Approved user app
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // First-time setup wizard.
    Route::get('/setup/{step?}', [SetupController::class, 'show'])->name('setup.index');
    Route::post('/setup/{step}', [SetupController::class, 'update'])->name('setup.update');

    // Profile + account settings.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // SMTP settings + test.
    Route::get('/smtp', [SmtpController::class, 'edit'])->name('smtp.edit');
    Route::put('/smtp', [SmtpController::class, 'update'])->name('smtp.update');
    Route::post('/smtp/test', [SmtpController::class, 'test'])->middleware('throttle:6,1')->name('smtp.test');

    // Contacts.
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/contacts/export', [ContactController::class, 'export'])->name('contacts.export');
    Route::post('/contacts/bulk-delete', [ContactController::class, 'bulkDestroy'])->name('contacts.bulkDestroy');
    Route::get('/contacts/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

    // CSV import (chunked).
    Route::get('/contacts-import', [ContactImportController::class, 'create'])->name('contacts.import.create');
    Route::post('/contacts-import', [ContactImportController::class, 'store'])->name('contacts.import.store');
    Route::post('/contacts-import/{import}/batch', [ContactImportController::class, 'batch'])->name('contacts.import.batch');
    Route::get('/contacts-import/{import}', [ContactImportController::class, 'show'])->name('contacts.import.show');

    // Templates.
    Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/create', [TemplateController::class, 'create'])->name('templates.create');
    Route::post('/templates', [TemplateController::class, 'store'])->name('templates.store');
    Route::get('/templates/{template}/edit', [TemplateController::class, 'edit'])->name('templates.edit');
    Route::put('/templates/{template}', [TemplateController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{template}', [TemplateController::class, 'destroy'])->name('templates.destroy');
    Route::post('/templates/{template}/duplicate', [TemplateController::class, 'duplicate'])->name('templates.duplicate');
    Route::get('/templates/{template}/preview', [TemplateController::class, 'preview'])->name('templates.preview');

    // Campaigns.
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create');
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::get('/campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('campaigns.send');
    Route::post('/campaigns/{campaign}/batch', [CampaignController::class, 'batch'])->name('campaigns.batch');
    Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');
    Route::get('/campaigns/{campaign}/recipients', [CampaignController::class, 'recipients'])->name('campaigns.recipients');

    // Domain health.
    Route::get('/domain-health', [DomainHealthController::class, 'index'])->name('domain-health.index');
    Route::post('/domain-health', [DomainHealthController::class, 'check'])->name('domain-health.check');

    // Activity log.
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
});

/*
|--------------------------------------------------------------------------
| Admin app
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users/pending', [AdminUserController::class, 'pending'])->name('users.pending');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/reject', [AdminUserController::class, 'reject'])->name('users.reject');
    Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/reactivate', [AdminUserController::class, 'reactivate'])->name('users.reactivate');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/logs/campaigns', [AdminLogController::class, 'campaigns'])->name('logs.campaigns');
    Route::get('/logs/smtp', [AdminLogController::class, 'smtp'])->name('logs.smtp');
    Route::get('/logs/system', [AdminLogController::class, 'system'])->name('logs.system');

    Route::get('/settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
