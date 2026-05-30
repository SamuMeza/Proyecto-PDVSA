# Tabla: `ejecucion_checklist_items`

Tareas de checklist marcadas como completadas durante una ejecución preventiva.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `ejecucion_preventiva_id` | INTEGER | FK → ejecuciones_preventivas.id, NOT NULL | Ejecución a la que pertenece |
| `checklist_item_id` | INTEGER | FK → checklist_items.id, NOT NULL | Tarea del checklist |
| `marcado_como_hecho` | BOOLEAN | DEFAULT FALSE | Si el técnico marcó la tarea |
| `observacion_item` | TEXT | NULL | Observación específica de la tarea |
| `fecha_marcado` | TIMESTAMP | NULL | Fecha/hora en que se marcó |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |
| `actualizado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Relaciones:**
- Muchos a uno con `ejecuciones_preventivas`
- Muchos a uno con `checklist_items`

**Índices:**
- `idx_ejecucion_items_ejecucion` (ejecucion_preventiva_id)
- `idx_ejecucion_items_checklist` (checklist_item_id)
