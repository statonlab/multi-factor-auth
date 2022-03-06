<?php

namespace Statonlab\MultiFactorAuth\Traits;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Statonlab\MultiFactorAuth\Identity;
use Statonlab\MultiFactorAuth\Models\AuthenticationToken;
use Statonlab\MultiFactorAuth\Notifications\IdentityVerificationNotification;

trait VerifiesAuthTokens
{
    /**
     * Send the user to home page on success.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return view('mfa::index')->with([
            'obfuscated_email' => $this->obfuscateEmail($user->email),
        ]);
    }

    /**
     * @param string $email
     * @return string
     */
    protected function obfuscateEmail(string $email)
    {
        $parts = explode('@', $email);
        $username = array_shift($parts);
        $domain = implode('.', $parts);
        $letter = substr($username, 0, 1);
        $end = substr($username, -2);
        $pad = str_pad('', strlen($username) - 3, '*');

        return "{$letter}{$pad}{$end}@$domain";
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showVerificationForm(Request $request)
    {
        $user = $request->user();

        return view('mfa::verify')->with([
            'minutes' => $user->getAuthTokenExpiresAfter(),
        ]);
    }

    /**
     * Create and send the notification containing the token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendToken(Request $request)
    {
        $user = $request->user();

        if (RateLimiter::tooManyAttempts("statonlab-mfa:send-token-$user->id", 1)) {
            return redirect()->back()->withErrors([
                'attempts' => ['Please wait at least 1 minute before attempting to send a verification code again.'],
            ]);
        }

        $identity = new Identity();
        $token = $identity->createToken($user);

        $this->validate($request, [
            'channel' => ['required', Rule::in(['mail'])],
        ]);

        $token->user->notify($this->createNotification($token,
            $request->input('channel')));

        return redirect()->to(route('mfa.form'));
    }

    /**
     * Create the notification.
     *
     * @param \Statonlab\MultiFactorAuth\Models\AuthenticationToken $token
     * @param string $channel
     * @return \Statonlab\MultiFactorAuth\Notifications\IdentityVerificationNotification
     */
    protected function createNotification(AuthenticationToken $token, string $channel)
    {
        return new IdentityVerificationNotification($token, $channel);
    }

    /**
     * Perform verification attempt.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function verify(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
            'remember' => 'nullable|boolean',
        ]);

        $user = $request->user();

        if (RateLimiter::tooManyAttempts("statonlab-mfa:verify-token-$user->id", 5)) {
            return redirect()->back()->withErrors([
                'code' => ['Please wait at least 1 minute before attempting to verify code again.'],
            ]);
        }

        $remember = $request->input('remember') == 1;
        $identity = new Identity();
        if ($identity->attempt($user, $request->input('code'), $remember)) {
            RateLimiter::clear("statonlab-mfa:verify-token-$user->id");

            return $this->sendSuccessResponse();
        }

        return redirect()->back()->withErrors([
            'code' => ['Invalid code. Please try again.'],
        ]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    protected function sendSuccessResponse()
    {
        return redirect()->intended($this->redirectTo);
    }
}
