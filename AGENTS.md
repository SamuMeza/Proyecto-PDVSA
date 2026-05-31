# Sistema PDVSA - Guía para Agentes OpenCode

## Comandos esenciales

**Instalación inicial (XAMPP/MySQL):**
1. Iniciar Apache y MySQL en XAMPP
2. Importar migraciones en orden: `for %f in (database\migrations\0*.sql) do mysql -u root < %f`
3. Importar seeds: `for %f in (database\seeds\0*.sql) do mysql -u root < %f`
4. Admin por defecto: usuario=`admin`, contraseña=`Admin2026!`
5. Acceder: `http://localhost/sistema_pdvsa/`

**Para PostgreSQL:**
- Configurar `DB_DRIVER=pgsql` en `.env`

**Migración MySQL → PostgreSQL:**
```bash
php scripts/migrate_mysql_to_postgresql.php
```

## Estructura completa

```
├── .env.example, .gitignore, README.md, composer.json
├── config/          → database.php, app.php, routes.php, session.php, autoload.php
├── public/          → Document root del servidor
│   ├── index.php    → Front controller
│   ├── .htaccess    → Rewrite rules
│   ├── assets/css/  → main.css (+ dashboard, calendar, forms, tables)
│   ├── assets/js/   → app, dashboard, calendar, forms, alerts, charts, theme, sidebar, session
│   ├── assets/images/ → logo-pdvsa.png, favicon.ico, avatars/
│   ├── assets/uploads/ → fotos-fallas, certificados, reportes-pdf
│   └── assets/fonts/inter/
├── src/
│   ├── Core/        → App, Router, Request, Response, Database, Session, Auth, Logger
│   ├── Models/      → 17 modelos (Zona, Equipo, User, Role, Localidad, Area, etc.)
│   ├── Controllers/ → 10 controladores (Auth, Dashboard, Equipo, Preventiva, Correctiva, etc.)
│   ├── Services/    → 12 servicios (Auth, Equipo, Preventiva, Correctiva, PDF, Chart, etc.)
│   ├── Views/       → layouts/, partials/, dashboard/, equipos/, preventivo/, etc.
│   ├── Middleware/   → Auth, Role, Session, Csrf
│   ├── Helpers/     → Date, String, File, Validation, Security
│   └── Exceptions/  → App, Validation, Auth, NotFound
├── database/
│   ├── migrations/  → 001-028 (numeradas)
│   ├── seeds/       → 001-008
│   └── procedures/  → sp_generar_calendario, sp_calcular_downtime, sp_generar_alertas
├── docs/            → INSTALL, CONFIG, DATABASE, API, ROLES, CHANGELOG
├── logs/            → app.log, auth.log, errors.log, queries.log
├── temp/            → cache/, sessions/, uploads-temp/
└── tests/           → Unit/, Integration/, Feature/, phpunit.xml
```

## Puntos críticos

- Páginas en `public/` requieren sesión activa (check con `AuthService::check()`)
- Registro de usuarios solo para rol Administrador
- Sistema OTP: login en 2 pasos
- Sesiones con timeout por rol: Admin(10min), Supervisor(20min), Otros(35min)
- La sesión NO se almacena en archivos sino en la tabla `usuarios` (`sesion_activa_token`)
- Las migraciones están en `database/migrations/` (NUNCA modificar las ya aplicadas)
- `openspec/` contiene propuestas (NO modificar/eliminar)
- `auth/`, `scripts/` y archivos legacy en `public/` conviven con el nuevo MVC
- Autoloader PSR-4: `App\` → `src/` (vía `config/autoload.php`)
- Los nombres de modelos/controladores existentes (User, Role, Preventiva, Correctiva) se mantienen
