# Documentación de Reportes - Fase 6

## Tipos de Reporte

### 1. Fallas
**Descripción:** Reporte de órdenes correctivas con detalles de fallas.

**Filtros disponibles:**
- `tipo_falla_id`: ID del tipo de falla
- `zona_id`: ID de la zona
- `prioridad_id`: ID de la prioridad
- `estado`: Estado de la orden ('reportada', 'en_progreso', 'cerrada')
- `mantenedor_id`: ID del técnico asignado
- `fecha_desde`: Fecha de inicio (YYYY-MM-DD)
- `fecha_hasta`: Fecha de fin (YYYY-MM-DD)

**Campos del reporte:**
- Código de la orden
- Código del equipo
- Tipo de falla
- Prioridad
- Zona
- Técnico asignado
- Fecha de reporte
- Estado

### 2. Cumplimiento
**Descripción:** Reporte de órdenes preventivas y su estado de cumplimiento.

**Filtros disponibles:**
- `zona_id`: ID de la zona
- `nivel_mantenimiento_id`: ID del nivel de mantenimiento
- `estado`: Estado de la orden ('cerrada', 'en_curso', 'planificada', 'suspendida')
- `fecha_desde`: Fecha de inicio (YYYY-MM-DD)
- `fecha_hasta`: Fecha de fin (YYYY-MM-DD)

**Campos del reporte:**
- Código de la orden
- Código del equipo
- Nivel de mantenimiento
- Zona
- Fecha planificada
- Fecha de cierre
- Estado

### 3. Resumen Mensual
**Descripción:** Resumen mensual de órdenes preventivas con estadísticas.

**Filtros disponibles:**
- `mes_desde`: Mes de inicio (YYYY-MM)
- `mes_hasta`: Mes de fin (YYYY-MM)
- `zona_id`: ID de la zona
- `nivel_mantenimiento_id`: ID del nivel de mantenimiento

**Campos del reporte:**
- Mes
- Total de preventivas
- Completadas
- En curso
- Suspendidas
- Porcentaje de cumplimiento

### 4. Técnicos
**Descripción:** Rendimiento individual de los técnicos/mantenedores.

**Filtros disponibles:**
- `mantenedor_id`: ID del técnico
- `estado`: Estado de la orden ('reportada', 'en_progreso', 'cerrada')
- `zona_id`: ID de la zona
- `fecha_desde`: Fecha de inicio (YYYY-MM-DD)
- `fecha_hasta`: Fecha de fin (YYYY-MM-DD)

**Campos del reporte:**
- Nombre del técnico
- Total de órdenes asignadas
- Completadas
- Pendientes
- Porcentaje de cumplimiento

## Estructura del PDF

### Configuración
- **Tamaño de página:** CARTA (Letter) - 215.9mm × 279.4mm
- **Orientación:** Vertical (Portrait)
- **Márgenes:** 15mm izquierda, 10mm derecha, 15mm arriba/abajo
- **Fuente:** DejaVu Sans (soporta UTF-8, acentos, ñ)

### Contenido
1. **Header:**
   - Logo de PDVSA (30mm × 30mm)
   - Título del sistema
   - Subtítulo "Punta Mata"

2. **Información del reporte:**
   - Título del reporte
   - Fecha y hora de generación
   - Usuario que generó el reporte

3. **Filtros aplicados:**
   - Lista de filtros utilizados
   - Formato legible para el usuario

4. **Tabla de datos:**
   - Encabezados de columna
   - Filas con los datos del reporte
   - Paginación automática

5. **Footer:**
   - Número de página
   - Crédito: "Generado por [nombre de usuario]"

## Formato del CSV

### Configuración
- **Separador:** Punto y coma (;)
- **Encoding:** UTF-8 con BOM (Byte Order Mark)
- **Primera fila:** Encabezados de columna

### Nombre del archivo
`[tipo_reporte]_[fecha]_[hora].csv`

Ejemplo: `fallas_2026-06-01_143025.csv`

### Contenido
- Todos los registros (sin límite)
- Mismos campos que la tabla del PDF
- Datos crudos para análisis en Excel

## Almacenamiento

### Ubicación
`storage/reportes-pdf/{año}/{mes}/`

Ejemplo: `storage/reportes-pdf/2026/06/`

### Seguridad
- Directorio protegido con `.htaccess`
- Acceso solo mediante endpoint `/reportes/descargar/{id}`
- Verificación de autenticación y permisos

### Limpieza automática
- Los reportes con más de 90 días se eliminan automáticamente
- Se eliminan tanto los registros de BD como los archivos físicos

## Endpoint de Descarga

### GET /reportes/descargar/{id}?formato=pdf

**Headers de respuesta:**
- `Content-Type`: 'application/pdf' o 'text/csv; charset=utf-8'
- `Content-Disposition`: 'attachment; filename="nombre_archivo.ext"'
- `Content-Length`: Tamaño del archivo en bytes
- `Cache-Control`: 'no-cache, must-revalidate'

**Verificaciones:**
1. Autenticación del usuario
2. Permisos (admin/supervisor ven todos, técnico solo los suyos)
3. Existencia del archivo en disco
