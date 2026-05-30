# Especificación - Órdenes Correctivas de Operación

## Descripción

Implementa el módulo de órdenes correctivas con catálogos de fallas, prioridades, categorías de equipos y zonas, junto con auditoría y carga de fotografías.

## Requisitos

- Debe existir soporte para catálogos de `tipos_falla`, `prioridades_falla`, `categorias_equipo` y `zonas`.
- Debe permitir registrar órdenes correctivas con los campos `equipo_id`, `tipo_falla`, `prioridad`, `zona`, `descripcion` y `estado`.
- Debe permitir cargar hasta 3 imágenes JPG/PNG con compresión para cada orden.
- Debe registrar auditoría de creación, edición, cierre y cambios de estado.
- El permiso de rol determina acceso: Admin todo; Supervisor y Programador leen/actualizan; Mantenedor sólo lee.

## Criterios de aceptación

- Se puede crear una orden correctiva con datos de fallas, prioridad y ubicación.
- La carga de imágenes acepta JPG o PNG y comprime las imágenes para almacenamiento.
- Las acciones principales quedan registradas en un log de auditoría.
- Los roles pueden interactuar sólo con las operaciones permitidas por su perfil.
- El historial de la orden muestra los cambios de estado y ediciones.

## Observaciones

- El módulo debe integrarse con la operación diaria de mantenimiento y permitir seguimiento de fallas.
- Las imágenes deben estar accesibles en la vista de detalle sin mostrar archivos no autorizados.
