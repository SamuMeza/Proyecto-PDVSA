<?php
namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Middleware\AuthMiddleware;
use App\Models\ConfiguracionSistema;

class ConfiguracionController
{
    public function parametros(Request $request): void
    {
        AuthMiddleware::admin();
        $configs = ConfiguracionSistema::findAll('clave');
        Response::view('configuracion/parametros', [
            'title' => 'Parámetros del Sistema',
            'configs' => $configs,
        ]);
    }

    public function update(Request $request): void
    {
        AuthMiddleware::admin();
        $data = $request->body();
        foreach ($data as $clave => $valor) {
            if (str_starts_with($clave, 'config_')) {
                $key = substr($clave, 7);
                ConfiguracionSistema::set($key, $valor);
            }
        }
        header('Location: ' . App::BASE_PATH . '/configuracion/parametros?actualizado=1');
    }

    public function trazas(Request $request): void
    {
        AuthMiddleware::admin();
        Response::view('configuracion/trazas', [
            'title' => 'Libro de Trazas',
        ]);
    }
}
