# Tabla: `checklist_items`

Tareas individuales dentro de un checklist.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `checklist_id` | INTEGER | FK → checklists.id, NOT NULL | Checklist al que pertenece |
| `orden_ejecucion` | INTEGER | NOT NULL | Número de orden secuencial (1, 2, 3...) |
| `descripcion_tarea` | TEXT | NOT NULL | Descripción de la tarea a realizar |
| `es_obligatorio` | BOOLEAN | DEFAULT TRUE | Si la tarea es obligatoria o opcional |
| `estado` | VARCHAR(50) | DEFAULT 'activo' | Estado del registro |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |

---

**Relaciones:**
- Muchos a uno con `checklists`
- Uno a muchos con `ejecucion_checklist_items`

**Índices:**
- `idx_checklist_items_checklist` (checklist_id)
- `idx_checklist_items_orden` (orden_ejecucion)
