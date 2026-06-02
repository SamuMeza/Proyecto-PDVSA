<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\PdfGeneratorService;

class PdfGeneratorServiceTest extends TestCase
{
    public function testPdfGeneratorServiceExists(): void
    {
        $this->assertTrue(class_exists(PdfGeneratorService::class));
    }

    public function testGenerarPdfMethodExists(): void
    {
        $this->assertTrue(method_exists(PdfGeneratorService::class, 'generarPdf'));
    }
}
