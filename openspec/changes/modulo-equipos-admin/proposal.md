## Why

El sistema necesita un módulo de equipos que permita a los roles administrativos gestionar la infraestructura de mantenimiento sin perder trazabilidad. Solo Admin, Supervisor y Programador deben ver el listado de equipos, y el estado inactivo se debe mantener visible para auditoría y seguimiento.

## What Changes

- **CRUD de equipos**: Crear, ver, editar y desactivar equipos desde una interfaz administrativa.
- **Estado `inactivo`**: El equipo no se elimina físicamente; permanece visible en los listados con una marca de inactividad.
- **Filtros**: Lista de equipos filtrable por `familia`, `categoria`, `zona` y `estado`.
- **Visibilidad por rol**: Solo Admin, Supervisor y Programador pueden acceder al listado de equipos.
- **Campos básicos de prueba**: Se implementan campos de prueba útiles para el módulo basado en las tablas existentes y el esquema de equipos.

## Capabilities

### New Capabilities
- `equipos-admin-crud`: Gestión completa de equipos, con creación, edición y desactivación.
- `equipos-filtros`: Filtrado multi-criterio por familia, categoría, zona y estado.
- `equipos-inactivo-visible`: Registro y visualización de equipos inactivos en listados.

### Modified Capabilities
- `role-based-permissions`: Ajuste para limitar el acceso al módulo de equipos a Admin, Supervisor y Programador.

## Impact

- **Base de datos**: Uso de la tabla `tabla_equipos.md` y las relaciones con `categorias_equipo`, `zonas` y `familia`.
- **Interfaz**: Página de equipos con listado, filtros y formulario de edición/creación.
- **Permisos**: Validación de roles antes de mostrar o permitir acciones en el módulo.
