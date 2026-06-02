# Estructura de Carpetas – Proyecto CMMS PDVSA Punta de Mata
## PHP + PostgreSQL – Aplicación Web

---

```
cmms-pdvsa-puntamata/
│
├── 📁 .env                          # Variables de entorno (no versionar)
├── 📁 .env.example                  # Plantilla de variables de entorno
├── 📁 .gitignore                    # Archivos ignorados por Git
├── 📁 README.md                     # Manual de instalación para AIT
├── 📁 composer.json                 # Dependencias PHP (Composer)
├── 📁 composer.lock                 # Lock de dependencias
│
├── 📁 config/
│   ├── database.php                 # Configuración de conexión PostgreSQL
│   ├── app.php                      # Configuración general de la aplicación
│   ├── routes.php                   # Definición de rutas del sistema
│   └── session.php                  # Configuración de sesiones e inactividad
│
├── 📁 public/                       # Document root del servidor web
│   ├── index.php                    # Punto de entrada único (front controller)
│   ├── .htaccess                    # Rewrite rules (Apache)
│   ├── 📁 assets/
│   │   ├── 📁 css/
│   │   │   ├── main.css             # Estilos principales (paleta pastel)
│   │   │   ├── dashboard.css        # Estilos del dashboard
│   │   │   ├── calendar.css         # Estilos del calendario
│   │   │   ├── forms.css            # Estilos de formularios
│   │   │   └── tables.css           # Estilos de tablas
│   │   ├── 📁 js/
│   │   │   ├── app.js               # JavaScript principal
│   │   │   ├── dashboard.js         # Lógica del dashboard
│   │   │   ├── calendar.js          # Lógica del calendario
│   │   │   ├── forms.js             # Validaciones de formularios
│   │   │   ├── alerts.js            # Sistema de alertas internas
│   │   │   └── charts.js            # Gráficos (Chart.js o similar)
│   │   ├── 📁 images/
│   │   │   ├── logo-pdvsa.png       # Logo institucional
│   │   │   ├── favicon.ico          # Favicon
│   │   │   └── 📁 avatars/          # Fotos de perfil de usuarios
│   │   └── 📁 uploads/
│   │       ├── 📁 fotos-fallas/       # Fotos de órdenes correctivas
│   │       ├── 📁 certificados/      # Certificados de calibración
│   │       └── 📁 reportes-pdf/      # Reportes generados en PDF
│   └── 📁 fonts/
│       └── inter/                    # Fuente Inter (Google Fonts local)
│
├── 📁 src/                          # Código fuente de la aplicación
│   ├── 📁 Core/
│   │   ├── App.php                  # Clase principal de la aplicación
│   │   ├── Router.php               # Sistema de enrutamiento
│   │   ├── Request.php              # Manejo de peticiones HTTP
│   │   ├── Response.php             # Manejo de respuestas HTTP
│   │   ├── Database.php             # Wrapper de conexión PostgreSQL (PDO)
│   │   ├── Session.php              # Gestión de sesiones e inactividad
│   │   ├── Auth.php                 # Autenticación y autorización
│   │   └── Logger.php               # Sistema de logging privado
│   │
│   ├── 📁 Models/                   # Modelos (capa de datos)
│   │   ├── Localidad.php
│   │   ├── Area.php
│   │   ├── Instalacion.php
│   │   ├── Zona.php
│   │   ├── CategoriaEquipo.php
│   │   ├── GrupoSeguridad.php
│   │   ├── Equipo.php
│   │   ├── TipoFalla.php
│   │   ├── PrioridadFalla.php
│   │   ├── NivelMantenimiento.php
│   │   ├── Checklist.php
│   │   ├── ChecklistItem.php
│   │   ├── Usuario.php
│   │   ├── Rol.php
│   │   ├── OrdenPreventiva.php
│   │   ├── EjecucionPreventiva.php
│   │   ├── EjecucionChecklistItem.php
│   │   ├── OrdenCorrectiva.php
│   │   ├── FotoCorrectiva.php
│   │   ├── Calibracion.php
│   │   ├── Alerta.php
│   │   ├── LogAuditoria.php
│   │   ├── ReporteGenerado.php
│   │   └── ConfiguracionSistema.php
│   │
│   ├── 📁 Controllers/              # Controladores (lógica de negocio)
│   │   ├── AuthController.php       # Login, logout, recuperación
│   │   ├── DashboardController.php  # Panel principal, KPIs
│   │   ├── EquipoController.php     # CRUD de equipos, ficha, filtros
│   │   ├── PreventivoController.php # Calendario, órdenes preventivas
│   │   ├── CorrectivoController.php # Reportar falla, órdenes correctivas
│   │   ├── ReporteCondicionController.php # Reportes de condición
│   │   ├── ReporteController.php    # Dashboard de reportes, exportación PDF
│   │   ├── UsuarioController.php    # Gestión de usuarios y roles
│   │   ├── ConfiguracionController.php # Parámetros del sistema
│   │   ├── AlertaController.php     # Alertas y notificaciones internas
│   │   └── ApiController.php        # Endpoints AJAX para el frontend
│   │
│   ├── 📁 Views/                    # Vistas (plantillas HTML)
│   │   ├── 📁 layouts/
│   │   │   ├── main.php             # Layout principal (header, sidebar, footer)
│   │   │   ├── auth.php             # Layout para pantallas de login
│   │   │   └── print.php            # Layout para impresión de órdenes
│   │   ├── 📁 partials/
│   │   │   ├── sidebar.php          # Menú lateral
│   │   │   ├── header.php           # Barra superior
│   │   │   ├── footer.php           # Pie de página
│   │   │   ├── breadcrumbs.php      # Migas de pan
│   │   │   ├── pagination.php       # Paginación de tablas
│   │   │   ├── alerts.php           # Alertas visuales prominentes
│   │   │   └── modals.php           # Modales reutilizables
│   │   ├── 📁 dashboard/
│   │   │   └── index.php            # Vista del dashboard
│   │   ├── 📁 equipos/
│   │   │   ├── index.php            # Listado de equipos
│   │   │   ├── create.php           # Formulario crear equipo
│   │   │   ├── edit.php             # Formulario editar equipo
│   │   │   ├── show.php             # Ficha individual del equipo
│   │   │   └── 📁 partials/
│   │   │       ├── info-general.php # Pestaña info general
│   │   │       ├── mantenimiento.php # Pestaña mantenimiento
│   │   │       ├── historial.php    # Pestaña historial fallas
│   │   │       └── calibracion.php  # Pestaña calibración
│   │   ├── 📁 preventivo/
│   │   │   ├── calendario.php       # Vista calendario semanal/mensual/anual
│   │   │   ├── ordenes.php          # Listado de órdenes preventivas
│   │   │   ├── create.php           # Crear orden manual
│   │   │   ├── show.php             # Ficha de orden preventiva
│   │   │   └── checklist.php        # Vista de checklist interactivo
│   │   ├── 📁 correctivo/
│   │   │   ├── reportar.php         # Formulario reportar falla
│   │   │   ├── ordenes.php          # Listado de órdenes correctivas
│   │   │   ├── show.php             # Ficha de orden correctiva
│   │   │   ├── condicion.php        # Formulario reporte de condición
│   │   │   └── cierre.php           # Formulario de cierre (causa raíz)
│   │   ├── 📁 reportes/
│   │   │   ├── cumplimiento.php     # Dashboard de cumplimiento
│   │   │   ├── tecnicos.php         # Rendimiento por técnico
│   │   │   ├── resumen-mensual.php  # Tabla resumen mensual
│   │   │   └── fallas.php           # Estadísticas de fallas
│   │   ├── 📁 usuarios/
│   │   │   ├── index.php            # Listado de usuarios
│   │   │   ├── create.php           # Crear usuario
│   │   │   ├── edit.php             # Editar usuario
│   │   │   └── roles.php            # Roles y permisos
│   │   ├── 📁 configuracion/
│   │   │   ├── parametros.php       # Parámetros del sistema
│   │   │   └── trazas.php           # Libro de trazas
│   │   └── 📁 auth/
│   │       └── login.php            # Pantalla de inicio de sesión
│   │
│   ├── 📁 Services/                 # Servicios de negocio
│   │   ├── PdfGenerator.php         # Generación de PDFs (dompdf/tcpdf)
│   │   ├── ChartGenerator.php       # Generación de gráficos para PDF
│   │   ├── EmailService.php         # (Reservado para futuro, no activo)
│   │   ├── ImageCompressor.php      # Compresión de fotos
│   │   ├── DowntimeCalculator.php   # Cálculo automático de downtime
│   │   ├── CalendarGenerator.php    # Generación de calendario automático
│   │   └── AlertGenerator.php       # Generación de alertas programadas
│   │
│   ├── 📁 Middleware/               # Middleware de peticiones
│   │   ├── AuthMiddleware.php       # Verifica autenticación
│   │   ├── RoleMiddleware.php       # Verifica permisos por rol
│   │   ├── SessionMiddleware.php    # Control de inactividad
│   │   └── CsrfMiddleware.php       # Protección CSRF
│   │
│   ├── 📁 Helpers/                  # Funciones auxiliares
│   │   ├── DateHelper.php           # Manejo de fechas y horas
│   │   ├── StringHelper.php         # Manejo de strings
│   │   ├── FileHelper.php           # Manejo de archivos y uploads
│   │   ├── ValidationHelper.php     # Validaciones comunes
│   │   └── SecurityHelper.php       # Hash, tokens, sanitización
│   │
│   └── 📁 Exceptions/               # Manejo de excepciones
│       ├── AppException.php
│       ├── ValidationException.php
│       ├── AuthException.php
│       └── NotFoundException.php
│
├── 📁 database/
│   ├── 📁 migrations/               # Scripts de migración de esquema
│   │   ├── 001_create_localidades.sql
│   │   ├── 002_create_areas.sql
│   │   ├── 003_create_instalaciones.sql
│   │   ├── 004_create_zonas.sql
│   │   ├── 005_create_categorias_equipo.sql
│   │   ├── 006_create_grupos_seguridad.sql
│   │   ├── 007_create_tipos_falla.sql
│   │   ├── 008_create_prioridades_falla.sql
│   │   ├── 009_create_roles.sql
│   │   ├── 010_create_usuarios.sql
│   │   ├── 011_create_equipos.sql
│   │   ├── 012_create_niveles_mantenimiento.sql
│   │   ├── 013_create_checklists.sql
│   │   ├── 014_create_checklist_items.sql
│   │   ├── 015_create_ordenes_preventivas.sql
│   │   ├── 016_create_ejecuciones_preventivas.sql
│   │   ├── 017_create_ejecucion_checklist_items.sql
│   │   ├── 018_create_ordenes_correctivas.sql
│   │   ├── 019_create_fotos_correctivas.sql
│   │   ├── 020_create_calibraciones.sql
│   │   ├── 021_create_alertas.sql
│   │   ├── 022_create_logs_auditoria.sql
│   │   ├── 023_create_reportes_generados.sql
│   │   └── 024_create_configuracion_sistema.sql
│   │
│   ├── 📁 seeds/                    # Datos iniciales (semillas)
│   │   ├── 001_seed_localidades.sql
│   │   ├── 002_seed_categorias.sql
│   │   ├── 003_seed_tipos_falla.sql
│   │   ├── 004_seed_prioridades.sql
│   │   ├── 005_seed_roles.sql
│   │   ├── 006_seed_usuario_admin.sql
│   │   ├── 007_seed_configuracion.sql
│   │   └── 008_seed_niveles_mantenimiento.sql
│   │
│   └── 📁 procedures/               # Stored procedures (si aplica)
│       ├── sp_generar_calendario.sql
│       ├── sp_calcular_downtime.sql
│       └── sp_generar_alertas.sql
│
├── 📁 tests/                        # Tests automatizados
│   ├── 📁 Unit/
│   │   ├── ModelsTest.php
│   │   ├── ServicesTest.php
│   │   └── HelpersTest.php
│   ├── 📁 Integration/
│   │   ├── DatabaseTest.php
│   │   ├── AuthFlowTest.php
│   │   └── OrdenesFlowTest.php
│   ├── 📁 Feature/
│   │   ├── DashboardTest.php
│   │   ├── EquiposCrudTest.php
│   │   ├── PreventivoTest.php
│   │   └── CorrectivoTest.php
│   └── phpunit.xml                  # Configuración de PHPUnit
│
├── 📁 docs/                         # Documentación
│   ├── INSTALL.md                   # Guía de instalación paso a paso
│   ├── CONFIG.md                    # Configuración del servidor
│   ├── DATABASE.md                  # Esquema y relaciones de BD
│   ├── API.md                       # Documentación de endpoints
│   ├── ROLES.md                     # Matriz de permisos por rol
│   └── CHANGELOG.md                 # Historial de cambios
│
├── 📁 logs/                         # Logs de la aplicación
│   ├── app.log                      # Log general de la aplicación
│   ├── auth.log                     # Log de autenticación
│   ├── errors.log                   # Log de errores
│   └── queries.log                  # Log de consultas SQL (debug)
│
└── 📁 temp/                         # Archivos temporales
    ├── 📁 cache/                    # Cache de la aplicación
    ├── 📁 sessions/                 # Archivos de sesión (si se usa file)
    └── 📁 uploads-temp/             # Uploads temporales antes de procesar
```

