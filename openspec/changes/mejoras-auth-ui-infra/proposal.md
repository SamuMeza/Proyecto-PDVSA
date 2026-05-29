## Why

Actualmente, el sistema carece de un flujo de autenticación seguro y moderno que incorpore verificación de dos factores (OTP), control granular de acceso mediante JSON de permisos, y una interfaz de usuario cohesiva que refleje la identidad de la marca. Esta propuesta establece las bases de seguridad, control de acceso y diseño visual premium (UI/UX) requeridas para las fases posteriores del sistema de mantenimiento.

## What Changes

- **Flujo de Autenticación Mejorado**: Renovación visual del inicio de sesión (Login) con soporte para logotipo dinámico y verificación de código OTP de un solo uso.
- **Seguridad en OTP**: Implementación de un límite de intentos diarios para la solicitud y validación de OTP, configurable mediante los parámetros del sistema, con bloqueo de cuenta temporal al alcanzar el límite.
- **Control de Acceso Basado en Permisos (RBAC)**: Procesamiento de permisos granular a través de `permisos_json` definido para cada rol en la base de datos, estructurado como `{"modulo": {"accion": true}}` o similar.
- **Plantilla Base y UI Consistente**: Creación de un layout responsive y moderno con un sidebar colapsable, cabecera y espacio de trabajo principal que aplique un estilo visual premium (colores corporativos, tipografía moderna, transiciones y animaciones suaves).

## Capabilities

### New Capabilities
- `secure-auth-otp`: Autenticación segura con verificación mediante código de un solo uso (OTP), validación de estado de usuario activo, límites configurables de generación de OTP diarios y redirecciones basadas en el rol.
- `role-based-permissions`: Control de acceso a nivel de aplicación mediante el análisis y mapeo del campo `permisos_json` asociado al rol de cada usuario para habilitar/deshabilitar acciones y vistas específicas.
- `ui-base-layout`: Layout base responsivo con diseño moderno y soporte para tematización visual premium, incluyendo sidebar interactivo, manejo de logotipos y alertas globales de sistema.

### Modified Capabilities
<!-- No existing capabilities to modify -->

## Impact

- **Base de Datos**: Consumo de datos desde `tabla_usuarios.md`, `tabla_roles.md`, y `tabla_configuracion_sistema.md` para validar credenciales, decodificar permisos y leer límites de OTP.
- **Código PHP**: Actualización del motor de autenticación en `auth/` e incorporación de sesiones PHP más seguras.
- **Interfaz y CSS**: Adición de hojas de estilo globales en `css/` y componentes visuales reutilizables.
