<?php
require_once __DIR__ . '/../config/autoload.php';

use App\Core\App;
use App\Core\Session;
use App\Core\Database;
use App\Services\AuthService;
use App\Services\EquipoService;
use App\Models\CategoriaEquipo;
use App\Models\Zona;
use App\Models\Equipo;

AuthService::requireAuth();

if (!EquipoService::checkAccess()) {
    header('Location: ' . App::BASE_PATH . '/public/index.php?error=sin_permiso');
    exit;
}

$pdo = Database::connection();
$categorias = CategoriaEquipo::allActive();
$zonas = Zona::allActive();
$familias = Equipo::families();

$puedeCrear = EquipoService::canCreate();
$puedeEditar = EquipoService::canEdit();
$puedeDesactivar = EquipoService::canDeactivate();

$error = '';
$mensaje = '';
$formData = EquipoService::getFormData();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_equipo'])) {
    $result = EquipoService::save($_POST, (int) Session::get('usuario_id'));
    if ($result['ok']) {
        $mensaje = $result['message'];
        $formData = ['equipo_id' => '', 'nombre' => '', 'familia' => '', 'categoria_id' => '', 'zona_id' => '', 'estado' => 'activo', 'descripcion' => ''];
    } else {
        $error = $result['error'];
        $formData = [
            'equipo_id' => $_POST['equipo_id'] ?? '',
            'nombre' => trim($_POST['nombre'] ?? ''),
            'familia' => trim($_POST['familia'] ?? ''),
            'categoria_id' => (int) ($_POST['categoria_id'] ?? 0),
            'zona_id' => (int) ($_POST['zona_id'] ?? 0),
            'estado' => in_array($_POST['estado'] ?? 'activo', ['activo', 'inactivo'], true) ? $_POST['estado'] : 'activo',
            'descripcion' => trim($_POST['descripcion'] ?? ''),
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_estado']) && $puedeDesactivar) {
    $result = EquipoService::toggleStatus((int) $_POST['toggle_estado']);
    if ($result) {
        $mensaje = $result === 'inactivo' ? 'Equipo desactivado.' : 'Equipo activado.';
    }
}

$equipos = EquipoService::listWithFilters();

$pageTitle = 'Equipos';
$pageSlug = 'equipos';
require __DIR__ . '/includes/layout.php';
require __DIR__ . '/../src/Views/equipos/index.php';
require __DIR__ . '/includes/layout_footer.php';
