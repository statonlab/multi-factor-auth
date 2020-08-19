<?php

namespace Statonlab\MultiFactorAuth\Http\Controllers;

use Illuminate\Http\Request;
use Statonlab\MultiFactorAuth\Traits\VerifiesAuthTokens;

class MultiFactorAuthController extends Controller
{
    use VerifiesAuthTokens;

    /**
     * Path to redirect to if verification is successful.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * MultiFactorAuthController constructor.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }
}
