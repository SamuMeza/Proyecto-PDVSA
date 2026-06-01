# Plan de Reestructuración — CMMS PDVSA Punta de Mata

Fecha: 01/06/2026
Base: `estructura_carpetas.md` (estructura deseada)
Estado actual: Híbrido (legacy `public/*.php` + MVC parcial)
Estado destino: MVC puro con front controller único

---

## Estructura Destino

```
sistema_pdvsa/
├── .env
├── .env.example
├── .gitignore
├── .htaccess
├── README.md
├── composer.json
├── composer.lock
│
├── config/
│   ├── autoload.php
│   ├── database.php
│   ├── app.php
│   ├── routes.php
│   └── session.php
│
├── public/                         ← ÚNICO punto de entrada
│   ├── index.php                   ← Front controller (Router)
│   ├── .htaccess
│   ├── assets/
│   │   ├── css/                    ← main.css, dashboard.css, calendar.css, forms.css, tables.css
│   │   ├── js/                     ← app.js, dashboard.js, calendar.js, forms.js, alerts.js, charts.js
│   │   ├── images/                 ← logo-pdvsa.png, favicon.ico, avatars/
│   │   ├── uploads/                ← fotos-fallas/, certificados/, reportes-pdf/
│   │   └── fonts/inter/
│   └── (NO más archivos PHP)
│
├── src/
│   ├── Core/                       ← App, Router, Request, Response, Database, Session, Auth, Logger
│   ├── Models/                     ← 26 modelos
│   ├── Controllers/                ← 13 controladores
│   ├── Services/                   ← 12 servicios + EmailService (opcional)
│   ├── Views/
│   │   ├── layouts/
│   │   │   ├── main.php            ← Layout principal (desde public/includes/layout.php)
│   │   │   ├── auth.php
│   │   │   └── print.php
│   │   ├── partials/               ← sidebar, header, footer, breadcrumbs, pagination, alerts, modals
│   │   ├── dashboard/
│   │   ├── equipos/
│   │   ├── preventivo/             ← UNIFICADO
│   │   ├── correctivo/             ← UNIFICADO
│   │   ├── calendario/
│   │   ├── reportes/
│   │   ├── usuarios/
│   │   ├── configuracion/
│   │   └── auth/
│   ├── Middleware/
│   ├── Helpers/
│   └── Exceptions/
│
├── database/
│   ├── migrations/                 ← 028 archivos (se quedan)
│   ├── seeds/                      ← 008 archivos (se quedan)
│   ├── procedures/                 ← 003 archivos (se quedan)
│   └── scripts/                    ← Desde raíz scripts/
│
├── docs/                           ← docs/ + docs/tablas/
├── logs/
├── temp/
├── tests/
└── openspec/                       ← NO TOCAR
```

---

## FASE 0 — Fundación (Pre-requisitos)

**Objetivo**: Que el proyecto tenga lo mínimo para ejecutarse.

| # | Acción | Detalle |
|---|--------|---------|
| 0.1 | Crear `.env` desde `.env.example` | Copiar `.env.example` → `.env`, ajustar credenciales si es necesario |
| 0.2 | Ejecutar `composer install` | Genera `composer.lock` y `vendor/` |

> ⚠ **Dependencia**: Todo lo demás necesita `vendor/autoload.php` y `.env`.

---

## FASE 1 — Reparar la base del MVC

**Objetivo**: Arreglar lo que está roto en el nuevo sistema MVC para que sea funcional, sin tocar el legacy.

### 1.1 — Agregar `Response::view()` y `Response::html()`

**Archivo**: `src/Core/Response.php`

Agregar dos métodos estáticos:

```php
public static function view(string $template, array $data = []): void
{
    extract($data);
    require dirname(__DIR__, 2) . '/src/Views/' . $template . '.php';
}

public static function html(string $template, array $data = [], string $layout = 'main'): void
{
    extract($data);
    require dirname(__DIR__, 2) . "/src/Views/layouts/{$layout}.php";
}
```

- `view()` renderiza una plantilla suelta (para AJAX, partials, layouts que ya abren HTML).
- `html()` renderiza una plantilla DENTRO del layout principal (flujo normal de páginas).

**Impacto**: Desbloquea `ConfiguracionController` y `ReporteCondicionController` que llaman a `Response::view()` y hoy crashean.

### 1.2 — Convertir `public/index.php` en front controller real

**Archivo**: `public/index.php`

