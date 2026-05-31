# Tabla: `reportes_generados`

Registro de reportes exportados por los usuarios.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `tipo_reporte` | VARCHAR(50) | NOT NULL | Tipo: cumplimiento_mensual / cumplimiento_anual / estadisticas_fallas / rendimiento_tecnico / fallas_detalladas |
| `formato` | VARCHAR(10) | DEFAULT 'pdf' | Formato de exportación: pdf |
| `generado_por_usuario_id` | INTEGER | FK → usuarios.id, NOT NULL | Usuario que generó el reporte |
| `fecha_generacion` | TIMESTAMP | DEFAULT NOW() | Fecha de generación |
| `parametros_filtros_json` | JSONB | NULL | Parámetros aplicados (rango de fechas, categorías, etc.) |
| `ruta_archivo` | VARCHAR(255) | NOT NULL | Ruta donde se guardó el archivo PDF |
| `nombre_archivo_descarga` | VARCHAR(255) | NOT NULL | Nombre sugerido para la descarga |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |

---

**Relaciones:**
- Muchos a uno con `usuarios`

**Índices:**
- `idx_reportes_usuario` (generado_por_usuario_id)
- `idx_reportes_tipo` (tipo_reporte)
- `idx_reportes_fecha` (fecha_generacion)
