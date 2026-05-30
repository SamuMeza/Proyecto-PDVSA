<?php
require_once __DIR__ . '/../config/autoload.php';

use App\Core\App;
use App\Core\Session;
use App\Services\AuthService;

Session::start();

if (AuthService::check()) {
    header('Location: ' . App::BASE_PATH . '/public/index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $result = AuthService::login($username, $password);

    if ($result['ok'] && ($result['needs_otp'] ?? false)) {
        header('Location: ' . App::BASE_PATH . '/auth/otp_verify.php');
        exit;
    }
    $error = $result['error'] ?? 'Error desconocido.';
}

$pageTitle = 'Iniciar sesión';
$authCardWide = '';
require __DIR__ . '/../src/Views/layouts/auth.php';
require __DIR__ . '/../src/Views/auth/login.php';
