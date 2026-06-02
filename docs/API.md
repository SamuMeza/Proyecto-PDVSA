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
| `/reportes/contar` | POST | Contar registros para un tipo de reporte |
| `/reportes/generar` | POST | Generar reporte PDF y CSV |
| `/reportes/descargar/{id}` | GET | Descargar archivo PDF o CSV |
| `/reportes/historial` | GET | Obtener historial de reportes |
| `/reportes/eliminar/{id}` | POST | Eliminar un reporte |
| `/session/keepalive` | GET | Renovar sesión |

## Autenticación

Todas las rutas API requieren sesión activa (verificada por `AuthMiddleware`).

## Reportes

### POST /reportes/generar

Genera un reporte PDF y CSV con los filtros proporcionados.

**Parámetros:**
- `tipo_reporte` (string): Tipo de reporte ('fallas', 'cumplimiento', 'resumen-mensual', 'tecnicos')
- `filtros` (string): JSON con los filtros aplicados

**Respuesta exitosa:**
```json
{
  "ok": true,
  "reporte_id": 123,
  "pdf": {
    "ruta": "/storage/reportes-pdf/2026/06/fallas_2026-06-01_143025.pdf",
    "nombre": "fallas_2026-06-01_143025.pdf",
    "tamano": 12345,
    "duracion": 1234
  },
  "csv": {
    "ruta": "/storage/reportes-pdf/2026/06/fallas_2026-06-01_143025.csv",
    "nombre": "fallas_2026-06-01_143025.csv"
  },
  "registros": 23
}
```

**Respuesta de error:**
```json
{
  "ok": false,
  "error": "No se encontraron resultados con los filtros seleccionados"
}
```

### GET /reportes/descargar/{id}

Descarga un archivo PDF o CSV de un reporte generado.

**Parámetros:**
- `{id}` (int): ID del reporte
- `formato` (string, opcional): 'pdf' (por defecto) o 'csv'

**Headers de respuesta:**
- `Content-Type`: 'application/pdf' o 'text/csv; charset=utf-8'
- `Content-Disposition`: 'attachment; filename="nombre_archivo.ext"'
- `Content-Length`: Tamaño del archivo en bytes

### GET /reportes/historial

Obtiene el historial de reportes generados con filtros y paginación.

**Parámetros de consulta:**
- `tipo_reporte` (string, opcional): Filtrar por tipo de reporte
- `estado` (string, opcional): Filtrar por estado ('completado', 'error')
- `fecha_desde` (string, opcional): Fecha de inicio (formato YYYY-MM-DD)
- `fecha_hasta` (string, opcional): Fecha de fin (formato YYYY-MM-DD)
- `pagina` (int, opcional): Número de página (por defecto 1)

**Respuesta:**
```json
{
  "reportes": [...],
  "total": 23,
  "total_paginas": 2,
  "pagina_actual": 1
}
```

### POST /reportes/eliminar/{id}

Elimina un reporte y su archivo asociado.

**Parámetros:**
- `{id}` (int): ID del reporte

**Respuesta:**
```json
{
  "ok": true
}
```
