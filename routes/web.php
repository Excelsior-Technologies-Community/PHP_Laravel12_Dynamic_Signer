<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignedUrlController;

/*
|--------------------------------------------------------------------------
| Signed URL Generator
|--------------------------------------------------------------------------
*/

// Main generator page.
Route::get('/', [SignedUrlController::class, 'form'])
    ->name('home');

// Generate signed URL.
Route::post('/generate', [SignedUrlController::class, 'generate'])
    ->name('generate');


/*
|--------------------------------------------------------------------------
| Signed URL Analytics
|--------------------------------------------------------------------------
*/

// Analytics dashboard.
Route::get('/signed-url/analytics', [
    SignedUrlController::class,
    'analytics'
])->name('signed-url.analytics');

// Signed URL history.
Route::get('/signed-url/history', [
    SignedUrlController::class,
    'history'
])->name('signed-url.history');

// Individual access history.
Route::get('/signed-url/{signedUrl}/access-history', [
    SignedUrlController::class,
    'accessHistory'
])->name('signed-url.access-history');

// Revoke signed URL.
Route::post('/signed-url/{signedUrl}/revoke', [
    SignedUrlController::class,
    'revoke'
])->name('signed-url.revoke');


/*
|--------------------------------------------------------------------------
| Protected Secure Page
|--------------------------------------------------------------------------
*/

// Original secure page.
//
// Middleware order:
// 1. Spatie validates signature + expiry.
// 2. Revocation middleware checks manual revocation.
// 3. Monitoring middleware records successful access.
Route::get('/secure-page', [
    SignedUrlController::class,
    'secure'
])
    ->name('secure.page')
    ->middleware([
        'signedurl',
        'signedurl.revoked',
        'signedurl.monitor',
    ]);