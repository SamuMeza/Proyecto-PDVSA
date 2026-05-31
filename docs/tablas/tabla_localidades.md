# Tabla: `localidades`

Jerarquía geográfica nivel 1 – Localidades donde opera PDVSA.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `nombre` | VARCHAR(100) | NOT NULL, UNIQUE | Nombre de la localidad (ej: Punta de Mata, Maturín) |
| `estado` | VARCHAR(50) | DEFAULT 'activo' | Estado del registro: activo / inactivo |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |
| `actualizado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Relaciones:**
- Uno a muchos con `areas` (una localidad tiene muchas áreas)

**Índices:**
- `idx_localidades_nombre` (nombre)
