<?php

namespace Statonlab\MultiFactorAuth\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Statonlab\MultiFactorAuth\Models\AuthenticationToken;

class VerificationModelTest extends TestCase
{
    /** @test */
    public function testRelatedUserModelExists()
    {
        $token = factory(AuthenticationToken::class)->create();
        $this->assertNotNull($token->user);
    }

    /** @test */
    public function testExpiredScope()
    {
        /** @var AuthenticationToken $token */
        $token = factory(AuthenticationToken::class)->create([
            'expires_at' => now()->subDay(),
        ]);

        $this->assertTrue(AuthenticationToken::where('id', $token->id)
            ->expired()
            ->exists());

        $this->assertFalse(AuthenticationToken::where('id', $token->id)
            ->active()
            ->exists());

        $this->assertTrue($token->isExpired());
    }

    /** @test */
    public function testActiveScope()
    {
        /** @var AuthenticationToken $token */
        $token = factory(AuthenticationToken::class)->create([
            'expires_at' => now()->addWeek(),
        ]);

        $this->assertTrue(AuthenticationToken::where('id', $token->id)
            ->active()
            ->exists());

        $this->assertFalse(AuthenticationToken::where('id', $token->id)
            ->expired()
            ->exists());

        $this->assertTrue($token->isActive());
    }
}