# Especificación - Calendario de Mantenimiento

## Descripción

Proporciona vistas de calendario semanal y mensual para visualizar y gestionar eventos de mantenimiento vinculados a órdenes preventivas y correctivas.

## Requisitos

- Debe incluir una vista semanal con bloques horarios por día.
- Debe incluir una vista mensual con resumen diario y la posibilidad de abrir la lista completa de eventos al hacer clic en un día.
- Debe permitir filtrar por `familia` de equipos.
- Los colores de familia deben ser configurables desde `configuracion_sistema`.
- El acceso debe cumplirse según roles: Admin puede crear/editar/borra, Supervisor y Planificador pueden editar, y Mantenedor puede ver.

## Criterios de aceptación

- El calendario semanal muestra eventos con horas de inicio y fin.
- El calendario mensual muestra una vista resumida por día y permite explorar eventos al hacer clic.
- Se puede filtrar el contenido por `familia` de equipo.
- Los colores se muestran con la configuración tomada de la tabla de configuración del sistema.
- Usuarios con permisos restringidos sólo ven el calendario sin opciones de edición cuando no correspondan.

## Observaciones

- El calendario debe ser una vista central para planificar mantenimientos y validar solapamientos.
- El módulo debe reflejar eventos creados en órdenes preventivas y, cuando aplique, órdenes correctivas.
