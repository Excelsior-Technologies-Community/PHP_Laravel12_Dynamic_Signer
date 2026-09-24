<?php

namespace App\Http\Middleware;

use App\Models\SignedUrl;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSignedUrlRevocation
{
    /**
     * Handle the incoming request.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $signature = $request->query('signature');

        if (!$signature) {
            abort(403);
        }

        $signedUrl = SignedUrl::where(
            'signature',
            $signature
        )->first();

        if (!$signedUrl) {
            abort(403);
        }

        if ($signedUrl->isRevoked()) {
            abort(403);
        }

        return $next($request);
    }
}