# Especificación - Órdenes Preventivas Base

## Descripción

Define la estructura y el flujo base de órdenes preventivas, incluyendo estados, horarios, OTP de validación y conexión con el calendario de mantenimiento.

## Requisitos

- Debe existir una tabla o entidad de órdenes preventivas con `hora_inicio`, `hora_fin`, `estado`, `equipo_id`, `responsable_id` y `descripcion`.
- Debe soportar estados: `Planificada`, `En curso`, `Cerrada` y `Suspendida`.
- Debe requerir OTP para validar el inicio o cierre de una orden de trabajo.
- Debe poder integrarse con el calendario de mantenimiento para mostrar horarios y limitaciones de disponibilidad.
- El permiso `permisos_json` debe controlar la creación, edición, bloqueo, cierre y consulta de historial.

## Criterios de aceptación

- Se puede crear una orden preventivo con horario y estado inicial `Planificada`.
- El cambio de estado a `En curso` o `Cerrada` puede requerir OTP según el flujo definido.
- El historial de la orden queda disponible para consulta.
- El registro de la orden aparece en el calendario con el bloque horario correcto.
- Un usuario sin permiso apropiado no puede ejecutar cambios de estado o ver el historial.

## Observaciones

- El OTP puede enviarse como código temporal al usuario responsable y validarse antes de confirmar la ejecución.
- El flujo debe ser compatible con la lógica de mantenimiento preventivo y no confligir con fechas/hora de otras órdenes.
