## Why

La gestión de órdenes preventivas requiere controles claros de planificación y validación, así como la capacidad de usar OTP para validar el trabajo realizado. El Planificador debe poder editar bloques existentes y crear nuevas OT/solicitudes de planificación sin que el calendario quede desordenado.

## What Changes

- **Base de órdenes preventivas**: Implementar la tabla `ordenes_preventivas` con `hora_inicio` y `hora_fin` para definir bloques de mantenimiento.
- **Flujo de estados**: `Planificada`, `En curso`, `Cerrada` y `Suspendida`.
- **Permisos**: Admin/Supervisor pueden crear y editar bloques en el calendario; el Planificador puede editar bloques existentes y generar nuevas OT/solicitudes; el Mantenedor solo ve.
- **OTP para validación**: La generación de OTP sirve para validar órdenes de trabajo cuando se lleva a cabo la intervención.
- **Validaciones con `permisos_json`**: Crear, editar, bloquear, cerrar y ver historial dependen de permisos declarados en JSON.

## Capabilities

### New Capabilities
- `ordenes-preventivas-otp`: Validación de órdenes mediante OTP para trabajos de mantenimiento.
- `ordenes-preventivas-flow`: Flujo de estados preventive OT con bloqueos temporales y validaciones.
- `ordenes-preventivas-permissions`: Control de acciones basado en `permisos_json`.

### Modified Capabilities
- `calendario-mantenimiento`: Integración de bloques de órdenes preventivas con el calendario.

## Impact

- **Base de datos**: Extensión de `ordenes_preventivas` y posible tabla de OTP de órdenes.
- **UI**: Formulario de OT preventiva y validación de código OTP en el proceso de ejecución.
- **Seguridad**: Las acciones sobre OT se validan con el JSON de permisos y el rol activo.
