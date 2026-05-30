# Tabla: `checklists`

Checklists de tareas por nivel de mantenimiento.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `nivel_mantenimiento_id` | INTEGER | FK → niveles_mantenimiento.id, NOT NULL | Nivel al que pertenece |
| `nombre_checklist` | VARCHAR(200) | NOT NULL | Nombre descriptivo del checklist |
| `descripcion` | TEXT | NULL | Descripción general del checklist |
| `estado` | VARCHAR(50) | DEFAULT 'activo' | Estado del registro |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |
| `actualizado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Relaciones:**
- Muchos a uno con `niveles_mantenimiento`
- Uno a muchos con `checklist_items`

**Índices:**
- `idx_checklists_nivel` (nivel_mantenimiento_id)