```
ESTADO ACTUAL:
  public/index.php renderiza el dashboard directamente
  (require layout.php + Views/dashboard/index.php + layout_footer.php)

ESTADO DESEADO:
  public/index.php es el front controller que ejecuta el Router
  Si no hay vendor/autoload.php, redirige a fallback
```

```php
<?php
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
    $router = new App\Core\Router();
    $routes = require __DIR__ . '/../config/routes.php';
    $routes($router);
    $app = App\Core\App::getInstance();
    $app->setRouter($router);
    $app->run();
} else {
    require_once __DIR__ . '/../config/database.php';
    header('Location: ' . App::BASE_PATH . '/public/index.php');
    exit;
}
```

> ⚠ **Dependencia**: Requiere FASE 0 (composer install).

---

## FASE 2 — Cerrar módulos pendientes

**Objetivo**: Usuarios y Reportes pasan de "en desarrollo" a funcionales.

### 2A — Usuarios CRUD

| # | Acción | Archivo | Descripción |
|---|--------|---------|-------------|
| 2A.1 | Completar `UsuarioController::index()` | `src/Controllers/UsuarioController.php` | Listar usuarios con filtros, paginación |
| 2A.2 | Agregar `UsuarioController::create()` | `src/Controllers/UsuarioController.php` | Formulario + POST para crear usuario (usa `AuthService::createUser()`) |
| 2A.3 | Agregar `UsuarioController::edit()` | `src/Controllers/UsuarioController.php` | Formulario + POST para editar usuario |
| 2A.4 | Agregar `UsuarioController::toggleStatus()` | `src/Controllers/UsuarioController.php` | Activar/desactivar usuario |
| 2A.5 | Agregar `UsuarioController::roles()` | `src/Controllers/UsuarioController.php` | Listado de roles y permisos |
| 2A.6 | Conectar vistas existentes: `index.php`, `create.php`, `edit.php`, `roles.php` | Views/usuarios/ | Las vistas ya existen, solo hay que pasarles datos |
| 2A.7 | Registrar rutas POST en `config/routes.php` | `config/routes.php` | `usuario/crear`, `usuario/editar`, `usuario/toggle` |
| 2A.8 | Actualizar `public/usuarios.php` para que delegue en el Controller | `public/usuarios.php` | O redirigir |

**Vistas que ya existen y hay que conectar**:
- `src/Views/usuarios/index.php` — listado (espera `$usuarios`)
- `src/Views/usuarios/create.php` — formulario crear (espera `$roles`)
- `src/Views/usuarios/edit.php` — formulario editar (espera `$usuario`, `$roles`)
- `src/Views/usuarios/roles.php` — tabla roles (espera `$roles`)

### 2B — Reportes

| # | Acción | Archivo | Descripción |
|---|--------|---------|-------------|
| 2B.1 | Crear `ReporteService` | `src/Services/ReporteService.php` | Métodos de agregación: contar preventivas, correctivas, cumplimiento, etc. |
| 2B.2 | Completar `ReporteController::index()` | `src/Controllers/ReporteController.php` | Dashboard de reportes con stats |
| 2B.3 | Agregar `ReporteController::cumplimiento()` | `src/Controllers/ReporteController.php` | Vista de cumplimiento con ChartGenerator |
| 2B.4 | Agregar `ReporteController::fallas()` | `src/Controllers/ReporteController.php` | Estadísticas de fallas |
| 2B.5 | Agregar `ReporteController::resumenMensual()` | `src/Controllers/ReporteController.php` | Resumen mensual |
| 2B.6 | Agregar `ReporteController::tecnicos()` | `src/Controllers/ReporteController.php` | Rendimiento por técnico |
| 2B.7 | Conectar vistas existentes: `index.php`, `cumplimiento.php`, `fallas.php`, `resumen-mensual.php`, `tecnicos.php` | Views/reportes/ | Ya existen, esperan `$stats`, `$fallas`, etc. |
| 2B.8 | Registrar rutas en `config/routes.php` | `config/routes.php` |
| 2B.9 | Actualizar `public/reportes.php` | `public/reportes.php` | O redirigir |

---

## FASE 3 — Unificar layouts y migrar auth

**Objetivo**: Mover `auth/` y `public/includes/layout*` a sus lugares definitivos.

### 3A — Layout principal

