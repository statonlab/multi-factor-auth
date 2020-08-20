<?php

namespace Statonlab\MultiFactorAuth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cookie;
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
    public function createToken(Authenticatable $user)
    {
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

        $cookie = Cookie::get($this->cookieName);
        if (! $cookie) {
            return false;
        }

        if ($cookie === sha1($user->getAuthIdentifier())) {
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
            $cookie = cookie()->make($this->cookieName, sha1($user->getAuthIdentifier()), $lifetime);
            cookie()->queue($cookie);
        }

        return $this;
    }

    /**
     * Delete cookie.
     */
    public function forgetDevice()
    {
        if (Cookie::get($this->cookieName)) {
            cookie()->queue(cookie()->forget($this->cookieName));
        }
    }

    /**
     * Register routes.
     *
     * @param string $controller
     */
    public function routes(string $controller = MultiFactorAuthController::class)
    {
        Route::get('/identity-verification', [$controller, 'index'])->name('mfa.index');
        Route::post('/identity-verification', [$controller, 'sendToken'])->name('mfa.send');
        Route::get('/identity-verification/verify', [$controller, 'showVerificationForm'])->name('mfa.form');
        Route::post('/identity-verification/verify', [$controller, 'verify'])->name('mfa.verify');
    }
}