<?php
namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use App\Controllers\PreventivoController;
use App\Services\PreventivaService;
use App\Models\OrdenPreventiva;

class PreventivoTest extends TestCase
{
    public function testPreventivoControllerExists(): void
    {
        $this->assertTrue(class_exists(PreventivoController::class));
    }

    public function testPreventivaServiceExists(): void
    {
        $this->assertTrue(class_exists(PreventivaService::class));
    }

    public function testOrdenPreventivaModelExists(): void
    {
        $this->assertTrue(class_exists(OrdenPreventiva::class));
    }

    public function testPreventivoControllerHasIndexMethod(): void
    {
        $this->assertTrue(method_exists(PreventivoController::class, 'index'));
    }

    public function testPreventivaServiceHasCheckAccessMethod(): void
    {
        $this->assertTrue(method_exists(PreventivaService::class, 'checkAccess'));
    }

    public function testPreventivaServiceHasCanCreateMethod(): void
    {
        $this->assertTrue(method_exists(PreventivaService::class, 'canCreate'));
    }

    public function testPreventivaServiceHasCanEditMethod(): void
    {
        $this->assertTrue(method_exists(PreventivaService::class, 'canEdit'));
    }

    public function testPreventivaServiceHasCanChangeStateMethod(): void
    {
        $this->assertTrue(method_exists(PreventivaService::class, 'canChangeState'));
    }

    public function testPreventivaServiceHasSaveMethod(): void
    {
        $this->assertTrue(method_exists(PreventivaService::class, 'save'));
    }

    public function testPreventivaServiceHasChangeStateMethod(): void
    {
        $this->assertTrue(method_exists(PreventivaService::class, 'changeState'));
    }

    public function testPreventivaServiceHasListWithFiltersMethod(): void
    {
        $this->assertTrue(method_exists(PreventivaService::class, 'listWithFilters'));
    }

    public function testPreventivaServiceHasGetFormDataMethod(): void
    {
        $this->assertTrue(method_exists(PreventivaService::class, 'getFormData'));
    }

    public function testOrdenPreventivaHasFindWithDetailsMethod(): void
    {
        $this->assertTrue(method_exists(OrdenPreventiva::class, 'findWithDetails'));
    }

    public function testOrdenPreventivaHasAllowedTransitionsMethod(): void
    {
        $this->assertTrue(method_exists(OrdenPreventiva::class, 'allowedTransitions'));
    }
}
