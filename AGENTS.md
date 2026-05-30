# Sistema PDVSA - Guía para Agentes OpenCode

## Comandos esenciales

**Instalación inicial (XAMPP/MySQL):**
1. Iniciar Apache y MySQL en XAMPP
2. Importar esquema: `mysql -u root < sql/schema_mysql.sql`
3. Admin por defecto: usuario=`admin`, contraseña=`Admin2026!`
4. Acceder: `http://localhost/sistema_pdvsa/`

**Para PostgreSQL:**
1. Importar: `mysql -u root < sql/schema_postgresql.sql`
2. En `config/db.php` o variables de entorno: `DB_DRIVER=pgsql`

**Migración MySQL → PostgreSQL:**
```bash
php scripts/migrate_mysql_to_postgresql.php
```
Variables opcionales: `MYSQL_HOST`, `MYSQL_DB`, `PG_HOST`, `PG_DB`

## Estructura clave

- `auth/` - Login, registro, OTP (`auth/login.php`, `auth/otp_verify.php`)
- `public/` - Páginas principales (requieren sesión): `equipos.php`, `reportes.php`, etc.
- `css/` - Estilos, tema claro/oscuro, sidebar responsive, session keepalive
- `sql/` - Esquemas y migraciones MySQL/PostgreSQL
- `scripts/` - Herramientas de migración
- `openspec/` - Propuestas y especificaciones (NO modificar/eliminar)
- `.opencode/` - Configuración de OpenCode

## Puntos críticos de autenticación

- Las páginas en `public/` requieren sesión activa (verificar con `estaAutenticado()`)
- Registro de usuarios solo accesible para rol Administrador
- Sistema OTP: login en 2 pasos (credenciales → código OTP)
- Sesiones con timeout por rol: Admin(10min), Supervisor(20min), Otros(35min)
- Renovar sesión: `public/session_keepalive.php` (llamar vía AJAX cada minuto)

## Notas importantes

- El tema claro/oscuro se controla mediante `data-theme` en `<html>` y CSS variables
- Barra lateral colapsable con toggle en header
- `config/db.php` maneja automáticamente MySQL vs PostgreSQL vía variable de entorno
- Los permisos se validan mediante `permisos_json` en sesión y función `tienePermiso()`
- Nunca eliminar la carpeta `openspec/` - contiene propuestas para futuras implementaciones

## Verificación rápida

Para verificar que el sistema funciona:
1. Login con admin/Admin2026!
2. Acceder a `/public/equipos.php` (módulo de equipos recientemente implementado)
3. Verificar que el tema se puede togglear y la barra lateral se colapsa