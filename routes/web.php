<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignedUrlController;

/*
|--------------------------------------------------------------------------
| Dynamic Signed URL Generator
|--------------------------------------------------------------------------
*/

Route::get('/', [
    SignedUrlController::class,
    'form'
])->name('home');


Route::post('/generate', [
    SignedUrlController::class,
    'generate'
])->name('generate');


/*
|--------------------------------------------------------------------------
| Analytics
|--------------------------------------------------------------------------
*/

Route::get('/signed-url/analytics', [
    SignedUrlController::class,
    'analytics'
])->name('signed-url.analytics');


/*
|--------------------------------------------------------------------------
| History
|--------------------------------------------------------------------------
*/

Route::get('/signed-url/history', [
    SignedUrlController::class,
    'history'
])->name('signed-url.history');


/*
|--------------------------------------------------------------------------
| CSV Export
|--------------------------------------------------------------------------
*/

Route::get('/signed-url/export', [
    SignedUrlController::class,
    'exportCsv'
])->name('signed-url.export');


/*
|--------------------------------------------------------------------------
| Individual Access History
|--------------------------------------------------------------------------
*/

Route::get(
    '/signed-url/{signedUrl}/access-history',
    [
        SignedUrlController::class,
        'accessHistory'
    ]
)->name('signed-url.access-history');


/*
|--------------------------------------------------------------------------
| Individual Revoke
|--------------------------------------------------------------------------
*/

Route::post(
    '/signed-url/{signedUrl}/revoke',
    [
        SignedUrlController::class,
        'revoke'
    ]
)->name('signed-url.revoke');


/*
|--------------------------------------------------------------------------
| Bulk Revoke
|--------------------------------------------------------------------------
*/

Route::post(
    '/signed-url/bulk-revoke',
    [
        SignedUrlController::class,
        'bulkRevoke'
    ]
)->name('signed-url.bulk-revoke');


/*
|--------------------------------------------------------------------------
| Secure Page
|--------------------------------------------------------------------------
*/

Route::get('/secure-page', [
    SignedUrlController::class,
    'secure'
])
    ->name('secure.page')
    ->middleware([
        'signedurl',
        'signedurl.revoked',
        'signedurl.limit',
        'signedurl.monitor',
    ]);
