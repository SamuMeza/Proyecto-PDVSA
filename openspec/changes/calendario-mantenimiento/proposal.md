## Why

El mantenimiento requiere una vista de calendario clara que permita a los roles responsables planificar y revisar bloques de trabajo por familia, con diferentes vistas semanales y mensuales. El calendario debe ser interactivo, permitir editar bloques y mostrar detalles por día en una lista completa.

## What Changes

- **Vistas semanal y mensual**: Dos modos separados de visualización del calendario.
- **Filtro por familia**: El calendario se divide por familias de equipos (`automatizacion`, `servicio_social_infraestructura`, etc.).
- **Colores configurables**: Los colores por familia se gestionan desde `configuracion_sistema`.
- **Lista completa por día**: La vista mensual muestra un resumen diario; al hacer clic en un día se abre la lista completa de bloques.
- **Interacción de roles**: Admin crea/edita/borra bloques, Supervisor edita bloques, Planificador edita bloques existentes y Mantenedor solo ve.
- **Familia obligatoria**: No se deben crear equipos sin familia asignada.

## Capabilities

### New Capabilities
- `calendario-semanal`: Vista semanal de bloques de mantenimiento.
- `calendario-mensual`: Vista mensual con resumen por día y acceso a lista completa.
- `calendario-familia-filtro`: Filtrado por familia de equipos y aplicación de colores.
- `calendario-colores-configurables`: Colores gestionables desde configuración del sistema.

### Modified Capabilities
- `modulo-equipos-admin`: Asociar equipos al calendario mediante familias y bloques de preventivas.

## Impact

- **Base de datos**: Dependencias con `categorias_equipo` y los campos de familia, además de los bloques de `ordenes_preventivas`.
- **UI**: Página de calendario con modos semanal/mensual, filtro por familia y listado de día.
- **Permisos**: Control de acceso de creación/edición/borrado según rol.
