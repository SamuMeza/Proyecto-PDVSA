<?php
namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class PreventivoTest extends TestCase
{
    public function testPreventivaControllerExists(): void
    {
        $this->assertTrue(class_exists(\App\Controllers\PreventivaController::class));
    }
}
