<?php
require_once __DIR__ . '/../config/autoload.php';

use App\Core\App;
use App\Services\AuthService;

AuthService::logout();
header('Location: ' . App::BASE_PATH . '/auth/login.php');
exit;
