# Tabla: `prioridades_falla`

Niveles de prioridad para las fallas reportadas.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `nombre` | VARCHAR(50) | NOT NULL, UNIQUE | Nombre de la prioridad |
| `color_alert` | VARCHAR(7) | NOT NULL | Color HEX para UI (badge, alerta) |
| `descripcion` | TEXT | NULL | Descripción de la prioridad |
| `estado` | VARCHAR(50) | DEFAULT 'activo' | Estado del registro |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |

---

**Prioridades confirmadas:**
1. Baja → color: `#90A4AE` (gris)
2. Media → color: `#F4A460` (naranja pastel)
3. Alta → color: `#CD5C5C` (rojo pastel)

**Relaciones:**
- Uno a muchos con `ordenes_correctivas`

**Índices:**
- `idx_prioridades_nombre` (nombre)
