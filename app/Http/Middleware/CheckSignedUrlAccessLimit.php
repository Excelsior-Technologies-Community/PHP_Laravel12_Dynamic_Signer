<?php

namespace App\Http\Middleware;

use App\Models\SignedUrl;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSignedUrlAccessLimit
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $signature = $request->query(
            'signature'
        );

        $signedUrl = SignedUrl::where(
            'signature',
            $signature
        )->first();

        if (!$signedUrl) {
            abort(403);
        }

        if ($signedUrl->hasReachedAccessLimit()) {
            abort(403);
        }

        if ($signedUrl->hasBeenUsed()) {
            abort(403);
        }

        return $next($request);
    }
}