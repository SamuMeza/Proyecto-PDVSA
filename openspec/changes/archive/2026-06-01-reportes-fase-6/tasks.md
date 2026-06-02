## Tareas para reportes-fase-6

### Fase 1: Preparación (Infraestructura)

- [x] 1.1. Agregar dependencia `tecnickcom/tcpdf` al `composer.json` y ejecutar `composer install`
- [x] 1.2. Crear migration `029_alter_reportes_generados_add_status_fields.sql`:
  - Agregar campo `estado VARCHAR(20) DEFAULT 'completado'`
  - Agregar campo `tamano_bytes INT NULL`
  - Agregar campo `duracion_ms INT NULL`
  - Agregar campo `mensaje_error TEXT NULL`
  - Actualizar registros existentes con estado 'completado'
- [x] 1.3. Crear directorio `storage/reportes-pdf/` con `.htaccess` que bloquee acceso directo
- [x] 1.4. Crear directorios organizativos `storage/reportes-pdf/{año}/{mes}/`

### Fase 2: Backend - Servicios

- [x] 2.1. Crear `src/Services/PdfGeneratorService.php`:
  - Método `generarPdf(string $tipoReporte, array $filtros, array $datos): array`
  - Configurar TCPDF: CARTA, Portrait, márgenes 15mm/10mm, DejaVu Sans
  - Header con logo PDVSA + título del sistema
  - Footer con paginación + "Generado por [usuario]"
  - Tabla con datos del reporte
  - Retornar ['ruta' => string, 'nombre' => string, 'tamano' => int, 'duracion' => int]
- [x] 2.2. Crear `src/Services/ReporteGeneradorService.php`:
  - Método `contarRegistros(string $tipoReporte, array $filtros): int`
  - Método `obtenerRegistros(string $tipoReporte, array $filtros, int $limite = 5000): array`
  - Método `generarReporte(string $tipoReporte, array $filtros, int $usuarioId): array`
  - Método `obtenerHistorial(int $usuarioId, array $filtros): array`
  - Método `eliminarReporte(int $reporteId, int $usuarioId): bool`
  - Método `limpiarReportesAntiguos(): int` (limpieza de +90 días)
  - Método `construirQuery(string $tipoReporte, array $filtros): string`
- [x] 2.3. Crear `src/Services/CsvGeneratorService.php`:
  - Método `generarCsv(string $tipoReporte, array $filtros, array $datos): array`
  - Encoding UTF-8 con BOM
  - Separador punto y coma (;)
  - Primera fila: encabezados
  - Retornar ['ruta' => string, 'nombre' => string]

### Fase 3: Backend - Controllers y Rutas

- [x] 3.1. Crear `src/Controllers/ReporteGeneradorController.php`:
  - Método `index(array $params = []): void` → Muestra formulario de generación
  - Método `generar(array $params = []): void` → POST: valida, genera PDF/CSV, retorna URLs
  - Método `descargar(array $params = []): void` → GET: sirve archivo con auth check
  - Método `historial(array $params = []): void` → Muestra tabla de solicitudes
  - Método `eliminar(array $params = []): void` → POST: elimina registro + archivo
- [x] 3.2. Agregar rutas en `config/routes.php`:
  - `GET /reportes/generar` → ReporteGeneradorController::index
  - `POST /reportes/generar` → ReporteGeneradorController::generar (procesar)
  - `GET /reportes/descargar/{id}` → ReporteGeneradorController::descargar
  - `GET /reportes/historial` → ReporteGeneradorController::historial
  - `POST /reportes/eliminar/{id}` → ReporteGeneradorController::eliminar
- [x] 3.3. Verificar que las rutas no conflicten con las existentes de ReporteController

### Fase 4: Frontend - Vistas

- [x] 4.1. Crear `src/Views/reportes/generar.php`:
  - Select de tipo de reporte (Fallas, Cumplimiento, Resumen Mensual, Técnicos)
  - Contenedor de filtros que cambia según el tipo seleccionado
  - Indicador de "X registros encontrados"
  - Botón "Generar Reporte PDF"
  - Diseño amigable, sin mostrar JSON al usuario
- [x] 4.2. Crear `src/Views/reportes/historial.php`:
  - Tabla con columnas: #, Tipo, Fecha, Estado, Acciones
  - Filtros: tipo de reporte, rango de fechas, estado
  - Paginación
  - Acciones: Descargar PDF, Descargar CSV, Eliminar
  - Empty state: "No hay reportes generados"
- [x] 4.3. Crear `public/assets/js/reportes.js`:
  - Lógica de cambio de tipo de reporte → actualizar campos visibles
  - Generación de JSON interno con data-filter attributes
  - AJAX para contar registros al cambiar filtros
  - POST para generar reporte
  - Manejo de respuestas (éxito/error)

### Fase 5: Testing

- [x] 5.1. Tests Unitarios:
  - `ReporteGeneradorServiceTest`: Probar construirQuery() con cada tipo de reporte
  - `ReporteGeneradorServiceTest`: Probar que el JSON de filtros se genera correctamente
  - `PdfGeneratorServiceTest`: Probar generación de PDF con datos mock
  - `CsvGeneratorServiceTest`: Probar generación de CSV con encoding correcto
- [x] 5.2. Tests de Integración:
  - `ReporteGeneradorIntegrationTest`: Generar reporte completo (query + PDF + CSV + BD)
  - `ReporteGeneradorIntegrationTest`: Probar endpoint de descarga con auth
  - `ReporteGeneradorIntegrationTest`: Probar limpieza de reportes antiguos
- [x] 5.3. Tests Funcionales:
  - `ReporteFeatureTest`: Flujo completo login → generar → descargar
  - `ReporteFeatureTest`: Verificar que usuario sin permiso no puede generar
  - `ReporteFeatureTest`: Verificar manejo de 0 resultados
  - `ReporteFeatureTest`: Verificar historial y paginación

### Fase 6: Documentación

- [x] 6.1. Documentar endpoints en `docs/API.md`:
  - `POST /reportes/generar` - Parámetros, respuesta, errores
  - `GET /reportes/descargar/{id}` - Parámetros, headers, errores
  - `GET /reportes/historial` - Filtros, paginación
  - `POST /reportes/eliminar/{id}` - Parámetros
- [x] 6.2. Documentar plantillas de reporte en `docs/REPORTES.md`:
  - Tipos de reporte disponibles
  - Filtros por cada tipo
  - Estructura del PDF
  - Formato del CSV
- [x] 6.3. Actualizar `README.md` con instrucciones de configuración de TCPDF

### Fase 7: Integración y Pruebas

- [x] 7.1. Probar generación de PDF con datos reales de la BD
- [x] 7.2. Probar que los dashboards existentes no se afectaron
- [x] 7.3. Probar en diferentes navegadores (Chrome, Firefox, Edge)
- [x] 7.4. Probar con grandes volúmenes de datos (stress test)
- [x] 7.5. Verificar que la limpieza automática funciona correctamente
- [x] 7.6. Code review y refactorización final
