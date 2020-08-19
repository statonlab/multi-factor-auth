<?php

namespace Statonlab\MultiFactorAuth\Tests;

use Statonlab\MultiFactorAuth\Models\AuthenticationToken;

class UserIntegrationTest extends TestCase
{
    /** @test */
    public function testCreatingTokens()
    {
        /** @var \User $user */
        $user = factory(\User::class)->create();

        $token = $user->createAuthToken();
        $this->assertInstanceOf(AuthenticationToken::class, $token);
    }

    /** @test */
    public function testRetrievingTokens() {
        /** @var \User $user */
        $user = factory(\User::class)->create();

        /** @var AuthenticationToken $token */
        $token = factory(AuthenticationToken::class)->create(['user_id' => $user->id]);

        $this->assertEquals($token->user->id, $user->id);
        $this->assertTrue($user->authTokens()->where('id', $token->id)->exists());
    }
}