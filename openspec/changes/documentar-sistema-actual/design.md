## System Overview

El sistema está construido con PHP y usa PDO para la base de datos. La aplicación está organizada en carpetas por responsabilidad:

- `auth/` → lógica de acceso: login, registro, logout y helpers de sesión.
- `public/` → páginas de la aplicación que requieren sesión activa.
- `css/` → estilos y scripts de interacción de la barra lateral y el tema.
- `config/` → conexión a la base de datos con soporte MySQL y PostgreSQL.
- `sql/` → scripts de esquema y seeds.
- `scripts/` → migración de MySQL a PostgreSQL.

## Authentication Flow

1. El usuario abre `auth/login.php`.
2. El sistema valida `nombre_usuario` y `contrasena` contra `usuarios` activos.
3. Si es correcto, se inicia sesión PHP y se registra un token en la tabla `usuarios`.
4. El registro de usuarios solo está disponible para el rol `Administrador` y requiere sesión activa.
5. Cierre de sesión via `auth/logout.php`.

## UI Layout

- Todas las páginas en `public/` incluyen `public/includes/layout.php` y `public/includes/layout_footer.php`.
- El layout ofrece:
  - sidebar izquierdo con navegación.
  - botón para ocultar/mostrar el menú.
  - botón para alternar tema claro/oscuro.
- Las páginas de `public/` son placeholders con un título principal y contenido en desarrollo.

## Database

- El esquema actual define solo dos tablas: `roles` y `usuarios`.
- El seed de administrador crea un usuario `admin` con contraseña `Admin2026!` si no existe.
- El script `sql/schema_postgresql.sql` ofrece la misma estructura en PostgreSQL.
- El script `scripts/migrate_mysql_to_postgresql.php` migra datos de `roles` y `usuarios`.

## Observaciones

- El estado actual cumplimenta la infraestructura básica, pero no implementa el esquema completo de 24 tablas ni la autenticación OTP mencionada en las propuestas.
- El proyecto está listo para continuar con los módulos de equipos, órdenes y reportes.
