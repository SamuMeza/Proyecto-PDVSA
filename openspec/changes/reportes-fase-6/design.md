## Diseño del módulo de reportes fase 6

### Modelo de datos

El módulo se apoya en la tabla `reportes_generados` que guarda:
- usuario que solicita el reporte
- filtros en JSON
- fecha y hora
- estado de la petición

### Interfaz

- Página de reportes con formulario de filtros.
- Botón “Generar reporte” que guarda la petición.
- Mensaje visible: “En desarrollo” si aún no se genera PDF real.

### Flujo

1. El usuario define filtros.
2. El sistema guarda la petición con los filtros JSON.
3. El sistema muestra un estado de petición registrada.
4. El PDF real se implementará en una fase futura.

### Impacto

- No bloquea operaciones de órdenes correctivas.
- Permite comenzar con reportes sin implementar exportación PDF.
- Facilita que luego se agregue el módulo `reportes-pdf-export`.
