<?php
namespace App\Controllers;

use App\Core\App;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;
use App\Models\User;
use App\Models\Role;

class AuthController
{
    public function login(): void
    {
        if (AuthService::check()) {
            Response::redirect(App::BASE_PATH . '/');
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $result = AuthService::login($username, $password);

            if ($result['ok'] && ($result['needs_otp'] ?? false)) {
                Response::redirect(App::BASE_PATH . '/otp');
            }
            $error = $result['error'] ?? 'Error desconocido.';
        }

        require dirname(__DIR__, 2) . '/src/Views/layouts/auth.php';
        require dirname(__DIR__, 2) . '/src/Views/auth/login.php';
    }

    public function otpVerify(): void
    {
        Session::start();
        if (AuthService::check()) {
            Response::redirect(App::BASE_PATH . '/');
        }

        $userId = Session::get('pending_otp_user_id');
        if (!$userId) {
            Response::redirect(App::BASE_PATH . '/login');
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['otp_code'] ?? '');
            $result = AuthService::verifyOtp((int) $userId, $code);
            if ($result['ok']) {
                $user = User::findWithRole((int) $userId);
                if ($user) {
                    AuthService::completeLogin($user);
                    Session::remove('pending_otp_user_id');
                    Session::remove('pending_otp_usuario');
                    Session::remove('pending_otp_expires');
                    Response::redirect(App::BASE_PATH . '/');
                }
            }
            $error = $result['error'] ?? 'Error desconocido.';
        }

        require dirname(__DIR__, 2) . '/src/Views/layouts/auth.php';
        require dirname(__DIR__, 2) . '/src/Views/auth/otp_verify.php';
    }

    public function logout(): void
    {
        AuthService::logout();
        Response::redirect(App::BASE_PATH . '/login');
    }

    public function register(): void
    {
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

        Response::view('auth/register', [
            'error' => $error,
            'mensaje' => $mensaje,
            'roles' => $roles,
            'pageTitle' => 'Registrar usuario',
            'pageSlug' => 'registrar',
        ]);
    }
}
