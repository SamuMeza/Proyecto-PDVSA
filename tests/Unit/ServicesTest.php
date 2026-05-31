<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ServicesTest extends TestCase
{
    public function testAuthServiceExists(): void
    {
        $this->assertTrue(class_exists(\App\Services\AuthService::class));
    }
}
