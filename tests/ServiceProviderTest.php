<?php

namespace Statonlab\MultiFactorAuth\Tests;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function testThatServiceProviderExists()
    {
        $this->assertTrue(class_exists('Statonlab\\MultiFactorAuth\\MultiFactorAuthProvider'));
    }
}