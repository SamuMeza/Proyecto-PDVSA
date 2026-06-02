## Why

El sistema necesita generar reportes PDF y CSV funcionales con filtros configurables. Los reportes actuales son dashboards en vivo (solo lectura), pero no permiten al usuario configurar filtros, generar un documento descargable ni mantener un historial de solicitudes. Esta funcionalidad es crítica para el análisis operativo y el cumplimiento de normativas de mantenimiento.

## What Changes

### Generación de Reportes
- **Formulario de filtros visual (Opción C)**: Interfaz amigable donde el usuario selecciona tipo de reporte y configura filtros específicos. Internamente genera un JSON que se envía al backend. El usuario nunca ve JSON.
- **Generación de PDF con TCPDF**: Cada reporte se genera como PDF funcional con logo PDVSA, filtros aplicados, tabla de datos y paginación.
- **Exportación CSV complementaria**: Además del PDF, se genera un CSV con todos los datos (para análisis en Excel). El PDF muestra resumen visual, el CSV contiene datos completos.
- **Historial de reportes**: Página con tabla de todas las solicitudes, filtros por tipo/fecha/estado, paginación y acciones (descargar/eliminar).

### Seguridad y Almacenamiento
- **Endpoint de descarga seguro**: `/reportes/descargar/{id}` con autenticación. Los archivos PDF NO son accesibles directamente por URL.
- **Almacenamiento fuera de public/**: Directorio `storage/reportes-pdf/` protegido con `.htaccess`.
- **Limpieza automática**: Al generar un nuevo reporte, se eliminan automáticamente los reportes con más de 90 días.

### Manejo de Errores
- **0 resultados**: Mensaje "No se encontraron resultados con los filtros seleccionados".
- **Error en query**: Log completo del error + mensaje amigable "Error al generar el reporte".
- **Error en TCPDF**: Log detallado + estado "error" en BD + limpieza de archivo parcial + mensaje amigable.
- **Proceso stuck**: Cleanup automático al generar nuevos reportes.

### Schema de BD
- **Migration 023 modificada**: Agregar campos `estado`, `tamano_bytes`, `duracion_ms`, `mensaje_error` a `reportes_generados`.

## Capabilities

### New Capabilities
- `reportes-generacion`: Formulario visual de filtros adaptable por tipo de reporte con JSON interno.
- `reportes-pdf-export`: Generación de PDF funcional con TCPDF (logo, filtros, tabla, paginación).
- `reportes-csv-export`: Generación de CSV complementario con encoding UTF-8 BOM para Excel.
- `reportes-historial`: Página de historial con tabla, filtros, paginación y acciones.
- `reportes-descarga-segura`: Endpoint protegido con auth para descarga de archivos.
- `reportes-limpieza`: Eliminación automática de reportes mayores a 90 días.
- `reportes-manejo-errores`: Manejo graceful de errores con logging y estados.

### Modified Capabilities
- `ordenes-correctivas-operacion`: Órdenes correctivas alimentan reportes de fallas y cumplimiento.
- `ordenes-preventivas-operacion`: Órdenes preventivas alimentan reportes de cumplimiento y resumen mensual.

## Impact

- **Base de datos**: Migration 023 modificada (campos adicionales en `reportes_generados`).
- **Composer**: Agregar dependencia `tecnickman/tcpdf` al `composer.json`.
- **Backend**: Nuevo controller `ReporteGeneradorController`, nuevo servicio `ReporteGeneradorService`, nuevo servicio `PdfGeneratorService`.
- **Frontend**: Nueva vista `reportes/generar.php`, nueva vista `reportes/historial.php`, nuevo archivo `reportes.js`.
- **Almacenamiento**: Nuevo directorio `storage/reportes-pdf/` con `.htaccess` de protección.
- **Seguridad**: Nuevo endpoint de descarga con verificación de permisos.
- **Testing**: Tests unitarios, de integración y funcionales para todo el módulo.
