# Tabla: `zonas`

Jerarquía geográfica nivel 4 – Zonas dentro de una instalación.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `instalacion_id` | INTEGER | FK → instalaciones.id, NOT NULL | Instalación a la que pertenece |
| `nombre` | VARCHAR(100) | NOT NULL | Nombre de la zona |
| `codigo_zona` | VARCHAR(50) | NULL | Código interno de la zona |
| `estado` | VARCHAR(50) | DEFAULT 'activo' | Estado del registro |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |
| `actualizado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Relaciones:**
- Muchos a uno con `instalaciones`
- Uno a muchos con `equipos`

**Índices:**
- `idx_zonas_instalacion_id` (instalacion_id)
- `idx_zonas_codigo` (codigo_zona)
