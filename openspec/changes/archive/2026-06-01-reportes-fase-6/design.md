## Diseño del Módulo de Reportes - Fase 6

### Arquitectura General

```
┌─────────────────────────────────────────────────────────────────┐
│                        PRESENTATION LAYER                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌────────────────┐  │
│  │ Dashboard       │  │ Generar Reporte │  │ Historial      │  │
│  │ (ReporteCtrl)   │  │ (ReporteGenCtrl)│  │ (ReporteGenCtrl│  │
│  │ /reportes       │  │ /reportes/      │  │ /reportes/     │  │
│  │                 │  │  generar        │  │  historial     │  │
│  └─────────────────┘  └─────────────────┘  └────────────────┘  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────┐
│                        SERVICE LAYER                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────────┐  ┌──────────────────┐  ┌───────────────┐ │
│  │ ReporteService    │  │ ReporteGenerador │  │ PdfGenerator  │ │
│  │ (stats, queries)  │  │ Service          │  │ Service       │ │
│  │                   │  │ (filtros, CRUD)  │  │ (TCPDF)       │ │
│  └──────────────────┘  └──────────────────┘  └───────────────┘ │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────┐
│                        DATA LAYER                               │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────────┐  ┌──────────────────┐  ┌───────────────┐ │
│  │ reportes_        │  │ ordenes_         │  │ storage/      │ │
│  │ generados        │  │ preventivas      │  │ reportes-pdf/ │ │
│  │ (BD)             │  │ correctivas      │  │ (archivos)    │ │
│  └──────────────────┘  └──────────────────┘  └───────────────┘ │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Modelo de Datos

#### Tabla `reportes_generados` (Modificada)

```sql
CREATE TABLE reportes_generados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_reporte VARCHAR(50) NOT NULL,           -- 'fallas', 'cumplimiento', 'resumen-mensual', 'tecnicos'
    formato VARCHAR(10) DEFAULT 'pdf',           -- 'pdf', 'csv'
    generado_por_usuario_id INT NOT NULL,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    parametros_filtros_json JSON NULL,           -- Filtros aplicados
    ruta_archivo VARCHAR(255) NOT NULL,          -- Ruta en storage/
    nombre_archivo_descarga VARCHAR(255) NOT NULL, -- Nombre para descarga
    estado VARCHAR(20) DEFAULT 'completado',     -- 'generando', 'completado', 'error'
    tamano_bytes INT NULL,                       -- Tamaño del archivo
    duracion_ms INT NULL,                        -- Tiempo de generación
    mensaje_error TEXT NULL,                     -- Detalle del error si falla
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- FK y indexes...
);
```

#### Estructura de Filtros JSON

Cada tipo de reporte genera un JSON específico:

```json
// Fallas
{
    "tipo_reporte": "fallas",
    "filtros": {
        "tipo_falla_id": 3,
        "zona_id": 1,
        "prioridad_id": null,
        "estado": "reportada",
        "mantenedor_id": null,
        "fecha_desde": "2026-01-01",
        "fecha_hasta": "2026-06-01"
    }
}

// Cumplimiento
{
    "tipo_reporte": "cumplimiento",
    "filtros": {
        "tipo_orden": "ambas",           // 'preventivas', 'correctivas', 'ambas'
        "estado": null,
        "zona_id": null,
        "nivel_mantenimiento_id": null,
        "fecha_desde": "2026-01-01",
        "fecha_hasta": "2026-06-01"
    }
}

// Resumen Mensual
{
    "tipo_reporte": "resumen-mensual",
    "filtros": {
        "mes_desde": "2026-01",
        "mes_hasta": "2026-06",
        "zona_id": null,
        "nivel_mantenimiento_id": null
    }
}

// Técnicos
{
    "tipo_reporte": "tecnicos",
    "filtros": {
        "mantenedor_id": null,
        "estado": null,
        "zona_id": null,
        "fecha_desde": "2026-01-01",
        "fecha_hasta": "2026-06-01"
    }
}
```

### Arquitectura de Filtros (Opción C)

El formulario es 100% visual para el usuario. El JSON se genera internamente con JavaScript:

```
USUARIO LLENA FORMULARIO         JAVASCRIPT GENERA JSON         BACKEND RECIBE
┌─────────────────────┐         ┌─────────────────────┐        ┌────────────────┐
│ Tipo: [Fallas ▼]    │   ───▶  │ {                   │  ───▶  │ $filtros =     │
│ Zona: [Zona A ▼]    │         │   "tipo_reporte":   │        │ json_decode()  │
│ Desde: [01/01/2026]  │         │    "fallas",        │        │                │
│ Hasta: [01/06/2026]  │         │   "filtros": {      │        │ Construir query│
│ [Generar PDF]        │         │     "zona_id": 1,   │        │ con filtros    │
└─────────────────────┘         │     ...             │        └────────────────┘
                                │   }                 │
                                │ }                   │
                                └─────────────────────┘
