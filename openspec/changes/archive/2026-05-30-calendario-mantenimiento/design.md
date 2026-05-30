## Diseño del calendario de mantenimiento

### Vistas

- **Semanal**: filas por día, bloques por hora, vista de detalle de cada OT.
- **Mensual**: resumen diario con indicador de carga. Al hacer clic en un día se muestra la lista completa de bloques del día.

### Filtros y colores

- Filtro por `familia` de equipos.
- `familia` se asigna desde `categorias_equipo` y es obligatoria para cada equipo.
- Los colores del calendario se configuran desde `configuracion_sistema` y se aplican por familia.

### Roles y acciones

- Admin: crear/editar/borrar bloques.
- Supervisor: editar bloques.
- Planificador: editar bloques existentes.
- Mantenedor: solo ver.

### Comportamiento

- Un equipo sin familia no debe poder crearse.
- El calendario utiliza los datos de `ordenes_preventivas` y `equipos`.
- En la vista mensual, el clic en un día abre la lista completa de bloques programados.
