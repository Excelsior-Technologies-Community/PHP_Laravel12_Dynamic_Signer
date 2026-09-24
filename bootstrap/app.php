<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckSignedUrlRevocation;
use App\Http\Middleware\MonitorSignedUrlAccess;
use Spatie\UrlSigner\Laravel\Middleware\ValidateSignature;

return Application::configure(
    basePath: dirname(__DIR__)
)

    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'signedurl' => ValidateSignature::class,
            'signedurl.revoked' => CheckSignedUrlRevocation::class,
            'signedurl.monitor' => MonitorSignedUrlAccess::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })

    ->create();