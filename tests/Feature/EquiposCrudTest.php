<?php
namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use App\Controllers\EquipoController;
use App\Services\EquipoService;
use App\Models\Equipo;

class EquiposCrudTest extends TestCase
{
    public function testEquipoControllerExists(): void
    {
        $this->assertTrue(class_exists(EquipoController::class));
    }

    public function testEquipoServiceExists(): void
    {
        $this->assertTrue(class_exists(EquipoService::class));
    }

    public function testEquipoModelExists(): void
    {
        $this->assertTrue(class_exists(Equipo::class));
    }

    public function testEquipoControllerHasIndexMethod(): void
    {
        $this->assertTrue(method_exists(EquipoController::class, 'index'));
    }

    public function testEquipoServiceHasCheckAccessMethod(): void
    {
        $this->assertTrue(method_exists(EquipoService::class, 'checkAccess'));
    }

    public function testEquipoServiceHasCanCreateMethod(): void
    {
        $this->assertTrue(method_exists(EquipoService::class, 'canCreate'));
    }

    public function testEquipoServiceHasCanEditMethod(): void
    {
        $this->assertTrue(method_exists(EquipoService::class, 'canEdit'));
    }

    public function testEquipoServiceHasCanDeactivateMethod(): void
    {
        $this->assertTrue(method_exists(EquipoService::class, 'canDeactivate'));
    }

    public function testEquipoServiceHasSaveMethod(): void
    {
        $this->assertTrue(method_exists(EquipoService::class, 'save'));
    }

    public function testEquipoServiceHasToggleStatusMethod(): void
    {
        $this->assertTrue(method_exists(EquipoService::class, 'toggleStatus'));
    }

    public function testEquipoServiceHasListWithFiltersMethod(): void
    {
        $this->assertTrue(method_exists(EquipoService::class, 'listWithFilters'));
    }

    public function testEquipoServiceHasGetFormDataMethod(): void
    {
        $this->assertTrue(method_exists(EquipoService::class, 'getFormData'));
    }

    public function testEquipoModelHasAllActiveMethod(): void
    {
        $this->assertTrue(method_exists(Equipo::class, 'allActive'));
    }

    public function testEquipoModelHasFamiliesMethod(): void
    {
        $this->assertTrue(method_exists(Equipo::class, 'families'));
    }
}
