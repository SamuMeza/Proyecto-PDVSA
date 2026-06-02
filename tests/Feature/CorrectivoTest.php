<?php
namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use App\Controllers\CorrectivoController;
use App\Services\CorrectivaService;

class CorrectivoTest extends TestCase
{
    public function testCorrectivoControllerExists(): void
    {
        $this->assertTrue(class_exists(CorrectivoController::class));
    }

    public function testCorrectivaServiceExists(): void
    {
        $this->assertTrue(class_exists(CorrectivaService::class));
    }

    public function testCorrectivoControllerHasIndexMethod(): void
    {
        $this->assertTrue(method_exists(CorrectivoController::class, 'index'));
    }

    public function testCorrectivaServiceHasCheckAccessMethod(): void
    {
        $this->assertTrue(method_exists(CorrectivaService::class, 'checkAccess'));
    }

    public function testCorrectivaServiceHasChangeStateMethod(): void
    {
        $this->assertTrue(method_exists(CorrectivaService::class, 'changeState'));
    }
}
