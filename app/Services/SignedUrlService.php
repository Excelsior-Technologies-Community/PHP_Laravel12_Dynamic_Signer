<?php

namespace App\Services;

use App\Models\SignedUrl;
use Spatie\UrlSigner\Laravel\Facades\UrlSigner;

class SignedUrlService
{
    public function create(
        string $target,
        int $minutes = 10,
        array $params = [],
        array $options = []
    ): array {
        /*
        |--------------------------------------------------------------------------
        | Expiration
        |--------------------------------------------------------------------------
        */

        if ($minutes < 1) {
            $minutes = 1;
        }

        if ($minutes > 60) {
            $minutes = 60;
        }

        $expiresAt = now()->addMinutes($minutes);

        /*
        |--------------------------------------------------------------------------
        | Target URL
        |--------------------------------------------------------------------------
        */

        $url = filter_var($target, FILTER_VALIDATE_URL)
            ? $target
            : route($target, $params);

        /*
        |--------------------------------------------------------------------------
        | Advanced Options
        |--------------------------------------------------------------------------
        */

        $name = $options['name'] ?? null;

        $maxAccesses = $options['max_accesses'] ?? null;

        if ($maxAccesses !== null && $maxAccesses !== '') {
            $maxAccesses = (int) $maxAccesses;
        } else {
            $maxAccesses = null;
        }

        $oneTime = (bool) ($options['one_time'] ?? false);

        /*
        |--------------------------------------------------------------------------
        | One-Time URL always has one access
        |--------------------------------------------------------------------------
        */

        if ($oneTime) {
            $maxAccesses = 1;
        }

        /*
        |--------------------------------------------------------------------------
        | Generate Spatie Signed URL
        |--------------------------------------------------------------------------
        */

        $signedUrl = UrlSigner::sign(
            $url,
            $expiresAt
        );

        /*
        |--------------------------------------------------------------------------
        | Extract Signature
        |--------------------------------------------------------------------------
        */

        $query = parse_url(
            $signedUrl,
            PHP_URL_QUERY
        );

        $queryParameters = [];

        if ($query) {
            parse_str(
                $query,
                $queryParameters
            );
        }

        $signature = $queryParameters['signature'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | Save Database Record
        |--------------------------------------------------------------------------
        */

        $record = SignedUrl::create([
            'name' => $name,

            'target_url' => $url,

            'signed_url' => $signedUrl,

            'signature' => $signature,

            'expires_at' => $expiresAt,

            'revoked_at' => null,

            'access_count' => 0,

            'max_accesses' => $maxAccesses,

            'one_time' => $oneTime,

            'last_accessed_at' => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Return Generated Information
        |--------------------------------------------------------------------------
        */

        return [
            'id' => $record->id,

            'url' => $record->signed_url,

            'expires' => $record->expires_at->timestamp,

            'name' => $record->name,

            'max_accesses' => $record->max_accesses,

            'one_time' => $record->one_time,
        ];
    }
}