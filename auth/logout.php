<?php
require_once __DIR__ . '/auth_functions.php';

cerrarSesionUsuario();
header('Location: ' . BASE_PATH . '/auth/login.php');
exit;
