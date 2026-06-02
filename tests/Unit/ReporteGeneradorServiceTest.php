<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ReporteGeneradorService;

class ReporteGeneradorServiceTest extends TestCase
{
    public function testReporteGeneradorServiceExists(): void
    {
        $this->assertTrue(class_exists(ReporteGeneradorService::class));
    }

    public function testContarRegistrosMethodExists(): void
    {
        $this->assertTrue(method_exists(ReporteGeneradorService::class, 'contarRegistros'));
    }

    public function testObtenerRegistrosMethodExists(): void
    {
        $this->assertTrue(method_exists(ReporteGeneradorService::class, 'obtenerRegistros'));
    }

    public function testGenerarReporteMethodExists(): void
    {
        $this->assertTrue(method_exists(ReporteGeneradorService::class, 'generarReporte'));
    }

    public function testObtenerHistorialMethodExists(): void
    {
        $this->assertTrue(method_exists(ReporteGeneradorService::class, 'obtenerHistorial'));
    }

    public function testEliminarReporteMethodExists(): void
    {
        $this->assertTrue(method_exists(ReporteGeneradorService::class, 'eliminarReporte'));
    }

    public function testLimpiarReportesAntiguosMethodExists(): void
    {
        $this->assertTrue(method_exists(ReporteGeneradorService::class, 'limpiarReportesAntiguos'));
    }
}
