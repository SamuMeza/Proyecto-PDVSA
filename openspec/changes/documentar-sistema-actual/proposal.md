## Why

El sistema actual ya tiene una base funcional de autenticación, navegación y conexión a base de datos, pero está incompleto respecto a la documentación y al alcance proyectado. Esta propuesta documenta el estado real del proyecto para que los desarrolladores tengan una referencia clara de qué está implementado y qué queda pendiente.

## What Changes

- Documentar la estructura de carpetas del proyecto: `auth/`, `css/`, `public/`, `config/`, `sql/`, `scripts/`.
- Documentar el flujo de autenticación: login, registro de usuarios restringido a Administrador, cierre de sesión, sesiones persistentes en BD.
- Documentar el layout y la navegación principal: sidebar izquierdo, toggle de tema claro/oscuro, páginas públicas con título y placeholders.
- Documentar el esquema de base de datos actual: solo `roles` y `usuarios` en MySQL y PostgreSQL.
- Documentar el script de migración MySQL → PostgreSQL.

## Capabilities

### New Capabilities
- `auth-admin-registration`: Registro de usuarios accesible solo para el rol Administrador.
- `base-ui-layout`: Layout común con sidebar izquierdo, navegación y tema claro/oscuro.
- `db-connection-basic`: Conexión a base de datos MySQL y soporte opcional para PostgreSQL.
- `db-migration-script`: Script de migración de datos de MySQL a PostgreSQL.

## Impact

- La documentación permitirá contrastar lo implementado con las propuestas OpenSpec existentes.
- Facilita la planificación de los próximos pasos: OTP, esquema completo de 24 tablas y módulos operativos.
- Proporciona una base para archivar el estado actual del proyecto en OpenSpec.
