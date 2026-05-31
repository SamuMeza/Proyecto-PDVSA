<?php
require_once __DIR__ . '/../config/autoload.php';

use App\Core\App;
use App\Core\Session;
use App\Services\AuthService;
use App\Models\Role;

AuthService::requireAdmin();

$error = '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $data['creado_por'] = Session::get('usuario_id');
    $result = AuthService::createUser($data);
    if ($result['ok']) {
        $mensaje = 'Usuario creado correctamente.';
    } else {
        $error = $result['error'];
    }
}

$roles = Role::allActive();
$pageTitle = 'Registrar usuario';
$pageSlug = 'registrar';

require __DIR__ . '/../public/includes/layout.php';
require __DIR__ . '/../src/Views/auth/register.php';
require __DIR__ . '/../public/includes/layout_footer.php';
