## Diseño del módulo de órdenes correctivas

### Modelo de datos

Las tablas principales son:
- `ordenes_correctivas`
- `tipos_falla`
- `prioridades_falla`
- `categorias_equipo`
- `zonas`
- `fotos_correctivas`
- `checklists`
- `ejecucion_checklist_items`
- `logs_auditoria`

### Roles y permisos

- Admin: crear, leer, actualizar, eliminar.
- Supervisor: leer y actualizar.
- Programador: leer y actualizar.
- Mantenedor: solo leer.

### Componentes clave

- Formulario de orden correctiva con catálogos.
- Sección de fotos con hasta 3 imágenes en JPG/PNG.
- Checklists asociados a la orden.
- Panel de auditoría con historial de cambios de estado.

### Auditoría y reportes

- Registrar creación, edición, cierre y cambios de estado.
- Guardar eventos en `logs_auditoria`.
- El reporte PDF real queda para una fase posterior; inicialmente basta el registro de la petición.
