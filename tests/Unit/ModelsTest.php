<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ModelsTest extends TestCase
{
    public function testModelBaseStructure(): void
    {
        $this->assertTrue(class_exists(\App\Models\Model::class));
    }
}
