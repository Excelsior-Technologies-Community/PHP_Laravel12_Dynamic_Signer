<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SignedUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'target_url',
        'signed_url',
        'signature',
        'expires_at',
        'max_accesses',
        'one_time',
        'revoked_at',
        'access_count',
        'last_accessed_at',
        'first_accessed_at',
        'created_ip',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
            'last_accessed_at' => 'datetime',
            'first_accessed_at' => 'datetime',

            'max_accesses' => 'integer',
            'access_count' => 'integer',

            'one_time' => 'boolean',
        ];
    }

    /**
     * Access records for this signed URL.
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
        return $this->expires_at !== null
            && $this->expires_at->isPast();
    }

    /**
     * Check whether the URL has been revoked.
     */
    public function isRevoked(): bool
    {
        return !is_null($this->revoked_at);
    }

    /**
     * Check whether the maximum access limit has been reached.
     *
     * One-time URLs are automatically limited to one access.
     */
    public function hasReachedAccessLimit(): bool
    {
        if ($this->one_time) {
            return $this->access_count >= 1;
        }

        if (is_null($this->max_accesses)) {
            return false;
        }

        return $this->access_count >= $this->max_accesses;
    }

    /**
     * Return the number of remaining accesses.
     *
     * null = unlimited
     */
    public function remainingAccesses(): ?int
    {
        // One-time URL
        if ($this->one_time) {
            return max(0, 1 - $this->access_count);
        }

        // Unlimited URL
        if (is_null($this->max_accesses)) {
            return null;
        }

        return max(
            0,
            $this->max_accesses - $this->access_count
        );
    }

    /**
     * Status of the signed URL.
     */
    public function getStatusAttribute(): string
    {
        if ($this->isRevoked()) {
            return 'Revoked';
        }

        if ($this->isExpired()) {
            return 'Expired';
        }

        if ($this->hasReachedAccessLimit()) {
            return 'Limit Reached';
        }

        return 'Active';
    }

    /**
     * CSS class for status badge.
     */
    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'Active' => 'active',
            'Expired' => 'expired',
            'Revoked' => 'revoked',
            'Limit Reached' => 'limit',
            default => 'active',
        };
    }

    /**
     * Remaining accesses as an attribute.
     *
     * This also allows:
     *
     * $signedUrl->remaining_accesses
     */
    public function getRemainingAccessesAttribute(): ?int
    {
        return $this->remainingAccesses();
    }
}