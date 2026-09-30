<?php

namespace App\Http\Middleware;

use App\Models\SignedUrl;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSignedUrlPasscode
{
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->query('signature');

        if (!$signature) {
            return $next($request);
        }

        $signedUrl = SignedUrl::where('signature', $signature)->first();

        if ($signedUrl && $signedUrl->is_passcode_protected) {
            $sessionKey = 'signed_url_verified_' . $signature;
            if (!$request->session()->has($sessionKey)) {
                return response()->view('verify-passcode', [
                    'signedUrl' => $signedUrl,
                    'fullUrl' => $request->fullUrl(),
                ]);
            }
        }

        return $next($request);
    }
}
