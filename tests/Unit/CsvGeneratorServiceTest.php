<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\CsvGeneratorService;

class CsvGeneratorServiceTest extends TestCase
{
    public function testCsvGeneratorServiceExists(): void
    {
        $this->assertTrue(class_exists(CsvGeneratorService::class));
    }

    public function testGenerarCsvMethodExists(): void
    {
        $this->assertTrue(method_exists(CsvGeneratorService::class, 'generarCsv'));
    }
}
