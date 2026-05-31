# API del Sistema

## Endpoints Internos (AJAX)

| Ruta | Método | Propósito |
|------|--------|-----------|
| `/api/equipo` | GET | Datos de un equipo |
| `/api/equipos/search` | GET | Búsqueda de equipos |
| `/api/zonas-by-categoria` | GET | Zonas filtradas por categoría |
| `/api/alertas` | GET | Alertas pendientes del usuario |
| `/api/alertas/count` | GET | Contador de alertas |
| `/api/alertas/mark-read` | POST | Marcar alerta como leída |
| `/session/keepalive` | GET | Renovar sesión |

## Autenticación

Todas las rutas API requieren sesión activa (verificada por `AuthMiddleware`).
