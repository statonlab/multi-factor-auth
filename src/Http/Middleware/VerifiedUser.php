<?php

namespace Statonlab\MultiFactorAuth\Http\Middleware;

use Closure;
use Statonlab\MultiFactorAuth\Identity;

class VerifiedUser
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            // Not authenticated

            return redirect()->to(route('login'));
        }

        $identity = new Identity();

        // Authenticated User
        if ($user->mfa_enabled && ! $identity->verified($user)) {
            return redirect()->to(route('mfa.index'));
        }

        return $next($request);
    }
}
