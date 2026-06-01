# Tabla: `areas`

Jerarquía geográfica nivel 2 – Áreas dentro de una localidad.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `localidad_id` | INTEGER | FK → localidades.id, NOT NULL | Localidad a la que pertenece |
| `nombre` | VARCHAR(100) | NOT NULL | Nombre del área |
| `descripcion` | TEXT | NULL | Descripción opcional del área |
| `estado` | VARCHAR(50) | DEFAULT 'activo' | Estado del registro |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |
| `actualizado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Relaciones:**
- Muchos a uno con `localidades`
- Uno a muchos con `instalaciones`

**Índices:**
- `idx_areas_localidad_id` (localidad_id)
- `idx_areas_nombre` (nombre)
