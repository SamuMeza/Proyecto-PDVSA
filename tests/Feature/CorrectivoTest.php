<?php
namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class CorrectivoTest extends TestCase
{
    public function testCorrectivaControllerExists(): void
    {
        $this->assertTrue(class_exists(\App\Controllers\CorrectivaController::class));
    }
}