```

**Extensibilidad**: Para agregar un filtro nuevo (ej: "Prioridad"), solo se agrega un `<select>` con `data-filter="prioridad_id"` en el HTML. El JSON se genera automáticamente. El Service lee el JSON y agrega `WHERE` si existe la key.

### Template del PDF

```
┌─────────────────────────────────────────────────────────────┐
│  [LOGO PDVSA]                                               │
│  Sistema de Mantenimiento PDVSA Punta Mata                 │
│  ─────────────────────────────────────────────────────────  │
│                                                             │
│  Reporte: Estadísticas de Fallas                            │
│  Generado: 01/06/2026 14:30:25                              │
│  Por: Juan García (Administrador)                           │
│  ─────────────────────────────────────────────────────────  │
│  Filtros aplicados:                                         │
│    • Tipo de falla: Mecánica                                │
│    • Zona: Zona A                                           │
│    • Rango: 01/01/2026 - 01/06/2026                         │
│  ─────────────────────────────────────────────────────────  │
│                                                             │
│  ┌──────┬────────────┬──────────┬─────────┬──────────┐     │
│  │ #    │ Código     │ Equipo   │ Fecha   │ Estado   │     │
│  ├──────┼────────────┼──────────┼─────────┼──────────┤     │
│  │ 1    │ OTC-001    │ Equipo A │ 15/01   │ Cerrada  │     │
│  │ 2    │ OTC-002    │ Equipo B │ 22/02   │ Abierta  │     │
│  │ ...  │ ...        │ ...      │ ...     │ ...      │     │
│  └──────┴────────────┴──────────┴─────────┴──────────┘     │
│                                                             │
│  Total: 23 registros                                        │
│  ─────────────────────────────────────────────────────────  │
│  Página 1 de 2                    Sistema PDVSA v1.0        │
└─────────────────────────────────────────────────────────────┘

Configuración TCPDF:
├── Tamaño: CARTA (Letter) - 215.9mm × 279.4mm
├── Orientación: Vertical (Portrait)
├── Márgenes: 15mm izquierda, 10mm derecha, 15mm top/bottom
├── Fuente: DejaVu Sans (soporta UTF-8, acentos, ñ)
├── Header: Logo + Título del sistema
├── Footer: Paginación + "Generado por [usuario]"
└── Encoding: UTF-8
```

### CSV - Especificación

```
FORMATO:
├── Separador: punto y coma (;)  ← Excel en Venezuela usa ; por defecto
├── Encoding: UTF-8 con BOM (Byte Order Mark) ← Sin BOM Excel no lee acentos
├── Primera fila: Encabezados de columna
├── Nombre: [tipo_reporte]_[fecha]_[hora].csv
└── Ejemplo: fallas_2026-06-01_143025.csv

CONTENIDO:
├── Todos los registros (sin límite)
├── Mismos campos que la tabla del PDF
├── Datos crudos para análisis en Excel
└── Sin formato visual (solo datos)
```

### Flujo de Generación

```
1. USUARIO ACCEDE A /reportes/generar
   │
   ▼
2. SELECCIONA TIPO DE REPORTE
   │
   ▼
3. FORMULARIO MUESTRA FILTROS ESPECÍFICOS
   │
   ▼
4. AL CAMBIAR CUALQUIER FILTRO
   │   ├── JS actualiza JSON interno
   │   └── JS cuenta registros (AJAX)
   │
   ▼
5. USUARIO CLICA "Generar Reporte"
   │
   ▼
6. CONTROLLER RECIBE POST CON JSON
   │
   ▼
7. VALIDAR FILTROS
   │   ├── Si 0 resultados → "No se encontraron resultados"
   │   └── Si hay datos → continuar
   │
   ▼
