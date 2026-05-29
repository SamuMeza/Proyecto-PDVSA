## Why

Los reportes finales deben registrarse desde el principio sin bloquear el resto de la operación correctiva. La primera versión documenta la petición de reporte y guarda los filtros usados, mientras deja la exportación PDF real para una etapa posterior.

## What Changes

- **Formulario de generación de reportes**: Página con filtros en formato JSON para definir qué se quiere consultar.
- **Registro de petición**: Guardar la solicitud en `reportes_generados` con filtros, usuario y timestamp.
- **Estado "En desarrollo"**: El botón "Generar reporte" muestra un mensaje de progreso/desarrollo si aún no hay exportación real.
- **Sin exportación PDF inicial**: La primera versión no genera PDF real; solo almacena la intención y permite seguimiento.

## Capabilities

### New Capabilities
- `reportes-generacion`: Interfaz para crear solicitudes de reporte desde filtros configurables.
- `reportes-filtros-json`: Guardado de los filtros de generación en JSON en la base de datos.
- `reportes-evento`: Registro del evento de generación para auditoría y seguimiento.

### Modified Capabilities
- `ordenes-correctivas-operacion`: Permite que las órdenes correctivas emitan reportes en desarrollo.

## Impact

- **Base de datos**: Uso de `tabla_reportes_generados.md` para registrar peticiones.
- **UI**: Página de reportes con formulario y mensaje de estado.
- **Usabilidad**: Evita bloquear el desarrollo de reportes PDF mientras se almacena la información necesaria.
