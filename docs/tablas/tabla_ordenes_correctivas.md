# Tabla: `ordenes_correctivas`

Órdenes de trabajo de mantenimiento correctivo (fallas imprevistas).

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `codigo_unico` | VARCHAR(50) | NOT NULL, UNIQUE | Código único (ej: OTC-2026-0001) |
| `equipo_id` | INTEGER | FK → equipos.id, NOT NULL | Equipo afectado |
| `tipo_falla_id` | INTEGER | FK → tipos_falla.id, NOT NULL | Tipo de falla |
| `prioridad_id` | INTEGER | FK → prioridades_falla.id, NOT NULL | Prioridad de la falla |
| `fecha_reporte` | DATE | NOT NULL | Fecha en que se reportó |
| `hora_reporte` | TIME | NOT NULL | Hora en que se reportó |
| `reportado_por_usuario_id` | INTEGER | FK → usuarios.id, NOT NULL | Usuario que reportó |
| `fecha_inicio_reparacion` | DATE | NULL | Fecha de inicio de reparación |
| `hora_inicio_reparacion` | TIME | NULL | Hora de inicio de reparación |
| `fecha_fin_reparacion` | DATE | NULL | Fecha de fin de reparación |
| `hora_fin_reparacion` | TIME | NULL | Hora de fin de reparación |
| `descripcion_falla` | TEXT | NOT NULL | Descripción de la falla |
| `acciones_tomadas` | TEXT | NULL | Acciones realizadas para reparar |
| `causa_raiz` | TEXT | NULL | Causa raíz (OBLIGATORIO para cerrar) |
| `repuestos_utilizados` | TEXT | NULL | Repuestos o materiales utilizados (texto libre) |
| `downtime_calculado_minutos` | INTEGER | NULL | Downtime calculado automáticamente |
| `estado` | VARCHAR(50) | DEFAULT 'reportada' | Estado de la orden |
| `supervisor_asigno_id` | INTEGER | FK → usuarios.id, NULL | Supervisor que asignó |
| `mantenedor_id` | INTEGER | FK → usuarios.id, NULL | Técnico asignado |
| `cerrada_por_usuario_id` | INTEGER | FK → usuarios.id, NULL | Usuario que cerró la orden |
| `fecha_cierre` | TIMESTAMP | NULL | Fecha/hora de cierre oficial |
| `es_preventivo_fuera_de_plan` | BOOLEAN | DEFAULT FALSE | Si es un preventivo que se hizo como correctivo |
| `es_reporte_condicion` | BOOLEAN | DEFAULT FALSE | Si es un Reporte de Condición |
| `condicion_observada` | TEXT | NULL | Descripción de la condición (solo para Reporte de Condición) |
| `recomendacion_condicion` | TEXT | NULL | Recomendación (solo para Reporte de Condición) |
| `orden_preventiva_relacionada_id` | INTEGER | FK → ordenes_preventivas.id, NULL | OTP relacionada (si aplica) |
| `orden_correctiva_relacionada_id` | INTEGER | FK → ordenes_correctivas.id, NULL | OTC relacionada (si aplica) |
| `creada_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |
| `actualizada_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Estados posibles:**
- `reportada` – Falla reportada, pendiente de asignación
- `asignada` – Asignada a un mantenedor
- `en_reparacion` – El mantenedor está reparando
- `reparada` – Reparación completada, pendiente de cierre
- `cerrada` – Cerrada oficialmente con causa raíz
- `cancelada` – Cancelada por decisión del supervisor

**Relaciones:**
- Muchos a uno con `equipos`
- Muchos a uno con `tipos_falla`
- Muchos a uno con `prioridades_falla`
- Muchos a uno con `usuarios` (reportado_por, supervisor, mantenedor, cerrada_por)
- Uno a muchos con `fotos_correctivas`

**Índices:**
- `idx_otc_codigo` (codigo_unico)
- `idx_otc_equipo` (equipo_id)
- `idx_otc_estado` (estado)
- `idx_otc_fecha_reporte` (fecha_reporte)
- `idx_otc_mantenedor` (mantenedor_id)
- `idx_otc_es_condicion` (es_reporte_condicion)
