# Sistema Base Documentación

## Descripción

Documento que describe el estado actual del sistema PDVSA implementado hasta la fecha. Incluye autenticación, navegación, temas UI, conexión a base de datos y migración entre MySQL y PostgreSQL.

## Requisitos

- El sistema debe permitir iniciar sesión con un usuario activo definido en la tabla `usuarios`.
- El registro de usuarios debe estar restringido al rol `Administrador`.
- Todas las páginas de `public/` deben requerir sesión activa y compartir un layout común.
- Debe existir un toggle de tema claro/oscuro y un menú lateral colapsable.
- Debe existir un archivo de conexión `config/db.php` compatible con MySQL y PostgreSQL.
- Debe existir un script de migración `scripts/migrate_mysql_to_postgresql.php`.
- El esquema actual debe incluir al menos las tablas `roles` y `usuarios` en MySQL y PostgreSQL.

## Observaciones

- El estado actual no incluye el esquema completo de 24 tablas descrito en las fases del proyecto.
- El estado actual no incluye la autenticación OTP descrita en las propuestas de diseño.
- Este documento se usará como referencia para continuar con las fases de `esquema-bd-completo`, `modulo-equipos-admin` y `ordenes-preventivas-base`.