---

## Notas de Organización

| Carpeta | Propósito | Regla de oro |
|---------|-----------|--------------|
| `public/` | Única carpeta accesible desde el navegador | Todo código PHP sensible debe estar fuera |
| `src/Models/` | Solo consultas SQL y mapeo de datos | Sin lógica de negocio |
| `src/Controllers/` | Solo orquestación: recibe, procesa, responde | Sin SQL directo |
| `src/Services/` | Lógica de negocio compleja | Reutilizable entre controladores |
| `src/Views/` | Solo HTML + PHP echo | Sin lógica de negocio |
| `database/migrations/` | Inmutables después de aplicar en producción | Nunca modificar, solo crear nuevas |
| `database/seeds/` | Datos mínimos para que el sistema arranque | No incluir datos de producción |
| `logs/` | Solo archivos .log rotativos | Configurar rotación para no llenar disco |
| `docs/` | Documentación para AIT | Markdown legible sin código |

---

## Archivos Críticos de Configuración

| Archivo | Contenido | ¿Versionar? |
|---------|-----------|-------------|
| `.env` | Credenciales de BD, claves secretas | ❌ NO – Crear manualmente en cada servidor |
| `.env.example` | Plantilla con variables vacías | ✅ SÍ |
| `config/database.php` | Lee credenciales de `.env` | ✅ SÍ |
| `config/app.php` | Nombre de app, timezone, locale | ✅ SÍ |
| `config/session.php` | Tiempo de inactividad, cookie settings | ✅ SÍ |
| `composer.json` | Dependencias PHP | ✅ SÍ |
| `composer.lock` | Versiones exactas de dependencias | ✅ SÍ |
| `README.md` | Instrucciones de instalación para AIT | ✅ SÍ |

---

## Dependencias PHP Sugeridas (composer.json)

```json
{
  "require": {
    "php": ">=8.1",
    "ext-pdo": "*",
    "ext-pdo_pgsql": "*",
    "ext-json": "*",
    "ext-gd": "*",
    "ext-fileinfo": "*",
    "vlucas/phpdotenv": "^5.5",
    "dompdf/dompdf": "^2.0",
    "tecnickcom/tcpdf": "^6.6",
    "intervention/image": "^2.7"
  },
  "require-dev": {
    "phpunit/phpunit": "^10.0"
  }
}
```

---

*Estructura generada para el proyecto CMMS PDVSA Punta de Mata.*
*Stack: PHP 8.1+, PostgreSQL 14+, Apache/Nginx, Linux.*
