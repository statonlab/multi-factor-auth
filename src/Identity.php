<?php

namespace Statonlab\MultiFactorAuth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Route;
use Statonlab\MultiFactorAuth\Http\Controllers\MultiFactorAuthController;
use Statonlab\MultiFactorAuth\Models\AuthenticationToken;

class Identity
{
    /**
     * @var string
     */
    protected $cookieName;

    /**
     * @var string
     */
    protected $sessionName;

    /**
     * Identity constructor.
     */
    public function __construct()
    {
        $this->cookieName = config('multi_factor_auth.cookie_name');
        $this->sessionName = config('multi_factor_auth.session_name');
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return AuthenticationToken
     */
    public function createToken(Authenticatable $user) {
        return $user->createAuthToken();
    }

    /**
     * Has verified identity.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return false
     */
    public function verified(Authenticatable $user)
    {
        if (session($this->sessionName, null) === $user->getAuthIdentifier()) {
            return true;
        }

        $cookie = cookie()->get($this->cookieName);
        if (! $cookie) {
            return false;
        }

        if ($cookie === $user->getAuthIdentifier()) {
            // Set session
            session()->put($this->sessionName, $user->getAuthIdentifier());

            return true;
        }

        return false;
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param string $token
     * @param bool $remember_device
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function attempt(Authenticatable $user, string $token, bool $remember_device = false)
    {
        $token = $user->authTokens()->where('token', $token)->active()->first();
        if ($token) {
            $this->setCookie($user, $remember_device);

            return true;
        }

        return false;
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param bool $remember_device
     * @return $this
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setCookie(Authenticatable $user, bool $remember_device = true)
    {
        session()->put($this->sessionName, $user->getAuthIdentifier());

        if ($remember_device) {
            $lifetime = config('multi_factor_auth.cookie_lifetime');
            $cookie = cookie()->make($this->cookieName, $user->getAuthIdentifier(), $lifetime);
            cookie()->queue($cookie);
        }

        return $this;
    }

    /**
     * Register routes.
     */
    public function routes() {
        Route::get('/identity-verification', [MultiFactorAuthController::class, 'index'])
            ->name('mfa.index');
        Route::post('/identity-verification', [MultiFactorAuthController::class, 'sendToken'])
            ->name('mfa.send');
        Route::get('/identity-verification/verify', [MultiFactorAuthController::class, 'showVerificationForm'])
            ->name('mfa.form');
        Route::post('/identity-verification/verify', [MultiFactorAuthController::class, 'verify'])
            ->name('mfa.verify');
    }
}