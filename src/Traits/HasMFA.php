<?php

namespace Statonlab\MultiFactorAuth\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Statonlab\MultiFactorAuth\Models\AuthenticationToken;

/**
 * Trait HasMFA
 *
 * @package Statonlab\MultiFactorAuth
 * @extends  Illuminate\Database\Eloquent\Model
 */
trait HasMFA
{
    /**
     * The number of minutes before the authentication token expires.
     *
     * @var int
     */
    protected $authTokenExpiresAfter = 60;

    /**
     * Initialize the trait.
     *
     * @return void
     */
    public function initializeHasMFA()
    {
        // Add the mfa_enabled column to fillable columns.
        $this->fillable[] = config('multi_factor_auth.column');
        $this->casts['mfa_enabled'] = 'boolean';
    }

    /**
     * Number of minutes until expiration of token.
     *
     * @return int
     */
    public function getAuthTokenExpiresAfter()
    {
        return $this->authTokenExpiresAfter;
    }

    /**
     * Define the user's relationship to the authentication tokens.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function authTokens(): MorphMany
    {
        return $this->morphMany(AuthenticationToken::class, 'user');
    }

    /**
     * Generate a unique random token.
     *
     * @param int $len The length of the token.
     *
     * @return string
     */
    protected function generateUniqueTokenString(int $len = 6): string
    {
        do {
            $token = strtoupper(Str::random($len));
        } while (AuthenticationToken::where('token', $token)->active()->exists());

        return $token;
    }

    /**
     * @return AuthenticationToken|\Illuminate\Database\Eloquent\Model
     */
    public function createAuthToken()
    {
        $token = $this->generateUniqueTokenString();

        return $this->authTokens()->create([
            'token' => $token,
            'expires_at' => now()->addMinutes($this->authTokenExpiresAfter),
        ]);
    }
}