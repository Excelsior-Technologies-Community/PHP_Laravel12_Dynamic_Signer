<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignedUrlController;


// Default route - Show the dynamic URL generator form
Route::get('/', [SignedUrlController::class, 'form']);

// Handle form submission - Generate signed URL
Route::post('/generate', [SignedUrlController::class, 'generate'])
    ->name('generate');

// Protected route - Accessible only via valid signed URL
Route::get('/secure-page', [SignedUrlController::class, 'secure'])
    ->name('secure.page')       
    ->middleware('signedurl');  