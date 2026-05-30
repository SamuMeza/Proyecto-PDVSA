## Estructura del módulo de equipos

El módulo de equipos se apoya en la tabla `equipos` y en las tablas de catálogo relacionadas: `categorias_equipo`, `zonas` y `familias`.

### Página principal

- Listado de equipos con columnas clave: nombre, familia, categoría, zona, estado.
- Filtros: familia, categoría, zona, estado.
- Botón de crear equipo disponible solo para roles autorizados.
- Equipos inactivos se distinguen visualmente con etiqueta o fondo diferenciado.

### Formulario de equipo

Campos principales:
- Nombre del equipo
- Familia (requerido)
- Categoría
- Zona
- Estado (activo/inactivo)
- Descripción corta

### Permisos y navegación

- Admin/Supervisor/Programador pueden acceder al módulo.
- Solo Admin puede desactivar equipos (`estado = 'inactivo'`).
- El resto de los roles solo visualiza y edita según permisos.

### Integración con permisos

El sistema usa `permisos_json` para decidir si un rol puede:
- ver el módulo de equipos
- editar un equipo
- cambiar el estado a inactivo
- aplicar filtros avanzados

### Resultado esperado

Pagina de equipos funcional con filtros claros, edición de registros y soporte para equipos inactivos.
