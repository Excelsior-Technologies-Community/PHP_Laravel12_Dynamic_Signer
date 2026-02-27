<?php

namespace App\Services;

// Import Spatie URL Signer facade
use Spatie\UrlSigner\Laravel\Facades\UrlSigner;

class SignedUrlService
{

    public function create($target, $minutes = 10, $params = [])
    {
        // Set expiration time for signed URL
        $expiresAt = now()->addMinutes($minutes);

        // Check if input is a full URL or a Laravel route name
        $url = filter_var($target, FILTER_VALIDATE_URL)
            ? $target                     // Use direct URL
            : route($target, $params);   // Generate URL from route name

        // Generate signed URL with expiry timestamp
        $signedUrl = UrlSigner::sign($url, $expiresAt);

        // Return signed URL and expiry timestamp
        return [
            'url' => $signedUrl,
            'expires' => $expiresAt->timestamp
        ];
    }
}