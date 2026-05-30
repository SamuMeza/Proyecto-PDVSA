## Why

El sistema requiere una base de autenticación sólida y una administración de usuarios controlada para operar de forma segura. Esta propuesta detalla el flujo de login y registro, la administración de sesiones, el control de permisos y un layout consistente con tema claro/oscuro en todo el sistema.

## What Changes

- **Registro de usuarios restringido**: Solo el rol `Administrador` puede crear cuentas nuevas. `auth/register.php` y el enlace de registro estarán accesibles únicamente para administradores autenticados.
- **Email interno automático**: El sistema genera internamente el correo como `nombre_usuario@pdvsa.com` y lo guarda en la base de datos. El formulario de registro no solicita un dominio externo.
- **Teléfono con prefijo obligatorio**: El campo de teléfono se guarda en un solo campo y exige el prefijo `+58`.
- **Sesiones inactivas por rol**: Los tiempos son Admin 10 min, Supervisor 20 min y Otros 35 min. Cualquier actividad (mouse/tecla/clic) renueva el contador, el modal aparece 2 min antes y si se ignora el sistema cierra sesión de inmediato.
- **Tema global dark/light**: El modo oscuro/claro debe aplicarse a todo el sistema, incluyendo login, sidebar, mensajes de error, formularios y tablas.
- **Layout premium**: Sidebar colapsable en la izquierda, logotipo PDVSA en login y menú, y navegación moderna con interacciones suaves.
- **Permisos basados en JSON**: Se usa `permisos_json` para habilitar acciones por módulo, con una estructura como `{"equipos": {"ver": true, "editar": false}, "calendario": {"crear": true}}`.

## Capabilities

### New Capabilities
- `auth-admin-registration`: Registro de usuarios accesible únicamente a Administradores.
- `internal-email-generation`: Generación y almacenamiento de correo interno `nombre_usuario@pdvsa.com`.
- `phone-58-format`: Validación y almacenamiento de teléfono con prefijo `+58`.
- `idle-session-per-role`: Sesiones con expiración por rol, modal previo y cierre inmediato si se ignora.
- `ui-theme-global`: Tema claro/oscuro aplicado en todo el sistema.
- `role-based-permissions`: Control granular de acciones basado en `permisos_json`.

### Modified Capabilities
<!-- No existing capabilities to modify -->

## Impact

- **Base de Datos**: Ajustes en `tabla_usuarios.md` para almacenar `email` interno y `telefono_extension` con formato `+58`.
- **Código PHP**: Restricción de registro para Administrador y lógica de sesión con renovaciones por actividad.
- **Interfaz y CSS**: Tema global, modal de cierre de sesión y sidebar responsive.
- **Seguridad**: Se reduce la superficie de creación de usuarios y se documenta el flujo de sesiones inactivas.
