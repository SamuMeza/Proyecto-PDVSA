<?php
require_once __DIR__ . '/../config/autoload.php';

use App\Controllers\AuthController;

$controller = new AuthController();
$controller->register();
