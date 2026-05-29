# Tabla: `calibraciones`

Registro de calibraciones para equipos de instrumentación (gas y fuego).

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `equipo_id` | INTEGER | FK → equipos.id, NOT NULL | Equipo calibrado |
| `tipo_calibracion` | VARCHAR(50) | NOT NULL | Tipo: Anual, Semestral, Programada |
| `fecha_ultima_calibracion` | DATE | NOT NULL | Fecha de última calibración realizada |
| `fecha_proxima_calibracion` | DATE | NOT NULL | Fecha calculada de próxima calibración |
| `entidad_certificadora` | VARCHAR(200) | NULL | Empresa o entidad que certificó |
| `certificado_ruta` | VARCHAR(255) | NULL | Ruta al archivo digital del certificado |
| `rango_medicion` | VARCHAR(100) | NULL | Rango de medición calibrado |
| `error_permitido` | VARCHAR(50) | NULL | Error o tolerancia permitida |
| `observaciones` | TEXT | NULL | Observaciones de la calibración |
| `registrado_por_usuario_id` | INTEGER | FK → usuarios.id, NOT NULL | Usuario que registró |
| `estado` | VARCHAR(50) | DEFAULT 'al_dia' | Estado: al_dia / proxima / vencida |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |
| `actualizado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Nota:** Solo aplica a equipos de la categoría "Instrumentación Especializada" relacionados con gas y fuego.

**Relaciones:**
- Muchos a uno con `equipos`
- Muchos a uno con `usuarios`

**Índices:**
- `idx_calibraciones_equipo` (equipo_id)
- `idx_calibraciones_fecha_proxima` (fecha_proxima_calibracion)
- `idx_calibraciones_estado` (estado)
