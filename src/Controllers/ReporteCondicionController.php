<?php
namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Middleware\AuthMiddleware;

class ReporteCondicionController
{
    public function index(Request $request): void
    {
        AuthMiddleware::authenticated();
        Response::view('correctivo/condicion', [
            'title' => 'Reportes de Condición',
        ]);
    }

    public function store(Request $request): void
    {
        AuthMiddleware::authenticated();
        $data = $request->body();
        header('Location: ' . App::BASE_PATH . '/correctivas?reporte_condicion_creado=1');
    }
}
