# Tabla: `roles`

Definición de roles y permisos del sistema.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `nombre` | VARCHAR(50) | NOT NULL, UNIQUE | Nombre del rol |
| `permisos_json` | JSONB | DEFAULT '{}' | Permisos granularizados en formato JSON |
| `descripcion` | TEXT | NULL | Descripción del rol y sus funciones |
| `estado` | VARCHAR(50) | DEFAULT 'activo' | Estado del registro |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |
| `actualizado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Roles confirmados:**
1. Administrador – Gestión total del sistema
2. Planificador/Programador – Configura frecuencias, genera calendario
3. Supervisor – Asigna, audita, desbloquea, ve reportes
4. Mantenedor – Ejecuta, cierra, reporta fallas

**Relaciones:**
- Uno a muchos con `usuarios`

**Índices:**
- `idx_roles_nombre` (nombre)
