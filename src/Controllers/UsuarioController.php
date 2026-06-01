<?php
namespace App\Controllers;

use App\Core\App;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\User;
use App\Models\Role;
use App\Services\AuthService;
use App\Helpers\SecurityHelper;

class UsuarioController
{
    private const PER_PAGE = 15;

    public function index(array $params = []): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('usuarios', 'ver');

        $search = trim($_GET['search'] ?? '');
        $filtroRol = (int) ($_GET['rol'] ?? 0);
        $filtroEstado = $_GET['estado'] ?? '';
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $where = ['1=1'];
        $params = [];

        if ($search !== '') {
            $where[] = '(nombre_usuario LIKE ? OR nombre_completo LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if ($filtroRol > 0) {
            $where[] = 'rol_id = ?';
            $params[] = $filtroRol;
        }

        if ($filtroEstado !== '' && in_array($filtroEstado, ['activo', 'inactivo'])) {
            $where[] = 'estado = ?';
            $params[] = $filtroEstado;
        }

        $whereSql = implode(' AND ', $where);

        $total = User::count($whereSql, $params);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * self::PER_PAGE;

        $usuarios = User::raw(
            "SELECT u.*, r.nombre AS rol_nombre
             FROM usuarios u
             INNER JOIN roles r ON r.id = u.rol_id
             WHERE {$whereSql}
             ORDER BY u.nombre_completo
             LIMIT " . self::PER_PAGE . " OFFSET {$offset}",
            $params
        );

        $roles = Role::allActive();

        Response::view('usuarios/index', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'search' => $search,
            'filtroRol' => $filtroRol,
            'filtroEstado' => $filtroEstado,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'pageTitle' => 'Usuarios',
            'pageSlug' => 'usuarios',
        ]);
    }

    public function create(array $params = []): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('usuarios', 'crear');

        $error = '';
        $success = '';
        $roles = Role::allActive();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = AuthService::createUser($_POST + ['creado_por' => Session::get('usuario_id')]);
            if ($result['ok']) {
                $success = 'Usuario creado exitosamente.';
                $_POST = [];
            } else {
                $error = $result['error'];
            }
        }

        Response::view('usuarios/create', [
            'error' => $error,
            'success' => $success,
            'roles' => $roles,
            'pageTitle' => 'Crear Usuario',
            'pageSlug' => 'usuarios',
        ]);
    }

    public function edit(array $params = []): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('usuarios', 'editar');

        $id = (int) ($params['id'] ?? $_GET['id'] ?? 0);
        $usuario = User::find($id);
        if (!$usuario) {
            Response::notFound();
        }

        $error = '';
        $success = '';
        $roles = Role::allActive();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
            $rolId = (int) ($_POST['rol_id'] ?? 0);
            $estado = $_POST['estado'] ?? $usuario['estado'];
            $nuevaContrasena = trim($_POST['nueva_contrasena'] ?? '');
            $confirmarContrasena = trim($_POST['confirmar_contrasena'] ?? '');

            if ($nombreCompleto === '') {
                $error = 'El nombre completo es obligatorio.';
            } elseif (!Role::existsActive($rolId)) {
                $error = 'Rol no válido.';
            } elseif ($nuevaContrasena !== '' && strlen($nuevaContrasena) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres.';
            } elseif ($nuevaContrasena !== '' && $nuevaContrasena !== $confirmarContrasena) {
                $error = 'Las contraseñas no coinciden.';
            } else {
                $updates = [
                    'nombre_completo' => $nombreCompleto,
                    'rol_id' => $rolId,
                    'estado' => in_array($estado, ['activo', 'inactivo']) ? $estado : $usuario['estado'],
                    'actualizado_en' => date('Y-m-d H:i:s'),
                ];
                if ($nuevaContrasena !== '') {
                    $updates['contrasena_hash'] = SecurityHelper::hashPassword($nuevaContrasena);
                }
                User::update($id, $updates);
                $usuario = User::find($id);
                $success = 'Usuario actualizado exitosamente.';
            }
        }

        Response::view('usuarios/edit', [
            'usuario' => $usuario,
            'error' => $error,
            'success' => $success,
            'roles' => $roles,
            'pageTitle' => 'Editar Usuario',
            'pageSlug' => 'usuarios',
        ]);
    }

    public function toggleStatus(array $params = []): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('usuarios', 'editar');

        $id = (int) ($_POST['id'] ?? 0);
        $usuario = User::find($id);
        if (!$usuario) {
            Response::notFound();
        }

        $nuevoEstado = $usuario['estado'] === 'activo' ? 'inactivo' : 'activo';
        User::update($id, [
            'estado' => $nuevoEstado,
            'actualizado_en' => date('Y-m-d H:i:s'),
        ]);

        Response::redirect(App::BASE_PATH . '/usuarios');
    }

    public function roles(array $params = []): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('usuarios', 'ver');

        $roles = Role::findAll('nombre');

        Response::view('usuarios/roles', [
            'roles' => $roles,
            'pageTitle' => 'Roles y Permisos',
            'pageSlug' => 'usuarios',
        ]);
    }
}
