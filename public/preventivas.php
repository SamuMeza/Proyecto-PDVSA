<?php
require_once __DIR__ . '/../config/autoload.php';

use App\Core\App;
use App\Core\Session;
use App\Core\Database;
use App\Services\AuthService;
use App\Services\PreventivaService;
use App\Models\Equipo;
use App\Models\User;
use App\Models\NivelMantenimiento;
use App\Models\OrdenPreventiva;
use App\Models\CategoriaEquipo;
use App\Helpers\SecurityHelper;

AuthService::requireAuth();

if (!PreventivaService::checkAccess()) {
    header('Location: ' . App::BASE_PATH . '/public/index.php?error=sin_permiso');
    exit;
}

$pdo = Database::connection();
$equipos = Equipo::allActive();
$categorias = CategoriaEquipo::allActive();
$planificadores = User::allPlanners();
$nivelesMantenimiento = NivelMantenimiento::allActive();

$puedeCrear = PreventivaService::canCreate();
$puedeEditar = PreventivaService::canEdit();
$puedeCambiarEstado = PreventivaService::canChangeState();

$error = '';
$mensaje = '';
$formData = PreventivaService::getFormData();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_preventiva'])) {
    $result = PreventivaService::save($_POST);
    if ($result['ok']) {
        $mensaje = $result['message'];
        $formData = ['id' => '', 'codigo_unico' => '', 'equipo_id' => '', 'nivel_mantenimiento_id' => '', 'fecha_planificada' => '', 'hora_inicio' => '', 'hora_fin' => '', 'estado' => 'planificada', 'planificador_id' => '', 'duracion_estimada_horas' => '', 'descripcion' => '', 'observaciones_mantenedor' => '', 'observaciones_supervisor' => '', 'motivo_suspension' => ''];
    } else {
        $error = $result['error'];
        $formData = array_merge($formData, $_POST);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_estado']) && $puedeCambiarEstado) {
    $result = PreventivaService::changeState((int) $_POST['cambiar_estado'], $_POST['nuevo_estado'] ?? '', trim($_POST['codigo_otp'] ?? ''));
    if ($result['ok']) {
        $mensaje = $result['message'];
    } else {
        $error = $result['error'];
    }
}

$ordenes = PreventivaService::listWithFilters();

$viewId = isset($_GET['view']) && is_numeric($_GET['view']) ? (int) $_GET['view'] : 0;
$viewDetalle = null;
if ($viewId > 0) {
    $viewDetalle = OrdenPreventiva::findWithDetails($viewId);
}

$pageTitle = 'Órdenes Preventivas';
$pageSlug = 'preventivas';
require __DIR__ . '/includes/layout.php';
require __DIR__ . '/../src/Views/preventivas/index.php';
require __DIR__ . '/includes/layout_footer.php';
