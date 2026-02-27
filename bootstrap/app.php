<?php

// Import Laravel Application class
use Illuminate\Foundation\Application;

// Import Exception configuration
use Illuminate\Foundation\Configuration\Exceptions;

// Import Middleware configuration
use Illuminate\Foundation\Configuration\Middleware;

// Import Spatie Signed URL validation middleware
use Spatie\UrlSigner\Laravel\Middleware\ValidateSignature;

// Configure the Laravel application
return Application::configure(basePath: dirname(__DIR__))

    // Register application routes
    ->withRouting(
        web: __DIR__.'/../routes/web.php',          // Web routes file
        commands: __DIR__.'/../routes/console.php', // Console commands file
        health: '/up',                               // Health check endpoint
    )

    // Register middleware
    ->withMiddleware(function (Middleware $middleware): void {

        // Create middleware alias 'signedurl'
        $middleware->alias([
            'signedurl' => ValidateSignature::class, // Validate signed URL middleware
        ]);

    })

    // Configure exception handling
    ->withExceptions(function (Exceptions $exceptions): void {
        // You can customize exception handling here
    })

    // Create and return application instance
    ->create();