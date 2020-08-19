<?php

namespace Statonlab\MultiFactorAuth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuthenticationToken extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'user_type',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get active (not expired) tokens.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|\Statonlab\MultiFactorAuth\Models\AuthenticationToken
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>=', now());
    }

    /**
     * Get expired tokens.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|\Statonlab\MultiFactorAuth\Models\AuthenticationToken
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Checks if the token is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Checks if the token is not expired.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->expires_at->isFuture();
    }
}
