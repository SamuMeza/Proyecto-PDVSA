<?php
namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

class OrdenesFlowTest extends TestCase
{
    public function testPreventivaServiceExists(): void
    {
        $this->assertTrue(class_exists(\App\Services\PreventivaService::class));
    }

    public function testCorrectivaServiceExists(): void
    {
        $this->assertTrue(class_exists(\App\Services\CorrectivaService::class));
    }
}
