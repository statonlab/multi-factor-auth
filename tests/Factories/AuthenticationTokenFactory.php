<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Statonlab\MultiFactorAuth\Models\AuthenticationToken;

$factory->define(AuthenticationToken::class, function (Faker $faker) {
    return [
        'user_id' => factory(\User::class)->create()->id,
        'token' => $faker->text(250),
        'expires_at' => now()->addHour(),
        'user_type' => User::class,
    ];
});
