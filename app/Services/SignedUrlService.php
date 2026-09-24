<?php

namespace App\Services;

use App\Models\SignedUrl;
use Spatie\UrlSigner\Laravel\Facades\UrlSigner;

class SignedUrlService
{
    /**
     * Generate and store a signed URL.
     */
    public function create($target, $minutes = 10, $params = [])
    {
        // Set expiration time.
        $expiresAt = now()->addMinutes($minutes);

        // Check whether input is a full URL or Laravel route name.
        $url = filter_var($target, FILTER_VALIDATE_URL)
            ? $target
            : route($target, $params);

        // Generate signed URL.
        $signedUrl = UrlSigner::sign($url, $expiresAt);

        // Extract signature from generated URL.
        $query = parse_url($signedUrl, PHP_URL_QUERY);

        parse_str($query ?? '', $queryParameters);

        $signature = $queryParameters['signature'] ?? null;

        // Store generated signed URL.
        $record = SignedUrl::create([
            'target_url' => $url,
            'signed_url' => $signedUrl,
            'signature' => $signature,
            'expires_at' => $expiresAt,
        ]);

        return [
            'id' => $record->id,
            'url' => $signedUrl,
            'expires' => $expiresAt->timestamp,
        ];
    }
}