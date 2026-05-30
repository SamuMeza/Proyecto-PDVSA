# Tabla: `tipos_falla`

Clasificación de los tipos de falla que pueden ocurrir en los equipos.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `nombre` | VARCHAR(50) | NOT NULL, UNIQUE | Nombre del tipo de falla |
| `descripcion` | TEXT | NULL | Descripción del tipo de falla |
| `estado` | VARCHAR(50) | DEFAULT 'activo' | Estado del registro |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |

---

**Tipos confirmados:**
1. Software
2. Hardware
3. Red
4. Energía

**Relaciones:**
- Uno a muchos con `ordenes_correctivas`

**Índices:**
- `idx_tipos_falla_nombre` (nombre)
