<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\AuthService;

class AuthServiceTest extends TestCase
{
    public function testLoginMethodExists(): void
    {
        $this->assertTrue(method_exists(AuthService::class, 'login'));
    }

    public function testVerifyOtpMethodExists(): void
    {
        $this->assertTrue(method_exists(AuthService::class, 'verifyOtp'));
    }

    public function testCompleteLoginMethodExists(): void
    {
        $this->assertTrue(method_exists(AuthService::class, 'completeLogin'));
    }

    public function testLogoutMethodExists(): void
    {
        $this->assertTrue(method_exists(AuthService::class, 'logout'));
    }

    public function testCheckMethodExists(): void
    {
        $this->assertTrue(method_exists(AuthService::class, 'check'));
    }

    public function testRequireAuthMethodExists(): void
    {
        $this->assertTrue(method_exists(AuthService::class, 'requireAuth'));
    }

    public function testIsAdminMethodExists(): void
    {
        $this->assertTrue(method_exists(AuthService::class, 'isAdmin'));
    }

    public function testHasPermissionMethodExists(): void
    {
        $this->assertTrue(method_exists(AuthService::class, 'hasPermission'));
    }

    public function testRequirePermissionMethodExists(): void
    {
        $this->assertTrue(method_exists(AuthService::class, 'requirePermission'));
    }

    public function testCreateUserMethodExists(): void
    {
        $this->assertTrue(method_exists(AuthService::class, 'createUser'));
    }

    public function testLoginReturnsArrayOnInvalidUser(): void
    {
        $result = AuthService::login('nonexistent_user', 'password');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('ok', $result);
        $this->assertFalse($result['ok']);
        $this->assertArrayHasKey('error', $result);
    }

    public function testLoginReturnsArrayOnInvalidPassword(): void
    {
        $result = AuthService::login('admin', 'wrong_password');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('ok', $result);
        $this->assertFalse($result['ok']);
    }

    public function testVerifyOtpReturnsArrayOnNoOtp(): void
    {
        $result = AuthService::verifyOtp(999999, '123456');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('ok', $result);
        $this->assertFalse($result['ok']);
    }

    public function testCreateUserReturnsArrayOnMissingFields(): void
    {
        $result = AuthService::createUser([]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('ok', $result);
        $this->assertFalse($result['ok']);
        $this->assertArrayHasKey('error', $result);
    }

    public function testCreateUserReturnsArrayOnShortPassword(): void
    {
        $result = AuthService::createUser([
            'rol_id' => 1,
            'nombre_completo' => 'Test User',
            'nombre_usuario' => 'test_user_' . time(),
            'contrasena' => '12345',
        ]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('ok', $result);
        $this->assertFalse($result['ok']);
    }

    public function testRolAdminConstant(): void
    {
        $this->assertEquals('Administrador', AuthService::ROL_ADMIN);
    }

    public function testOtpTokenMinutesConstant(): void
    {
        $this->assertEquals(5, AuthService::OTP_TOKEN_MINUTES);
    }

    public function testOtpLimiteDiarioConstant(): void
    {
        $this->assertEquals(150, AuthService::OTP_LIMITE_DIARIO);
    }
}
