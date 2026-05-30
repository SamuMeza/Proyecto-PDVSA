# Especificación - Módulo de Equipos Admin

## Descripción

Este módulo permite la gestión de equipos de mantenimiento mediante un listado filtrable, formularios de creación y edición, y un manejo de estado inactivo que no elimina registros.

## Requisitos

- Debe existir una vista de listado de equipos con filtros por `familia`, `categoria`, `zona` y `estado`.
- Debe existir un formulario para crear y editar equipos con al menos los campos `nombre`, `familia`, `categoria`, `zona`, `estado` y `descripcion`.
- Al desactivar un equipo, el registro debe conservarse y mostrarse marcado como `inactivo`.
- Solo los roles `Admin`, `Supervisor` y `Programador` deben poder acceder al módulo.
- El permiso `permisos_json` debe controlar la visibilidad de las acciones `crear`, `editar` y `desactivar`.

## Criterios de aceptación

- Se muestra el listado de equipos con los filtros solicitados.
- Un equipo inactivo aparece en los listados con una etiqueta clara de `Inactivo`.
- El formulario guarda los datos del equipo y valida campos obligatorios.
- Un usuario sin rol permitido no puede acceder a la URL del módulo.
- Acciones restringidas quedan ocultas según `permisos_json`.

## Observaciones

- El módulo puede apoyarse en tablas de catálogo existentes para `familias`, `categorias` y `zonas`.
- No se elimina ningún equipo físicamente; solo cambia su atributo de estado.
