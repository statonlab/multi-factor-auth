<?php

namespace Statonlab\MultiFactorAuth\Facades;

use Illuminate\Support\Facades\Facade;

class MultiFactorAuthFacade extends Facade
{
    public static function getFacadeAccessor() {
        return 'MultiFactorAuth';
    }
}