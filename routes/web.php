<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicantUploadController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\ApplicantAuthController;

// Redirect root URL to applicant login
Route::get('/', function () {
    return redirect()->route('applicant.login');
});

// Admin Authentication Routes
Route::group(['prefix' => 'admin'], function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

// Applicant Authentication Routes
Route::get('/login', [ApplicantAuthController::class, 'showLoginForm'])->name('applicant.login');
Route::post('/login', [ApplicantAuthController::class, 'login'])->name('applicant.login.submit');
Route::get('/register', [ApplicantAuthController::class, 'showRegistrationForm'])->name('applicant.register');
Route::post('/register', [ApplicantAuthController::class, 'register'])->name('applicant.register.submit');
Route::post('/logout', [ApplicantAuthController::class, 'logout'])->name('applicant.logout');

// Protected Applicant Routes
Route::group(['middleware' => 'applicant'], function () {
    Route::get('/upload', [ApplicantUploadController::class, 'create'])->name('upload.create');
    Route::post('/upload', [ApplicantUploadController::class, 'store'])->name('upload.store');
    Route::get('/applicant/dashboard', [ApplicantUploadController::class, 'dashboard'])->name('applicant.dashboard');
});

// Admin Dashboard Routes (grouped under 'admin' and protected)
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/', function() { return redirect()->route('admin.dashboard'); });
    
    // Added missing routes for the admin panel
    Route::get('/profile', [AdminDashboardController::class, 'profile'])->name('admin.profile');
    Route::get('/documents', [AdminDashboardController::class, 'documents'])->name('admin.documents');
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings');
    Route::get('/help', [AdminDashboardController::class, 'help'])->name('admin.help');

    // Additional admin application routes (updated for email-based lookups)
    Route::get('/application/{email}', [AdminDashboardController::class, 'getApplication'])
        ->name('admin.getApplication')
        ->where('email', '.*'); // Allow dots in email addresses
    Route::post('/application/save-notes', [AdminDashboardController::class, 'saveNotes'])->name('admin.saveNotes');
    Route::post('/application/update-status', [AdminDashboardController::class, 'updateStatus'])->name('admin.updateStatus');
    Route::post('/application/document-status', [AdminDashboardController::class, 'updateDocumentStatus'])->name('admin.updateDocumentStatus');
    
    // Update the download route to handle slashes in folder names
    Route::get('/download/{path}', [AdminDashboardController::class, 'downloadFile'])
        ->where('path', '.*')
        ->name('admin.downloadFile');
});

// Static pages for Privacy Policy & Terms of Service
Route::view('/privacy-policy', 'privacy-policy')->name('privacy.policy');
Route::view('/terms-of-service', 'terms-of-service')->name('terms.service');
