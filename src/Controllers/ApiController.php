<?php
namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Middleware\AuthMiddleware;
use App\Models\Equipo;
use App\Models\Zona;
use App\Models\CategoriaEquipo;

class ApiController
{
    public function equipo(Request $request): void
    {
        AuthMiddleware::authenticated();
        $id = (int) ($request->body()['id'] ?? 0);
        $equipo = Equipo::findWithDetails($id);
        Response::json($equipo ?: []);
    }

    public function zonasByCategoria(Request $request): void
    {
        AuthMiddleware::authenticated();
        $categoriaId = (int) ($request->query()['categoria_id'] ?? 0);
        $zonas = [];
        if ($categoriaId > 0) {
            $equipos = Equipo::findWhere('categoria_id = ? AND estado = ?', [$categoriaId, 'activo']);
            $zonaIds = array_unique(array_column($equipos, 'zona_id'));
            foreach ($zonaIds as $zid) {
                $zona = Zona::find($zid);
                if ($zona) $zonas[] = $zona;
            }
        }
        Response::json($zonas);
    }

    public function searchEquipos(Request $request): void
    {
        AuthMiddleware::authenticated();
        $q = trim($request->query()['q'] ?? '');
        if (strlen($q) < 2) {
            Response::json([]);
            return;
        }
        $equipos = Equipo::raw(
            'SELECT id, numero_activo_fijo, nombre FROM equipos WHERE estado = ? AND (nombre LIKE ? OR numero_activo_fijo LIKE ?) LIMIT 20',
            ['activo', "%$q%", "%$q%"]
        );
        Response::json($equipos);
    }
}
