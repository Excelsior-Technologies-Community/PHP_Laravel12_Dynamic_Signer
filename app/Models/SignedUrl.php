<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SignedUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_url',
        'signed_url',
        'signature',
        'expires_at',
        'revoked_at',
        'access_count',
        'last_accessed_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
            'last_accessed_at' => 'datetime',
        ];
    }

    /**
     * Get all access records for this signed URL.
     */
    public function accesses(): HasMany
    {
        return $this->hasMany(SignedUrlAccess::class);
    }

    /**
     * Check whether the URL has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check whether the URL has been manually revoked.
     */
    public function isRevoked(): bool
    {
        return !is_null($this->revoked_at);
    }

    /**
     * Get current URL status.
     */
    public function getStatusAttribute(): string
    {
        if ($this->isRevoked()) {
            return 'Revoked';
        }

        if ($this->isExpired()) {
            return 'Expired';
        }

        return 'Active';
    }
}