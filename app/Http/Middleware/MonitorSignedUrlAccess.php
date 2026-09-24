<?php

namespace App\Http\Middleware;

use App\Models\SignedUrl;
use App\Models\SignedUrlAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MonitorSignedUrlAccess
{
    /**
     * Handle the incoming request.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $signature = $request->query('signature');

        $signedUrl = SignedUrl::where(
            'signature',
            $signature
        )->first();

        if ($signedUrl) {
            $signedUrl->increment('access_count');

            $signedUrl->update([
                'last_accessed_at' => now(),
            ]);

            SignedUrlAccess::create([
                'signed_url_id' => $signedUrl->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'accessed_at' => now(),
            ]);
        }

        return $next($request);
    }
}