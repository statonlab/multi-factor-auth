<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Statonlab\MultiFactorAuth\Traits\HasMFA;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasMFA, Authorizable;

    /** @var string[]  */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @var string[]  */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
    ];
}