| # | Acción | Archivo | Descripción |
|---|--------|---------|-------------|
| 3A.1 | Crear `src/Views/layouts/main.php` | Nuevo | Copiar contenido de `public/includes/layout.php` |
| 3A.2 | Actualizar sidebar URLs (de `/public/equipos.php` a `/equipos`) | `main.php` | Usar rutas MVC |
| 3A.3 | Fusionar `layout_footer.php` al final de `main.php` | `main.php` | Incluir JS y cerrar HTML |
| 3A.4 | Actualizar `public/includes/layout.php` para que haga `require` de `main.php` | `public/includes/layout.php` | Puente temporal |
| 3A.5 | Actualizar TODOS los controllers que usan `require layout.php` para que usen `Response::html('...')` | 8 controllers | Dashboard, Equipo, Preventiva, Correctiva, Calendario, Usuario, Reporte, Auth(register) |
| 3A.6 | Actualizar `public/*.php` para lo mismo | 7 archivos | index, equipos, preventivas, correctivas, calendario, usuarios, reportes |
| 3A.7 | Una vez que todo funciona con `main.php`, eliminar `public/includes/layout.php` y `layout_footer.php` | Delete | Legacy cleanup |

**Referencias a actualizar (15 lugares)**:

| Archivo | Línea actual | Nuevo |
|---------|-------------|-------|
| `public/index.php` | `require __DIR__ . '/includes/layout.php'` | `Response::html('dashboard/index', [...], 'main')` |
| `public/equipos.php` | igual | `Response::html('equipos/index', [...], 'main')` |
| `public/preventivas.php` | igual | `Response::html('preventivas/index', [...], 'main')` |
| `public/correctivas.php` | igual | `Response::html(...)` |
| `public/calendario.php` | igual | `Response::html(...)` |
| `public/reportes.php` | igual | `Response::html(...)` |
| `public/usuarios.php` | igual | `Response::html(...)` |
| `auth/register.php` | igual | `Response::html(...)` |
| `src/Controllers/DashboardController.php` | `require dirname(...)` | `Response::html('dashboard/index', [...], 'main')` |
| `src/Controllers/EquipoController.php` | igual | igual |
| `src/Controllers/PreventivaController.php` | igual | igual |
| `src/Controllers/CorrectivaController.php` | igual | igual |
| `src/Controllers/CalendarioController.php` | igual | igual |
| `src/Controllers/UsuarioController.php` | igual | igual |
| `src/Controllers/ReporteController.php` | igual | igual |

### 3B — Migrar auth/ a Controllers

