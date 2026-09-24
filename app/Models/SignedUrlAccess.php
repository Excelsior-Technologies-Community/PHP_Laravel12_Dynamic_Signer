<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignedUrlAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'signed_url_id',
        'ip_address',
        'user_agent',
        'accessed_at',
    ];

    protected function casts(): array
    {
        return [
            'accessed_at' => 'datetime',
        ];
    }

    /**
     * Get the signed URL related to this access.
     */
    public function signedUrl(): BelongsTo
    {
        return $this->belongsTo(SignedUrl::class);
    }
}