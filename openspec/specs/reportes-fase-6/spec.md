# Especificación - Reportes Fase 6

## Descripción

Sistema completo de generación de reportes PDF y CSV con filtros configurables, historial de solicitudes, manejo seguro de archivos y limpieza automática. Los reportes actuales (dashboards en vivo) se mantienen intactos; esta fase agrega la capacidad de generar documentos descargables.

## Requisitos Funcionales

### RF-001: Formulario de Generación de Reportes
- Debe existir una página `/reportes/generar` con formulario de filtros.
- El formulario debe mostrar un select de tipo de reporte con opciones: Fallas, Cumplimiento, Resumen Mensual, Técnicos.
- Al seleccionar un tipo, los campos de filtro deben cambiar dinámicamente.
- Los filtros por tipo son:
  - **Fallas**: Tipo de falla, Zona, Prioridad, Estado, Técnico, Fecha desde/hasta
  - **Cumplimiento**: Tipo (Preventivas/Correctivas/Ambas), Estado, Zona, Nivel de Mantenimiento, Fecha desde/hasta
  - **Resumen Mensual**: Mes desde/hasta, Zona, Nivel de Mantenimiento
  - **Técnicos**: Técnico, Estado, Zona, Fecha desde/hasta
- Al cambiar cualquier filtro, debe mostrar cuántos registros se encontraron.
- Debe tener un botón "Generar Reporte PDF".
- La interfaz debe ser 100% visual; el usuario nunca debe ver JSON.

### RF-002: Generación de PDF
- El sistema debe generar un PDF funcional usando TCPDF.
- El PDF debe contener:
  - Header con logo de PDVSA y título del sistema
  - Título del reporte
  - Fecha y hora de generación
  - Usuario que generó el reporte
  - Filtros aplicados (legibles)
  - Tabla con los datos del reporte
  - Paginación
  - Footer con "Generado por [nombre de usuario]"
- Configuración: Tamaño CARTA, orientación vertical, márgenes 15mm izq / 10mm der, fuente DejaVu Sans.
- El archivo debe guardarse en `storage/reportes-pdf/{año}/{mes}/`.

### RF-003: Generación de CSV
- Además del PDF, se debe generar un CSV complementario.
- Formato: separador punto y coma (;), encoding UTF-8 con BOM, primera fila con encabezados.
- El CSV contiene todos los registros (sin límite de 5,000 como el PDF).
- El archivo debe guardarse junto al PDF.

### RF-004: Registro en Base de Datos
- Cada generación debe crear un registro en `reportes_generados` con:
  - tipo_reporte, formato, generado_por_usuario_id
  - parametros_filtros_json (JSON con los filtros aplicados)
  - ruta_archivo, nombre_archivo_descarga
  - estado ('generando', 'completado', 'error')
  - tamano_bytes, duracion_ms (cuando complete)
  - mensaje_error (cuando falle)

### RF-005: Endpoint de Descarga Segura
- Debe existir `GET /reportes/descargar/{id}` con:
  - Verificación de autenticación
  - Verificación de permisos (admin/supervisor ven todos, técnico solo los suyos)
  - Headers correctos: Content-Type, Content-Disposition, Content-Length
  - La ruta real del archivo nunca debe exponerse al usuario

### RF-006: Historial de Reportes
- Debe existir una página `/reportes/historial` con:
  - Tabla con: #, Tipo de Reporte, Fecha, Estado, Acciones
  - Filtros: tipo de reporte, rango de fechas, estado
  - Paginación (20 por página)
  - Acciones: Descargar PDF, Descargar CSV, Eliminar
  - Empty state: "No hay reportes generados"

### RF-007: Manejo de Errores
- Si la query retorna 0 registros: mostrar "No se encontraron resultados con los filtros seleccionados".
- Si hay error en la query: log completo del error + mostrar "Error al generar el reporte. Intente de nuevo."
- Si TCPDF falla: log detallado + actualizar estado a 'error' + guardar mensaje_error + limpiar archivo parcial + mostrar mensaje amigable.
- El usuario NUNCA debe ver errores técnicos o de código.

### RF-008: Limpieza Automática
- Al generar un nuevo reporte, el sistema debe eliminar automáticamente los reportes con más de 90 días.
- La limpieza debe borrar el registro de BD y el archivo físico del disco.

### RF-009: Seguridad
- Solo usuarios con permiso `reportes:ver` pueden acceder.
- Admin y Supervisor pueden ver reportes de todos los usuarios.
- Los archivos PDF/CSV no son accesibles directamente por URL.
- El directorio `storage/` debe estar protegido con `.htaccess`.

## Requisitos No Funcionales

### RNF-001: Rendimiento
- La generación de PDF no debe tomar más de 30 segundos para reportes normales (< 5,000 registros).
- El conteo de registros al cambiar filtros debe responder en menos de 2 segundos.
- El endpoint de descarga debe servir archivos en menos de 5 segundos.

### RNF-002: Extensibilidad
- Para agregar un nuevo tipo de reporte, se debe poder hacer modificando:
  - Un caso en el switch de ReporteGeneradorService::construirQuery()
  - Los campos del formulario en generar.php
  - El template del PDF en PdfGeneratorService
- No se deben modificar controllers, rutas ni estructura general.

### RNF-003: Compatibilidad
- Los dashboards existentes (cumplimiento, fallas, resumen-mensual, tecnicos) NO deben modificarse.
- Las rutas existentes de ReporteController NO deben cambiar.
- El sistema debe funcionar en XAMPP con PHP 8.1+.

### RNF-004: Testing
- Cobertura mínima de tests: 80% para servicios.
- Tests unitarios para lógica de filtros y generación.
- Tests de integración para flujo completo.
- Tests funcionales para escenarios de usuario.

## Criterios de Aceptación

1. El usuario puede seleccionar un tipo de reporte y ver filtros específicos para ese tipo.
2. Al cambiar filtros, se muestra la cantidad de registros encontrados.
3. Al hacer clic en "Generar", se crea un PDF con los datos filtrados y se descarga.
4. Se genera automáticamente un CSV complementario con todos los datos.
5. El historial muestra todos los reportes generados con su estado.
6. El usuario puede descargar PDFs y CSVs desde el historial.
7. El usuario puede eliminar reportes del historial.
8. Los reportes de más de 90 días se eliminan automáticamente.
9. Los archivos no son accesibles directamente por URL.
10. Los dashboards existentes funcionan exactamente igual que antes.
11. Los errores se muestran como mensajes amigables, nunca como errores técnicos.
12. Los tests pasan exitosamente.

## Observaciones

- Los reportes actuales (dashboards) son visualización en vivo; los nuevos reportes son documentos descargables.
- El PDF muestra resumen visual (tablas formateadas); el CSV contiene datos crudos para análisis.
- La limpieza automática evita acumulación de archivos sin necesidad de cron job.
- La arquitectura de filtros (Opción C) permite agregar nuevos filtros sin modificar el backend.
