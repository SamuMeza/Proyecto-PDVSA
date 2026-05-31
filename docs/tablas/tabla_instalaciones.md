# Tabla: `instalaciones`

Jerarquía geográfica nivel 3 – Instalaciones dentro de un área.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `area_id` | INTEGER | FK → areas.id, NOT NULL | Área a la que pertenece |
| `nombre` | VARCHAR(100) | NOT NULL | Nombre de la instalación |
| `codigo_instalacion` | VARCHAR(50) | NULL | Código interno de la instalación |
| `estado` | VARCHAR(50) | DEFAULT 'activo' | Estado del registro |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |
| `actualizado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Relaciones:**
- Muchos a uno con `areas`
- Uno a muchos con `zonas`

**Índices:**
- `idx_instalaciones_area_id` (area_id)
- `idx_instalaciones_codigo` (codigo_instalacion)
