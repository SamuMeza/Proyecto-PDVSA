# Esquema de Base de Datos

## Diagrama de Relaciones

- `localidades` 1:N `areas` 1:N `instalaciones` 1:N `zonas`
- `categorias_equipo` 1:N `niveles_mantenimiento`
- `niveles_mantenimiento` 1:N `checklists` 1:N `checklist_items`
- `zonas` 1:N `equipos`
- `equipos` 1:N `ordenes_preventivas`
- `equipos` 1:N `ordenes_correctivas`
- `equipos` 1:N `calibraciones`
- `roles` 1:N `usuarios`
- `usuarios` 1:N `ordenes_preventivas` (planificador, supervisor, mantenedor)
- `usuarios` 1:N `ordenes_correctivas` (reportado, supervisor, mantenedor)

## Migraciones (001-024)

Ejecutar en orden numérico ascendente.

## Seeds (001-008)

Ejecutar después de las migraciones en orden numérico.
