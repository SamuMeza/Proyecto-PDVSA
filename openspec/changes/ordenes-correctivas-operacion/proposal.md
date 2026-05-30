## Why

Las órdenes correctivas son críticas para el registro de fallas y la operación del mantenimiento. El módulo debe incluir catálogos, checklists, fotos y auditoría, con distintos niveles de permiso según el rol.

## What Changes

- **CRUD de órdenes correctivas**: Admin puede crear, leer, actualizar y eliminar; Supervisor y Programador pueden leer y actualizar; Mantenedor solo puede leer.
- **Catálogos iniciales**: `tipos_falla`, `prioridades_falla`, `categorias_equipo`, `zonas`.
- **Checklists y ejecuciones**: Registro de checklist items y estados de ejecución.
- **Fotos de correctivas**: Carga de hasta 3 fotos en JPG/PNG, con compresión opcional.
- **Auditoría completa**: Registrar creación, edición, cierre y cambios de estado en `logs_auditoria`.
- **Reportes en desarrollo**: Guardar registro y flujo mínimo, con PDF real pospuesto.

## Capabilities

### New Capabilities
- `ordenes-correctivas-crud`: Gestión de órdenes correctivas con roles diferenciados.
- `ordenes-correctivas-fotos`: Carga de hasta 3 fotos con validación de formato y compresión.
- `ordenes-correctivas-auditoria`: Registro de eventos críticos en un log de auditoría.
- `ordenes-correctivas-checklists`: Uso de checklists y ejecuciones asociadas.

### Modified Capabilities
- `reportes-fase-6`: Base para guardar peticiones de reporte aunque el PDF real quede para después.

## Impact

- **Base de datos**: Uso de tablas `ordenes_correctivas`, `fotos_correctivas`, `checklists`, `ejecucion_checklist_items`, `logs_auditoria`.
- **UI**: Formulario de órdenes correctivas, panel de lectura/edición y botones de auditoría.
- **Seguridad**: Distinción clara de permisos según rol para editar y ver.
