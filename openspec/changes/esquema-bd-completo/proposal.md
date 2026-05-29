## Why

Actualmente, solo existe un esquema de base de datos básico con dos tablas (`roles` y `usuarios`). Para soportar las funcionalidades de mantenimiento planeadas, es necesario implementar el esquema completo que cubre los 24 modelos documentados en `tablas/`.

## What Changes

- **Implementación del Esquema Completo**: Se añadirán las 22 tablas restantes definidas en `tablas/` a los scripts SQL (`schema_mysql.sql` y `schema_postgresql.sql`).
- **Relaciones y Restricciones**: Definición de llaves foráneas e integridad referencial en el orden correcto.
- **Campos clave actualizados**:
  - `categorias_equipo` incluye `familia` con valores de prueba como `automatizacion`, `servicio_social_infraestructura`, `infraestructura`.
  - `ordenes_preventivas` incluye `hora_inicio` y `hora_fin` para representar el bloque de mantenimiento.
  - `nombre_usuario` pasa a `VARCHAR(150)` y se mantiene único.
  - `email` de usuarios se genera como `nombre_usuario@pdvsa.com` y se guarda en la base de datos.
  - `permisos_json` se define como JSON estructurado por módulo y acción.

## Capabilities

### New Capabilities
- `db-schema-full`: Implementación completa de las 24 tablas del sistema documentadas en `tablas/`.
- `equipos-categorias-familia`: Definición de familia en `categorias_equipo` para soportar filtros y colores en el calendario.
- `preventivas-horarios`: Bloques preventivos con `hora_inicio` y `hora_fin`.
- `permisos-json-schema`: Estructura de permisos basada en JSON por módulo y acción.

### Modified Capabilities

## Impact

- **SQL MySQL y PostgreSQL**: Se actualizan ambos scripts para incluir todas las tablas y sus índices.
- **Datos de referencia**: Se agregan valores de prueba para `familia` y `color_calendario` en `categorias_equipo`.
- **Migración de datos**: Permite avanzar en los módulos de calendario, equipos y órdenes preventivas.
