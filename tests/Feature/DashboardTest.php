<?php
namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class DashboardTest extends TestCase
{
    public function testDashboardControllerExists(): void
    {
        $this->assertTrue(class_exists(\App\Controllers\DashboardController::class));
    }
}
