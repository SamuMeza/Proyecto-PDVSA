<?php
namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use App\Services\AuthService;
use App\Core\Session;

class AuthFlowTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function testLoginMethodSignature(): void
    {
        $this->assertTrue(method_exists(AuthService::class, 'login'));
    }

    public function testVerifyOtpMethodSignature(): void
    {
        $this->assertTrue(method_exists(AuthService::class, 'verifyOtp'));
    }

    public function testCompleteLoginMethodSignature(): void
    {
        $this->assertTrue(method_exists(AuthService::class, 'completeLogin'));
    }

    public function testLogoutMethodSignature(): void
    {
        $this->assertTrue(method_exists(AuthService::class, 'logout'));
    }

    public function testCheckMethodSignature(): void
    {
        $this->assertTrue(method_exists(AuthService::class, 'check'));
    }

    public function testLoginReturnsFalseForNonexistentUser(): void
    {
        $result = AuthService::login('nonexistent_user_' . time(), 'password');
        $this->assertFalse($result['ok']);
    }

    public function testVerifyOtpReturnsFalseForInvalidOtp(): void
    {
        $result = AuthService::verifyOtp(999999, '000000');
        $this->assertFalse($result['ok']);
    }

    public function testCheckReturnsFalseWithoutSession(): void
    {
        Session::start();
        Session::destroy();
        $this->assertFalse(AuthService::check());
    }

    public function testIsAdminReturnsFalseWithoutSession(): void
    {
        Session::start();
        Session::destroy();
        $this->assertFalse(AuthService::isAdmin());
    }
}
