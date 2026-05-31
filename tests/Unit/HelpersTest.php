<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function testDateHelperExists(): void
    {
        $this->assertTrue(class_exists(\App\Helpers\DateHelper::class));
    }
}
