<?php
require_once __DIR__ . '/../config/autoload.php';

use App\Core\App;
use App\Core\Session;
use App\Services\AuthService;
use App\Models\User;

Session::start();

if (AuthService::check()) {
    header('Location: ' . App::BASE_PATH . '/public/index.php');
    exit;
}

$userId = Session::get('pending_otp_user_id');
if (!$userId) {
    header('Location: ' . App::BASE_PATH . '/auth/login.php');
    exit;
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
            header('Location: ' . App::BASE_PATH . '/public/index.php');
            exit;
        }
    }
    $error = $result['error'] ?? 'Error desconocido.';
}

$pageTitle = 'Verificación OTP';
$authCardWide = '';
require __DIR__ . '/../src/Views/layouts/auth.php';
require __DIR__ . '/../src/Views/auth/otp_verify.php';