8. CREAR REGISTRO EN BD (estado = 'generando')
   │
   ▼
9. GENERAR PDF CON TCPDF
   │   ├── Si error → log + estado 'error' + mensaje_error
   │   └── Si éxito → continuar
   │
   ▼
10. GUARDAR ARCHIVO EN storage/reportes-pdf/
   │
   ▼
11. ACTUALIZAR BD (estado = 'completado', tamano_bytes, duracion_ms)
   │
   ▼
12. GENERAR CSV COMPLEMENTARIO
   │
   ▼
13. RESPONDER CON URLs DE DESCARGA
   │
   ▼
14. USUARIO DESCARGA PDF Y/O CSV
```

### Endpoint de Descarga

```
GET /reportes/descargar/{id}?formato=pdf

FLUJO:
1. Verificar autenticación (AuthService::requireAuth())
2. Verificar permiso (reportes, ver)
3. Buscar registro en reportes_generados por ID
4. Verificar que el usuario tiene acceso (admin/supervisor ven todos)
5. Verificar que el archivo existe en disco
6. Enviar Headers:
   - Content-Type: application/pdf
   - Content-Disposition: attachment; filename="nombre_reporte.pdf"
   - Content-Length: tamaño_bytes
7. Leer y enviar el archivo
8. NUNCA exponer la ruta real del archivo
```

### Seguridad

```
AUTENTICACIÓN:
├── Todos los endpoints requieren login
├── Endpoint de descarga verifica auth por cada request

AUTORIZACIÓN:
├── Admin: Puede generar y ver reportes de todos
├── Supervisor: Puede generar y ver reportes de todos
├── Técnico: Solo ve sus propios reportes (futuro)

PROTECCIÓN DE ARCHIVOS:
├── storage/ está FUERA de public/
├── .htaccess bloquea acceso directo
├── Única forma de acceder: endpoint /reportes/descargar/{id}
└── El endpoint verifica permisos antes de servir el archivo
```

### Manejo de Errores

```
ESTADO EN BD:
├── 'generando'  → Proceso en curso
├── 'completado' → PDF generado exitosamente
└── 'error'      → Falló la generación

ERRORES POSIBLES:
├── 0 resultados → Mensaje "No se encontraron resultados"
├── Error en query → Log + "Error al generar el reporte"
├── Error TCPDF → Log + estado 'error' + limpiar archivo parcial
├── Sin permisos → Redirigir a dashboard
└── Archivo no encontrado → 404

LOGGING:
├── errores.log: Error completo con stack trace
├── app.log: Información general del proceso
└── El usuario NUNCA ve errores técnicos
```

### Limpieza Automática

```
TRIGGER: Al generar un nuevo reporte
ACCIÓN: DELETE FROM reportes_generados WHERE creado_en < NOW() - INTERVAL 90 DAY
EFECTO: Borra registros viejos + archivos asociados del disco
VENTAJA: No necesita cron job, funciona en XAMPP sin configuración
```

### Estructura de Archivos

```
src/
├── Controllers/
│   ├── ReporteController.php           (SIN CAMBIOS - dashboards)
│   └── ReporteGeneradorController.php  (NUEVO - generación)
├── Services/
│   ├── ReporteService.php              (SIN CAMBIOS - stats)
│   ├── ReporteGeneradorService.php     (NUEVO - lógica de generación)
│   └── PdfGeneratorService.php         (NUEVO - TCPDF wrapper)
├── Views/reportes/
│   ├── index.php                       (SIN CAMBIOS - dashboard)
│   ├── cumplimiento.php                (SIN CAMBIOS)
│   ├── fallas.php                      (SIN CAMBIOS)
│   ├── resumen-mensual.php             (SIN CAMBIOS)
│   ├── tecnicos.php                    (SIN CAMBIOS)
│   ├── generar.php                     (NUEVO - formulario de filtros)
│   └── historial.php                   (NUEVO - tabla de solicitudes)
└── js/
    └── reportes.js                     (NUEVO - lógica de filtros)

storage/
└── reportes-pdf/                       (NUEVO - archivos generados)
    ├── .htaccess                       (NUEVO - bloquear acceso directo)
    └── {año}/{mes}/                    (NUEVO - organización por fecha)

database/
└── migrations/
    └── 029_alter_reportes_generados_add_status_fields.sql (NUEVO)
```
