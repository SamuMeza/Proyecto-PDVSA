# Tabla: `grupos_seguridad`

Agrupación de equipos por sistemas de seguridad industrial.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `nombre` | VARCHAR(100) | NOT NULL, UNIQUE | Nombre del grupo |
| `descripcion` | TEXT | NULL | Descripción del grupo |
| `estado` | VARCHAR(50) | DEFAULT 'activo' | Estado del registro |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |
| `actualizado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Grupos confirmados:**
1. SIS (Sistema Interventoría de Seguridad)
2. CSC (Centro de Supervisión y Control)
3. Control Distribuido

**Relaciones:**
- Uno a muchos con `equipos`

**Índices:**
- `idx_grupos_nombre` (nombre)