| # | Acción | Archivo | Descripción |
|---|--------|---------|-------------|
| 3B.1 | Verificar AuthController | `src/Controllers/AuthController.php` | Ya tiene login, otpVerify, logout, register |
| 3B.2 | Actualizar redirecciones en AuthService | `src/Services/AuthService.php` | Cambiar `/auth/login.php` → `/login`, `/public/index.php` → `/dashboard` |
| 3B.3 | Actualizar redirecciones en auth/*.php | 4 archivos | `auth/login.php`, `auth/register.php`, `auth/logout.php`, `auth/otp_verify.php` |
| 3B.4 | Mover `auth/auth_functions.php` (si se usa) | — | Verificar dependencias |
| 3B.5 | Actualizar `public/session_keepalive.php` | SessionController | `SessionController::keepalive()` ya existe, verificar |
| 3B.6 | Eliminar directorio `auth/` de raíz | Delete | Solo después de verificar que todo funciona |

**Redirecciones hardcodeadas en `AuthService.php`**:

```php
// Actual (ejemplos aproximados)
header('Location: ' . App::BASE_PATH . '/auth/login.php');
header('Location: ' . App::BASE_PATH . '/public/index.php');

// Nuevo
header('Location: ' . App::BASE_PATH . '/login');
header('Location: ' . App::BASE_PATH . '/dashboard');
```

---

## FASE 4 — Activar el Router como front controller

**Objetivo**: Que todas las peticiones pasen por el Router.

### 4.1 — Actualizar `.htaccess`

**Archivo**: `.htaccess` (raíz)

```
ESTADO ACTUAL:
  RewriteRule ^$ public/index.php [L]                    ← solo redirige /
  RewriteRule ^config/ - [F,L]                           ← bloquea config/
  RewriteRule ^sql/ - [F,L]                              ← bloquea sql/

ESTADO DESEADO:                                          ← TODO va a public/index.php
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^(.*)$ public/index.php [QSA,L]
  RewriteRule ^config/ - [F,L]
  RewriteRule ^sql/ - [F,L]                              ← (sql/ ya no existe, pero por seguridad)
```

### 4.2 — Verificar `public/.htaccess`

**Archivo**: `public/.htaccess`

Asegurar que rewritea correctamente a `public/index.php` cuando la URL no coincide con un archivo real.

### 4.3 — Progresión de migración de rutas

No hacer todo de golpe. Por cada ruta:

1. Verificar que el Controller correspondiente esté completo
2. Agregar la ruta a `config/routes.php`
3. Probar que funcione por URL amigable
4. Reemplazar el `public/*.php` legacy con un redirect header
5. Dejar unos días, luego eliminar el archivo legacy

Orden sugerido:

| Orden | Ruta | Controller | Legacy |
|-------|------|-----------|--------|
| 1 | `/dashboard` | DashboardController | `public/index.php` |
| 2 | `/equipos` | EquipoController | `public/equipos.php` |
| 3 | `/preventivas` | PreventivaController | `public/preventivas.php` |
| 4 | `/correctivas` | CorrectivaController | `public/correctivas.php` |
| 5 | `/calendario` | CalendarioController | `public/calendario.php` |
| 6 | `/usuarios` | UsuarioController | `public/usuarios.php` |
| 7 | `/reportes` | ReporteController | `public/reportes.php` |
| 8 | `/session/keepalive` | SessionController | `public/session_keepalive.php` |

### 4.4 — Eliminar `index.php` de la raíz

Una vez que el Router funciona y todas las rutas pasan por `public/index.php`, eliminar el `index.php` de la raíz del proyecto.

---

## FASE 5 — Limpiar directorios extra de la raíz

**Objetivo**: Que la raíz quede limpia como en la estructura deseada.

| # | Directorio | Acción | Destino |
|---|-----------|--------|---------|
| 5.1 | `css/` | `styles.css` → `public/assets/css/` (fusionar si hay contenido único) | `public/assets/css/` |
| | | `session.js`, `sidebar.js`, `theme.js` → `public/assets/js/` (ya existen, verificar si hay diferencias) | `public/assets/js/` |
| | | Eliminar directorio `css/` | — |
| 5.2 | `sql/` | `schema_mysql.sql`, `schema_postgresql.sql` → `database/schemas/` | `database/schemas/` |
| | | `seed_admin.sql` → `database/seeds/` | `database/seeds/` |
| | | `migrations/` (si tiene contenido extra) → fusionar con `database/migrations/` | `database/migrations/` |
| | | Eliminar directorio `sql/` | — |
| 5.3 | `tablas/` | Ya existe en `docs/tablas/`. Verificar contenido, eliminar raíz | — |
| 5.4 | `scripts/` | `migrate_mysql_to_postgresql.php` → `database/scripts/` | `database/scripts/` |
| | | Eliminar directorio `scripts/` | — |
| 5.5 | `auth/` | Ya migrado en FASE 3B. Eliminar | — |
| 5.6 | `documentacion_fases_y_decisiones.md` | Mover a `docs/` | `docs/` |

---

## FASE 6 — Refactor y consolidación

**Objetivo**: Resolver inconsistencias de naming y código duplicado.

### 6A — Renombrar Controladores

| Archivo actual | Nuevo nombre |
|----------------|-------------|
| `PreventivaController.php` | `PreventivoController.php` |
| `CorrectivaController.php` | `CorrectivoController.php` |

Actualizar:
- `config/routes.php`
- Todos los `use` que referencien los nombres antiguos
- Namespace y nombre de clase dentro de los propios archivos

### 6B — Unificar vistas duplicadas

Actualmente hay carpetas paralelas:

```
src/Views/preventivo/  → calendario.php, ordenes.php, create.php, show.php, checklist.php
src/Views/preventivas/ → index.php              ← FUSIONAR

src/Views/correctivo/  → reportar.php, ordenes.php, show.php, condicion.php, cierre.php
src/Views/correctivas/ → index.php              ← FUSIONAR
```

| # | Acción |
|---|--------|
| 6B.1 | Mover `preventivas/index.php` → `preventivo/index.php` |
| 6B.2 | Mover `correctivas/index.php` → `correctivo/index.php` |
| 6B.3 | Eliminar carpetas `preventivas/` y `correctivas/` |
| 6B.4 | Actualizar referencias en Controllers y archivos legacy |

### 6C — Extraer lógica inline

| # | Archivo | Líneas aproximadas | ¿Qué contiene? |
|---|---------|-------------------|----------------|
| 6C.1 | `public/correctivas.php` | ~250 de lógica + ~350 de HTML | CRUD completo, filtros, fotos, checklists, auditoría — TODO inline |
| 6C.2 | `public/calendario.php` | ~160 de lógica + ~130 de HTML | Vistas semanal/mensual, filtros por familia, colores configurables |

**Estrategia**: No refactorizar todo de una vez. Por cada archivo:
1. Identificar bloques de lógica pura (SQL, validaciones)
2. Moverlos a su Service correspondiente: `CorrectivoService`, `CalendarioService`
3. El `public/*.php` legacy queda como un "routing delgado" que llama al Service
4. Eventualmente ese routing delgado se mueve al Controller

### 6D — Bugfix CalendarGenerator

**Archivo**: `CalendarGenerator.php:35`

Problema: `str_contains($freq, 'semanal') || str_contains($freq, 'semanal')` la segunda condición está duplicada.

Fix: Agregar la condición real que falta (ej: `str_contains($freq, 'semana')` si aplica, o eliminar el duplicado si solo hay una frecuencia semanal).

### 6E — Hacer funcionales los JS stubs

| # | Archivo JS | Estado actual | Acción |
|---|-----------|--------------|--------|
| 6E.1 | `dashboard.js` | `console.log` | Fetch a `/api/dashboard/stats` (o endpoint similar) |
| 6E.2 | `calendar.js` | `console.log` | Fetch a `/api/calendar/events` |
| 6E.3 | `alerts.js` | `console.log` | Fetch a `/api/alerts` |
| 6E.4 | `charts.js` | `console.log` | Renderizar charts con Chart.js |
| 6E.5 | `forms.js` | `console.log` | Validación client-side de formularios |

**Nota**: Los JS stubs pueden implementarse gradualmente. No bloquean el funcionamiento del sistema.

### 6F — Unificar patrones de servicios

**Problema**: Todos los servicios son 100% estáticos (sin DI, imposibles de mockear).

**Propuesta**: No refactorizar ahora (sería un cambio masivo). Documentar en `docs/` que el patrón actual es servicios estáticos, y considerar inyección de dependencias para la próxima iteración mayor.

---

## FASE 7 — Tests (Opcional)

**Objetivo**: Reemplazar los tests `class_exists()` con tests reales.

| # | Test | Archivo | Tipo |
|---|------|---------|------|
| 7.1 | `DowntimeCalculator::calculate()` | `tests/Unit/DowntimeCalculatorTest.php` | Unitario |
| 7.2 | `AuthService::login()` + `::verifyOtp()` | `tests/Unit/AuthServiceTest.php` | Unitario |
| 7.3 | Flujo completo de login | `tests/Integration/AuthFlowTest.php` (expandir) | Integración |
| 7.4 | CRUD de equipos | `tests/Feature/EquiposCrudTest.php` (expandir) | Feature |
| 7.5 | CRUD de preventivas | `tests/Feature/PreventivoTest.php` (expandir) | Feature |
| 7.6 | CRUD de correctivas | `tests/Feature/CorrectivoTest.php` (expandir) | Feature |

---

## Mapa de archivos completo

### Archivos a CREAR

| Archivo | En FASE |
|---------|---------|
| `.env` | 0 |
| `src/Views/layouts/main.php` | 3A |
| `src/Services/ReporteService.php` | 2B |

### Archivos a MODIFICAR

| Archivo | En FASE |
|---------|---------|
| `src/Core/Response.php` | 1 |
| `public/index.php` | 1 |
| `src/Controllers/UsuarioController.php` | 2A |
| `src/Controllers/ReporteController.php` | 2B |
| `src/Services/AuthService.php` | 3B |
| `config/routes.php` | 2A, 2B, 4 |
| `.htaccess` (raíz) | 4 |
| `public/.htaccess` | 4 |
| `public/includes/layout.php` | 3A |
| `public/equipos.php` | 3A, 4 |
| `public/preventivas.php` | 3A, 4 |
| `public/correctivas.php` | 3A, 4 |
| `public/calendario.php` | 3A, 4 |
| `public/reportes.php` | 3A, 4 |
| `public/usuarios.php` | 3A, 4 |
| `public/session_keepalive.php` | 4 |
| `CalendarGenerator.php` | 6D |
| `public/assets/js/*.js` (6 archivos) | 6E |

### Archivos a RENOMBRAR

| Original | Nuevo | En FASE |
|----------|-------|---------|
| `PreventivaController.php` | `PreventivoController.php` | 6A |
| `CorrectivaController.php` | `CorrectivoController.php` | 6A |

### Directorios a MOVER

| Origen | Destino | En FASE |
|--------|---------|---------|
| `auth/` | → Controllers + Views | 3B |
| `css/` | → `public/assets/` | 5 |
| `sql/` | → `database/` | 5 |
| `tablas/` | → ya existe en `docs/` | 5 |
| `scripts/` | → `database/scripts/` | 5 |

### Directorios/Archivos a ELIMINAR

| Elemento | En FASE | Condición |
|----------|---------|-----------|
| `public/includes/layout.php` | 3A | Después de migrar a `main.php` |
| `public/includes/layout_footer.php` | 3A | Después de fusionar en `main.php` |
| `auth/` | 5 | Después de migrar a Controllers |
| `css/` | 5 | Después de mover contenido |
| `sql/` | 5 | Después de mover contenido |
| `tablas/` (raíz) | 5 | Después de verificar `docs/tablas/` |
| `scripts/` (raíz) | 5 | Después de mover a `database/scripts/` |
| `public/equipos.php` | 4 | Después de activar ruta `/equipos` |
| `public/preventivas.php` | 4 | Después de activar ruta `/preventivas` |
| `public/correctivas.php` | 4 | Después de activar ruta `/correctivas` |
| `public/calendario.php` | 4 | Después de activar ruta `/calendario` |
| `public/reportes.php` | 4 | Después de activar ruta `/reportes` |
| `public/usuarios.php` | 4 | Después de activar ruta `/usuarios` |
| `public/session_keepalive.php` | 4 | Después de activar ruta `/session/keepalive` |
| `index.php` (raíz) | 4 | Después de activar front controller |
| `src/Views/preventivas/` | 6B | Después de fusionar en `preventivo/` |
| `src/Views/correctivas/` | 6B | Después de fusionar en `correctivo/` |

---

## Diagrama de dependencias

```
FASE 0 (env + composer)
    │
    ▼
FASE 1 (Response::view, front controller base)
    │
    ├─────────────────────┐
    ▼                     ▼
FASE 2 (usuarios,      FASE 3 (layouts + auth)
 reportes)                  │
    │                       ▼
    └──────────┬────────FASE 4 (Router ON)
               │              │
               ▼              ▼
            FASE 5 (limpiar raíz)
               │
               ▼
            FASE 6 (refactor)
               │
               ▼
            FASE 7 (tests, opcional)
```

Las Fases 2 y 3 son **paralelizables** (no dependen una de la otra).

FASE 0 debe completarse primero.
FASE 1 debe completarse antes de 3 y 4.
FASE 4 requiere FASE 1 y preferiblemente FASE 3.
FASE 5 requiere FASE 3B y FASE 4 (para saber qué se elimina).
FASE 6 puede hacerse en cualquier momento después de FASE 4.

---

## Riesgos y mitigaciones

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|-------------|---------|------------|
| Router no maneja alguna ruta legacy | Media | Alto — página rota | Probar ruta por ruta en FASE 4, dejar legacy como backup |
| Redirecciones hardcodeadas en AuthService | Alta | Alto — loop de login | grep de todas las URLs hardcodeadas antes de FASE 3B |
| Response::view() no integra el layout completo | Media | Medio — páginas sin header/footer | Probar ConfiguracionController inmediatamente después de FASE 1 |
| Sidebar sigue apuntando a `/public/*.php` | Alta | Bajo — funciona pero URL incorrecta | Actualizar en FASE 3A.2 |
| Views huérfanas se pierden si no se conectan antes de eliminar legacy | Media | Bajo — existen en disco, no se pierden | Migrarlas progresivamente |
| Romper algo que funciona por cambios simultáneos | Media | Alto | No hacer FASE 4 hasta que FASE 2 y 3 estén completas y probadas |

---

## Notas finales

1. **No eliminar nada hasta que el reemplazo funcione**. Los archivos legacy se mantienen hasta que la nueva versión esté probada.
2. **FASE 4 (Router ON) es el punto más crítico**. Hacerlo de a una ruta por vez, probando cada una.
3. **Admin hace todo**. Dado que el usuario indicó que todas las acciones las realiza un admin, se simplifica la lógica de permisos. Las comprobaciones de rol existentes se mantienen pero el admin las pasa todas.
4. **Este plan no toca**: `openspec/`, `.opencode/`, `AGENTS.md`, `database/migrations/` (inmutables), `src/Models/` (completos), `src/Core/` (completo), `src/Middleware/` (completo), `src/Helpers/` (completo), `src/Exceptions/` (completo).
