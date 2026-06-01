<?php
namespace App\Controllers;

use App\Core\App;
use App\Core\Response;
use App\Services\AuthService;

class DashboardController
{
    public function index(): void
    {
        AuthService::requireAuth();

        $error = $_GET['error'] ?? '';
        $pageTitle = 'Inicio';
        $pageSlug = 'index';

        Response::view('dashboard/index', [
            'error' => $error,
            'pageTitle' => $pageTitle,
            'pageSlug' => $pageSlug,
        ]);
    }
}
