# Tabla: `logs_auditoria`

Libro de trazas – Registro de todas las acciones realizadas en el sistema.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGSERIAL | PK, NOT NULL | Identificador único |
| `usuario_id` | INTEGER | FK → usuarios.id, NULL | Usuario que realizó la acción (NULL si es sistema) |
| `accion` | VARCHAR(20) | NOT NULL | Tipo: INSERT / UPDATE / DELETE / LOGIN / LOGOUT / EXPORT / ASSIGN / UNLOCK |
| `tabla_afectada` | VARCHAR(50) | NOT NULL | Nombre de la tabla afectada |
| `registro_afectado_id` | INTEGER | NULL | ID del registro afectado |
| `datos_anteriores_json` | JSONB | NULL | Datos antes de la modificación |
| `datos_nuevos_json` | JSONB | NULL | Datos después de la modificación |
| `direccion_ip` | VARCHAR(45) | NULL | IP del cliente |
| `fecha_hora` | TIMESTAMP | DEFAULT NOW() | Fecha y hora exacta de la acción |
| `descripcion` | TEXT | NULL | Descripción legible de la acción |

---

**Relaciones:**
- Muchos a uno con `usuarios`

**Índices:**
- `idx_logs_usuario` (usuario_id)
- `idx_logs_accion` (accion)
- `idx_logs_tabla` (tabla_afectada)
- `idx_logs_fecha` (fecha_hora)
- `idx_logs_registro` (tabla_afectada, registro_afectado_id)
