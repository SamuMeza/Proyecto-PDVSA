## Context

El sistema requiere una base sólida para el control de acceso, la seguridad en el inicio de sesión y una interfaz visual cohesiva. Actualmente, no hay control granular de permisos (RBAC dinámico), no se soporta autenticación de doble factor (OTP), y el diseño de la interfaz de usuario es básico y no responsivo.

## Goals / Non-Goals

**Goals:**
- Implementar flujo de inicio de sesión de dos pasos: primero credenciales básicas, luego código OTP de 6 dígitos.
- Aplicar un límite de generación de OTP diario por usuario (máximo 150 intentos/solicitudes al día).
- Soportar roles dinámicos parseando el campo `permisos_json` de la tabla `roles` en formato `{"modulo": {"accion": true}}`.
- Crear una interfaz base premium responsiva que use variables CSS nativas, soporte temas claro/oscuro y muestre el logo corporativo.

**Non-Goals:**
- Implementar envío real de SMS/Email en esta fase (se simulará la salida en pantalla/logs del sistema o se proveerá un simulador en interfaz).
- Crear las pantallas y vistas de los módulos de mantenimiento (calendarios, órdenes) que corresponden a fases posteriores.

## Decisions

### 1. Mecanismo de Almacenamiento y Control de OTP
- **Opción seleccionada**: Crear una tabla auxiliar `usuario_otp` en la base de datos para almacenar el código actual, fecha de expiración, cantidad de intentos/generaciones diarias y la marca de tiempo de la última solicitud.
- **Razón**: Permite la persistencia del contador diario de OTP por usuario de manera independiente y limpia sin alterar la tabla principal `usuarios`. El límite diario de 150 se validará contra `intentos_hoy` y se reiniciará si el último intento corresponde a un día diferente (comprobación por fecha/hora).

### 2. Estructura de `permisos_json`
- **Opción seleccionada**: Estructura de árbol asociativo JSON: `{"modulo": {"accion": true}}` (por ejemplo: `{"calendario": {"crear": true, "ver": true}}`).
- **Razón**: Es altamente eficiente para validaciones rápidas en PHP usando isset/empty: `isset($permisos[$modulo][$accion]) && $permisos[$modulo][$accion] === true`.

### 3. Framework Frontend y Diseño Visual
- **Opción seleccionada**: Vanilla CSS con CSS Variables para la paleta de colores corporativos (basada en el logo PDVSA con rojo y gris/oscuro) y soporte de modo oscuro/claro de manera nativa.
- **Razón**: Evita la complejidad y dependencias adicionales de frameworks CSS en un entorno PHP clásico, garantizando el máximo rendimiento y control visual.

## Risks / Trade-offs

- **[Riesgo] Desincronización de zona horaria** → El servidor de base de datos y la aplicación PHP deben usar la misma zona horaria para validar correctamente la expiración del OTP y el reinicio diario del contador de intentos.
  - *Mitigación*: Forzar `date_default_timezone_set` en la inicialización de la app.
