<?php
namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

class AuthFlowTest extends TestCase
{
    public function testLoginMethodSignature(): void
    {
        $this->assertTrue(method_exists(\App\Services\AuthService::class, 'login'));
    }
}
