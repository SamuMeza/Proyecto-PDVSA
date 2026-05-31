<?php
namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class EquiposCrudTest extends TestCase
{
    public function testEquipoControllerExists(): void
    {
        $this->assertTrue(class_exists(\App\Controllers\EquipoController::class));
    }
}
