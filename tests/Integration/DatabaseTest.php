<?php
namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testConnectionAvailable(): void
    {
        $this->assertTrue(method_exists(\App\Core\Database::class, 'connection'));
    }
}
